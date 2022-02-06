<?php

namespace GloCurrency\Bancore\Enums;

use GloCurrency\MiddlewareBlocks\Enums\IdentificationTypeEnum as MIdentificationTypeEnum;
use BrokeYourBike\Bancore\Enums\IdentificationTypeEnum;

class IdentificationTypeFactory
{
    public static function makeFrom(MIdentificationTypeEnum $identificationType): ?IdentificationTypeEnum
    {
        return match ($identificationType) {
            MIdentificationTypeEnum::PASSPORT => IdentificationTypeEnum::PASSPORT,
            MIdentificationTypeEnum::DRIVING_LICENSE => IdentificationTypeEnum::DRIVING_LICENSE,
            MIdentificationTypeEnum::NATIONAL_ID => IdentificationTypeEnum::NATIONAL_ID,
            default => null,
        };
    }
}
