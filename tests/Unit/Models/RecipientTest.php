<?php

namespace GloCurrency\Bancore\Tests\Unit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\Bancore\Models\Recipient;
use BrokeYourBike\BaseModels\BaseUuid;

class RecipientTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Recipient::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Recipient::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_can_return_name(): void
    {
        $recipient = new Recipient();
        $recipient->first_name = 'John';
        $recipient->last_name = 'Doe';

        $this->assertSame('John Doe', $recipient->name);
    }

    /** @test */
    public function it_can_return_country_code_alpha2()
    {
        $recipient = new Recipient();
        $recipient->country_code = 'USA';

        $this->assertSame('US', $recipient->country_code_alpha2);
    }
}
