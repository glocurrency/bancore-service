<?php

namespace GloCurrency\Bancore\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Quota;
use GloCurrency\Bancore\Events\TransactionCreatedEvent;

class GetQuotaTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_quota(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $quota = Quota::factory()->create();

        $transaction = Transaction::factory()->create([
            'bancore_quota_id' => $quota->id,
        ]);

        $this->assertSame($quota->id, $transaction->fresh()->quota->id);
    }
}
