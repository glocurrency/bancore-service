<?php

namespace GloCurrency\Bancore\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Recipient;
use GloCurrency\Bancore\Events\TransactionCreatedEvent;

class GetRecipientTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_recipient(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $recipient = Recipient::factory()->create();

        $transaction = Transaction::factory()->create([
            'bancore_recipient_id' => $recipient->id,
        ]);

        $this->assertSame($recipient->id, $transaction->fresh()->recipient->id);
    }
}
