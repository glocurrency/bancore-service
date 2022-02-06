<?php

namespace GloCurrency\Bancore\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use GloCurrency\Bancore\Tests\Fixtures\TransactionFixture;
use GloCurrency\Bancore\Tests\Fixtures\ProcessingItemFixture;
use GloCurrency\Bancore\Tests\Fixtures\BankFixture;
use GloCurrency\Bancore\BancoreServiceProvider;
use GloCurrency\Bancore\Bancore;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        Bancore::useTransactionModel(TransactionFixture::class);
        Bancore::useProcessingItemModel(ProcessingItemFixture::class);
        Bancore::useBankModel(BankFixture::class);
    }

    protected function getPackageProviders($app)
    {
        return [BancoreServiceProvider::class];
    }

    /**
     * Create the HTTP mock for API.
     *
     * @return array<\GuzzleHttp\Handler\MockHandler|\GuzzleHttp\HandlerStack> [$httpMock, $handlerStack]
     */
    protected function mockApiFor(string $class): array
    {
        $httpMock = new \GuzzleHttp\Handler\MockHandler();
        $handlerStack = \GuzzleHttp\HandlerStack::create($httpMock);

        $this->app->when($class)
            ->needs(\GuzzleHttp\ClientInterface::class)
            ->give(function () use ($handlerStack) {
                return new \GuzzleHttp\Client(['handler' => $handlerStack]);
            });

        return [$httpMock, $handlerStack];
    }
}
