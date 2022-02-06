<?php

namespace GloCurrency\Bancore\Tests\Unit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Enums\TransactionStateCodeEnum;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\BaseModels\BaseUuid;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Transaction::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Transaction::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_implemets_source_model_interface(): void
    {
        $this->assertInstanceOf(SourceModelInterface::class, new Transaction());
    }

    /** @test */
    public function it_returns_receive_amount_as_float(): void
    {
        $transaction = new Transaction();
        $transaction->receive_amount = '1.02';

        $this->assertSame(1.02, $transaction->receive_amount);
    }

    /** @test */
    public function it_returns_state_code_as_enum(): void
    {
        $transaction = new Transaction();
        $transaction->setRawAttributes([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED->value,
        ]);

        $this->assertEquals(TransactionStateCodeEnum::LOCAL_UNPROCESSED, $transaction->state_code);
    }

    /** @test */
    public function it_returns_error_code_as_enum(): void
    {
        $transaction = new Transaction();
        $transaction->setRawAttributes([
            'error_code' => ErrorCodeEnum::AUTH_FAILED->value,
        ]);

        $this->assertEquals(ErrorCodeEnum::AUTH_FAILED, $transaction->error_code);
    }
}
