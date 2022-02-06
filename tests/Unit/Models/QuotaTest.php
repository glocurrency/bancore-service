<?php

namespace GloCurrency\Bancore\Tests\Unit\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\Bancore\Models\Quota;
use BrokeYourBike\BaseModels\BaseUuid;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;

class QuotaTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Quota::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Quota::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_returns_send_amount_as_float(): void
    {
        $quota = new Quota();
        $quota->send_amount = '1.02';

        $this->assertSame(1.02, $quota->send_amount);
    }

    /** @test */
    public function it_returns_receive_amount_as_float(): void
    {
        $quota = new Quota();
        $quota->receive_amount = '1.02';

        $this->assertSame(1.02, $quota->receive_amount);
    }

    /** @test */
    public function it_returns_expires_at_as_carbon_object(): void
    {
        $quota = new Quota();
        $quota->expires_at = '2020-01-01';

        $this->assertInstanceOf(Carbon::class, $quota->expires_at);
        $this->assertEquals(Carbon::parse('2020-01-01'), $quota->expires_at);
    }

    /** @test */
    public function it_returns_error_code_as_enum(): void
    {
        $quota = new Quota();
        $quota->setRawAttributes([
            'error_code' => ErrorCodeEnum::AUTH_FAILED->value,
        ]);

        $this->assertEquals(ErrorCodeEnum::AUTH_FAILED, $quota->error_code);
    }
}
