<?php

namespace Tests\Unit\Enums\Bancore;

use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\MiddlewareBlocks\Enums\GenderTypeEnum as MGenderTypeEnum;
use GloCurrency\Bancore\Enums\GenderTypeFactory;
use BrokeYourBike\Bancore\Enums\GenderTypeEnum;

class GenderTypeFactoryTest extends TestCase
{
    /** @test */
    public function it_can_return_self_from_all_base_gender_types()
    {
        foreach (MGenderTypeEnum::cases() as $value) {
            $this->assertInstanceOf(GenderTypeEnum::class, GenderTypeFactory::makeFrom($value));
        }
    }
}
