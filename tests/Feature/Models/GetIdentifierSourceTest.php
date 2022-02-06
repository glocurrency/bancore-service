<?php

namespace GloCurrency\Bancore\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Bank;
use GloCurrency\Bancore\Events\TransactionCreatedEvent;
use BrokeYourBike\Bancore\Interfaces\IdentifierSourceInterface;

class GetIdentifierSourceTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_processing_item(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $identifierSource = Bank::factory()->create();

        $transaction = Transaction::factory()->create([
            'identifier_source_type' => Bank::class,
            'identifier_source_id' => $identifierSource->id,
        ]);

        $this->assertSame($identifierSource->id, $transaction->fresh()->identifierSource->id);
        $this->assertInstanceOf(IdentifierSourceInterface::class, $transaction->identifierSource);
    }
}
