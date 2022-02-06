<?php

namespace GloCurrency\Bancore\Enums;

use GloCurrency\MiddlewareBlocks\Enums\GenderTypeEnum as MGenderTypeEnum;
use BrokeYourBike\Bancore\Enums\GenderTypeEnum;

class GenderTypeFactory
{
    public static function makeFrom(MGenderTypeEnum $genderType): GenderTypeEnum
    {
        return match ($genderType) {
            MGenderTypeEnum::MALE => GenderTypeEnum::MALE,
            MGenderTypeEnum::FEMALE => GenderTypeEnum::FEMALE,
        };
    }
}
