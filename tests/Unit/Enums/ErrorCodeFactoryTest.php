<?php

namespace GloCurrency\Bancore\Tests\Unit\Enums;

use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\Bancore\Enums\TransactionStateCodeEnum;
use GloCurrency\Bancore\Enums\ErrorCodeFactory;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;

class ErrorCodeFactoryTest extends TestCase
{
    /** @test */
    public function it_can_return_transaction_state_code_from_all_values()
    {
        foreach (ErrorCodeEnum::cases() as $value) {
            $this->assertInstanceOf(TransactionStateCodeEnum::class, ErrorCodeFactory::getTransactionStateCode($value));
        }
    }
}
