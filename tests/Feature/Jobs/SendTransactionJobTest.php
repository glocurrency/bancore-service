<?php

namespace GloCurrency\Bancore\Tests\Feature\Jobs;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\JobProcessed;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Quota;
use GloCurrency\Bancore\Jobs\SendTransactionJob;
use GloCurrency\Bancore\Exceptions\SendTransactionException;
use GloCurrency\Bancore\Events\TransactionUpdatedEvent;
use GloCurrency\Bancore\Events\TransactionCreatedEvent;
use GloCurrency\Bancore\Enums\TransactionStateCodeEnum;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;
use BrokeYourBike\Bancore\Client;

class SendTransactionJobTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([
            TransactionCreatedEvent::class,
            TransactionUpdatedEvent::class,
        ]);
    }

    private function makeAuthResponse(): \GuzzleHttp\Psr7\Response
    {
        return new \GuzzleHttp\Psr7\Response(200, [], '{
            "uuid": "ed0d0d9c-8591-4b24-b738-6e511f50da8a",
            "username": "user@example.com",
            "firstName": "JOHN",
            "lastName": "DOE",
            "email": "user@example.com",
            "token": "123456789",
            "expiresIn": 86400,
            "lastLoggedIn": "2022-01-03 04:43:32",
            "createdAt": "2021-04-14 11:00:00"
        }');
    }

    private function makeValidationResponse(ErrorCodeEnum $responseCode): \GuzzleHttp\Psr7\Response
    {
        return new \GuzzleHttp\Psr7\Response(200, [], '{
            "responseCode": "'. $responseCode->value .'",
            "responseDescription": ""
        }');
    }

    private function makeQuotationResponse(ErrorCodeEnum $responseCode, string $quoteId, string $beneficiaryAmount): \GuzzleHttp\Psr7\Response
    {
        return new \GuzzleHttp\Psr7\Response(200, [], '{
            "responseCode": "'. $responseCode->value .'",
            "responseDescription": "",
            "quoteId": "'. $quoteId .'",
            "beneficiaryAmount": "'. $beneficiaryAmount .'"
        }');
    }

    private function makeTransactionResponse(ErrorCodeEnum $responseCode, string $description = ''): \GuzzleHttp\Psr7\Response
    {
        return new \GuzzleHttp\Psr7\Response(200, [], '{
            "responseCode": "'. $responseCode->value .'",
            "responseDescription": "'. $description .'"
        }');
    }

    /**
     * @test
     * @dataProvider transactionStatesProvider
     */
    public function it_will_throw_if_state_not_LOCAL_UNPROCESSED(TransactionStateCodeEnum $stateCode, bool $shouldFail): void
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => $stateCode,
            'receive_amount' => 100.0,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeValidationResponse(ErrorCodeEnum::SUCCESS));
        $httpMock->append($this->makeQuotationResponse(ErrorCodeEnum::SUCCESS, 'LOL123', '100'));
        $httpMock->append($this->makeTransactionResponse(ErrorCodeEnum::SUCCESS));

        if ($shouldFail) {
            $this->expectExceptionMessage("Transaction state_code `{$targetTransaction->state_code->value}` not allowed");
            $this->expectException(SendTransactionException::class);
        }

        SendTransactionJob::dispatchSync($targetTransaction);

        if (!$shouldFail) {
            $this->assertEquals(TransactionStateCodeEnum::PAID, $targetTransaction->fresh()->state_code);
        }
    }

    public function transactionStatesProvider(): array
    {
        $states = collect(TransactionStateCodeEnum::cases())
            ->filter(fn($c) => !in_array($c, [
                TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            ]))
            ->flatten()
            ->map(fn($c) => [$c, true])
            ->toArray();

        $states[] = [TransactionStateCodeEnum::LOCAL_UNPROCESSED, false];

        return $states;
    }

    /** @test */
    public function it_will_throw_if_quoted_amount_dont_match_with_transaction_amount()
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'receive_amount' => 100.01,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeValidationResponse(ErrorCodeEnum::SUCCESS));
        $httpMock->append($this->makeQuotationResponse(ErrorCodeEnum::SUCCESS, 'LOL123', '200.01'));

        $this->assertNull(Quota::first());

        try {
            SendTransactionJob::dispatchSync($targetTransaction);
        } catch (\Throwable $th) {
            $this->assertStringContainsString("`{$targetTransaction->id}` receive_amount 100.01", $th->getMessage());
            $this->assertStringContainsString("receive_amount 200.01", $th->getMessage());
            $this->assertInstanceOf(SendTransactionException::class, $th);
            return;
        }

        $this->fail('Exception was not thrown');
    }

    /** @test */
    public function it_will_throw_if_error_code_is_unexpected(): void
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'receive_amount' => 100.0,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeValidationResponse(ErrorCodeEnum::SUCCESS));
        $httpMock->append($this->makeQuotationResponse(ErrorCodeEnum::SUCCESS, 'LOL123', '100.00'));
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "responseCode": "not a code you can expect",
            "responseDescription": "KEK"
        }'));

        try {
            SendTransactionJob::dispatchSync($targetTransaction);
        } catch (\Throwable $th) {
            $this->assertEquals('Unexpected ' . ErrorCodeEnum::class . ': `not a code you can expect`', $th->getMessage());
            $this->assertInstanceOf(SendTransactionException::class, $th);
        }

        /** @var Transaction */
        $targetTransaction = $targetTransaction->fresh();

        $this->assertEquals(TransactionStateCodeEnum::UNEXPECTED_ERROR_CODE, $targetTransaction->state_code);
    }

    /** @test */
    public function it_can_send_transaction(): void
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'receive_amount' => $this->faker->randomFloat(2, 1),
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeValidationResponse(ErrorCodeEnum::SUCCESS));
        $httpMock->append($this->makeQuotationResponse(ErrorCodeEnum::SUCCESS, 'LOL123', (string) $targetTransaction->receive_amount));
        $httpMock->append($this->makeTransactionResponse(ErrorCodeEnum::SUCCESS, 'Successful operation'));

        $this->assertNull(Quota::first());

        SendTransactionJob::dispatchSync($targetTransaction);

        /** @var Transaction */
        $targetTransaction = $targetTransaction->fresh();

        /** @var Quota $quota */
        $this->assertNotNull($quota = Quota::first());
        $this->assertSame('LOL123', $quota->reference);
        $this->assertSame($targetTransaction->receive_amount, $quota->send_amount);
        $this->assertSame($targetTransaction->receive_amount, $quota->receive_amount);
        $this->assertSame($targetTransaction->receive_currency_code, $quota->send_currency_code);
        $this->assertSame($targetTransaction->receive_currency_code, $quota->receive_currency_code);

        $this->assertEquals(TransactionStateCodeEnum::PAID, $targetTransaction->state_code);
        $this->assertEquals(ErrorCodeEnum::SUCCESS, $targetTransaction->error_code);
        $this->assertSame('Successful operation', $targetTransaction->error_code_description);
        $this->assertSame($quota->id, $targetTransaction->bancore_quota_id);
        // TODO: make more assetions
    }

    /** @test */
    public function it_will_release_job_if_validation_timeouted()
    {
        Log::spy();

        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append(new \GuzzleHttp\Exception\ConnectException(
            'Timeout',
            new \GuzzleHttp\Psr7\Request('GET', 'validation'),
            null,
            ['errno' => CURLE_OPERATION_TIMEOUTED]
        )); // validation

        Queue::after(function (JobProcessed $event) {
            $this->assertTrue($event->job->isReleased());
            $this->assertSame(1, $event->job->attempts());
            $this->assertSame(3, $event->job->maxTries());
        });

        $logMessage = SendTransactionJob::class . " with " . $targetTransaction::class . " `{$targetTransaction->id}` released back 1/3";

        Log::shouldReceive('debug')
            ->with($logMessage)
            ->once();

        SendTransactionJob::dispatchSync($targetTransaction);
    }

    /** @test */
    public function it_will_release_job_if_quotation_timeouted()
    {
        Log::spy();

        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeValidationResponse(ErrorCodeEnum::SUCCESS));
        $httpMock->append(new \GuzzleHttp\Exception\ConnectException(
            'Timeout',
            new \GuzzleHttp\Psr7\Request('GET', 'quotation'),
            null,
            ['errno' => CURLE_OPERATION_TIMEOUTED]
        )); // quotation

        Queue::after(function (JobProcessed $event) {
            $this->assertTrue($event->job->isReleased());
            $this->assertSame(1, $event->job->attempts());
            $this->assertSame(3, $event->job->maxTries());
        });

        $logMessage = SendTransactionJob::class . " with " . $targetTransaction::class . " `{$targetTransaction->id}` released back 1/3";

        Log::shouldReceive('debug')
            ->with($logMessage)
            ->once();

        SendTransactionJob::dispatchSync($targetTransaction);
    }

    /** @test */
    public function it_will_not_release_job_if_payout_timeouted()
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'receive_amount' => $this->faker->randomFloat(2, 1),
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeValidationResponse(ErrorCodeEnum::SUCCESS));
        $httpMock->append($this->makeQuotationResponse(ErrorCodeEnum::SUCCESS, 'LOL123', (string) $targetTransaction->receive_amount));
        $httpMock->append(new \GuzzleHttp\Exception\ConnectException(
            'Timeout',
            new \GuzzleHttp\Psr7\Request('GET', 'payout'),
            null,
            ['errno' => CURLE_OPERATION_TIMEOUTED]
        )); // payout

        $this->assertNull(Quota::first());

        try {
            $job = SendTransactionJob::dispatchSync($targetTransaction);
        } catch (\Throwable $th) {
            $this->assertStringContainsString('Timeout', $th->getMessage());
            $this->assertInstanceOf(SendTransactionException::class, $th);
            return;
        }

        $this->fail('Exception was not thrown.');
    }
}
