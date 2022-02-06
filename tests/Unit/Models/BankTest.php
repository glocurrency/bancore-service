<?php

namespace GloCurrency\Bancore\Tests\Unit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\Bancore\Models\Bank;
use BrokeYourBike\BaseModels\BaseUuid;

class BankTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Bank::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Bank::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }
}
