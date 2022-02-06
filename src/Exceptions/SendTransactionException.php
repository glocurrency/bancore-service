<?php

namespace GloCurrency\Bancore\Exceptions;

use Psr\Http\Message\ResponseInterface;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Quota;
use GloCurrency\Bancore\Enums\TransactionStateCodeEnum;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;
use BrokeYourBike\Bancore\Client;

final class SendTransactionException extends \RuntimeException
{
    private TransactionStateCodeEnum $stateCode;
    private string $stateCodeReason;

    public function __construct(TransactionStateCodeEnum $stateCode, string $stateCodeReason, ?\Throwable $previous = null)
    {
        $this->stateCode = $stateCode;
        $this->stateCodeReason = $stateCodeReason;

        parent::__construct($stateCodeReason, 0, $previous);
    }

    public function getStateCode(): TransactionStateCodeEnum
    {
        return $this->stateCode;
    }

    public function getStateCodeReason(): string
    {
        return $this->stateCodeReason;
    }

    public static function stateNotAllowed(Transaction $transaction): self
    {
        $className = $transaction::class;
        $message = "{$className} state_code `{$transaction->state_code->value}` not allowed";
        return new static(TransactionStateCodeEnum::STATE_NOT_ALLOWED, $message);
    }

    public static function apiRequestException(\Throwable $e): self
    {
        $className = Client::class;
        $message = "Exception during {$className} request with message: `{$e->getMessage()}`";
        return new static(TransactionStateCodeEnum::API_REQUEST_EXCEPTION, $message);
    }

    public static function invalidQuoteReference(ResponseInterface $response): self
    {
        $className = $response::class;
        $message = "{$className} body `{$response->getBody()}` `quoteId` invalid";
        return new static(TransactionStateCodeEnum::RESULT_JSON_INVALID, $message);
    }

    public static function invalidRecipientAmount(ResponseInterface $response): self
    {
        $className = $response::class;
        $message = "{$className} body `{$response->getBody()}` `beneficiaryAmount` invalid";
        return new static(TransactionStateCodeEnum::RESULT_JSON_INVALID, $message);
    }

    public static function unexpectedErrorCode(string $code): self
    {
        $className = ErrorCodeEnum::class;
        $message = "Unexpected {$className}: `{$code}`";
        return new static(TransactionStateCodeEnum::UNEXPECTED_ERROR_CODE, $message);
    }

    public static function transactionValidationFailed(Transaction $transaction, ResponseInterface $response): self
    {
        $transactionClassName = $transaction::class;
        $message = "API validation of {$transactionClassName} `{$transaction->id}` failed with response `{$response->getBody()}`";
        return new static(TransactionStateCodeEnum::UNEXPECTED_ERROR_CODE, $message);
    }

    public static function transactionQuotationFailed(Transaction $transaction, ResponseInterface $response): self
    {
        $transactionClassName = $transaction::class;
        $message = "API quotation of {$transactionClassName} `{$transaction->id}` failed with response `{$response->getBody()}`";
        return new static(TransactionStateCodeEnum::UNEXPECTED_ERROR_CODE, $message);
    }

    public static function receiveAmountMismatch(Transaction $transaction, Quota $quota): self
    {
        $transactionClassName = $transaction::class;
        $quotaClassName = $quota::class;

        $message = "{$transactionClassName} `{$transaction->id}` receive_amount {$transaction->receive_amount}";
        $message .= " do not match with {$quotaClassName} `{$quota->id}` receive_amount {$quota->receive_amount}";
        return new static(TransactionStateCodeEnum::UNEXPECTED_ERROR_CODE, $message);
    }
}
