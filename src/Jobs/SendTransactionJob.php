<?php

namespace GloCurrency\Bancore\Jobs;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Bus\Queueable;
use GloCurrency\MiddlewareBlocks\Enums\QueueTypeEnum as MQueueTypeEnum;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Quota;
use GloCurrency\Bancore\Exceptions\SendTransactionException;
use GloCurrency\Bancore\Enums\TransactionStateCodeEnum;
use GloCurrency\Bancore\Enums\ErrorCodeFactory;
use Carbon\Exceptions\InvalidFormatException;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;
use BrokeYourBike\Bancore\Client;

class SendTransactionJob implements ShouldQueue, ShouldBeUnique, ShouldBeEncrypted
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    private Transaction $targetTransaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $targetTransaction)
    {
        $this->targetTransaction = $targetTransaction;
        $this->afterCommit();
        $this->onQueue(MQueueTypeEnum::SERVICES->value);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->targetTransaction->id;
    }

    /**
     * Generate new random session ID
     *
     * @return string
     */
    private function prepareSessionId()
    {
        return str_replace('-', '', (string) Uuid::uuid4());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (TransactionStateCodeEnum::LOCAL_UNPROCESSED !== $this->targetTransaction->state_code) {
            throw SendTransactionException::stateNotAllowed($this->targetTransaction);
        }

        if ($this->validateTransaction($this->targetTransaction) === false) {
            $this->releaseJob();
            return;
        }

        $quota = $this->getQuota($this->targetTransaction);

        if (is_bool($quota)) {
            $this->releaseJob();
            return;
        }

        $quota->save();

        $this->targetTransaction->bancore_quota_id = $quota->id;

        if ($this->targetTransaction->receive_amount !== $quota->receive_amount) {
            throw SendTransactionException::receiveAmountMismatch($this->targetTransaction, $quota);
        }

        try {
            /** @var Client */
            $api = App::make(Client::class);
            $response = $api->sendBankTransaction($this->targetTransaction);
        } catch (\Throwable $e) {
            report($e);
            throw SendTransactionException::apiRequestException($e);
        }

        $errorCode = ErrorCodeEnum::tryFrom($response->responseCode);

        if (!$errorCode) {
            throw SendTransactionException::unexpectedErrorCode($response->responseCode);
        }

        $this->targetTransaction->error_code = $errorCode;
        $this->targetTransaction->state_code = ErrorCodeFactory::getTransactionStateCode($errorCode);
        $this->targetTransaction->error_code_description = $response->responseDescription;
        $this->targetTransaction->save();
    }

    private function releaseJob(): void
    {
        $className = $this->targetTransaction::class;
        Log::debug(__CLASS__ . " with {$className} `{$this->targetTransaction->id}` released back {$this->attempts()}/{$this->tries}");
        $this->release(300 * $this->attempts());
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        report($exception);

        if ($exception instanceof SendTransactionException) {
            $this->targetTransaction->update([
                'state_code' => $exception->getStateCode(),
                'state_code_reason' => $exception->getStateCodeReason(),
            ]);
            return;
        }

        $this->targetTransaction->update([
            'state_code' => TransactionStateCodeEnum::LOCAL_EXCEPTION,
            'state_code_reason' => $exception->getMessage(),
        ]);
    }

    /**
     * Perform validation of the recipient related information.
     *
     * @param Transaction $targetTransaction
     * @return bool
     *
     * @throws SendTransactionException
     */
    private function validateTransaction(Transaction $targetTransaction): bool
    {
        try {
            /** @var Client */
            $api = App::make(Client::class);
            $response = $api->validateBankTransaction($this->prepareSessionId(), $targetTransaction);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            report($e);

            $handlerContext = $e->getHandlerContext();
            if (isset($handlerContext['errno']) && $handlerContext['errno'] === CURLE_OPERATION_TIMEOUTED) {
                return false;
            }

            throw SendTransactionException::apiRequestException($e);
        } catch (\Throwable $e) {
            report($e);
            throw SendTransactionException::apiRequestException($e);
        }

        $errorCode = ErrorCodeEnum::tryFrom($response->responseCode);

        if (!$errorCode) {
            throw SendTransactionException::unexpectedErrorCode($response->responseCode);
        }

        if (ErrorCodeEnum::SUCCESS !== $errorCode) {
            throw SendTransactionException::transactionValidationFailed($targetTransaction, $response->getRawResponse());
        }

        return true;
    }

    /**
     * Perform quotation of the transaction.
     *
     * @param Transaction $targetTransaction
     * @return Quota|bool
     *
     * @throws SendTransactionException
     */
    private function getQuota(Transaction $targetTransaction): Quota|bool
    {
        try {
            /** @var Client */
            $api = App::make(Client::class);
            $response = $api->quoteBankTransaction($this->prepareSessionId(), $targetTransaction);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            report($e);

            $handlerContext = $e->getHandlerContext();
            if (isset($handlerContext['errno']) && $handlerContext['errno'] === CURLE_OPERATION_TIMEOUTED) {
                return false;
            }

            throw SendTransactionException::apiRequestException($e);
        } catch (\Throwable $e) {
            report($e);
            throw SendTransactionException::apiRequestException($e);
        }

        $errorCode = ErrorCodeEnum::tryFrom($response->responseCode);

        if (!$errorCode) {
            throw SendTransactionException::unexpectedErrorCode($response->responseCode);
        }

        if (ErrorCodeEnum::SUCCESS !== $errorCode) {
            throw SendTransactionException::transactionQuotationFailed($targetTransaction, $response->getRawResponse());
        }

        if (!$response->quoteId) {
            throw SendTransactionException::invalidQuoteReference($response->getRawResponse());
        }

        if (!$response->beneficiaryAmount) {
            throw SendTransactionException::invalidRecipientAmount($response->getRawResponse());
        }

        $quota = new Quota([
            'bancore_transaction_id' => $targetTransaction->id,
            'error_code' => $errorCode,
            'reference' => $response->quoteId,
            'send_currency_code' => $targetTransaction->receive_currency_code,
            'receive_currency_code' => $targetTransaction->receive_currency_code,
            'send_amount' => $targetTransaction->receive_amount,
            'receive_amount' => floatval($response->beneficiaryAmount),
            'error_code_description' => $response->responseDescription,
            'rate' => $response->fxRate ? floatval($response->fxRate) : null,
        ]);

        if ($response->quoteExpiryTime) {
            try {
                $quota->expires_at = Carbon::parse($response->quoteExpiryTime);
            } catch (InvalidFormatException $th) {
                report($th);
            }
        }

        return $quota;
    }
}
