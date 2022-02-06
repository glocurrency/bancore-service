<?php

namespace Tests\Feature\Models\Bancore\Quota;

use Illuminate\Support\Facades\Event;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Quota;

class GetTransactionTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_transaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $transaction = Transaction::factory()->create();

        $quota = Quota::factory()->create([
            'bancore_transaction_id' => $transaction->id,
        ]);

        $this->assertSame($transaction->id, $quota->fresh()->transaction->id);
    }
}
