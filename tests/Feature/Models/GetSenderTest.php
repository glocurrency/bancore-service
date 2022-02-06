<?php

namespace GloCurrency\Bancore\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Sender;

class GetSenderTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_sender(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $sender = Sender::factory()->create();

        $transaction = Transaction::factory()->create([
            'bancore_sender_id' => $sender->id,
        ]);

        $this->assertSame($sender->id, $transaction->fresh()->sender->id);
    }
}
