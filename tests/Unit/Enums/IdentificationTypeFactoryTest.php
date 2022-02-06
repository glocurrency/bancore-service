<?php

namespace Tests\Unit\Enums\Bancore;

use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\MiddlewareBlocks\Enums\IdentificationTypeEnum as MIdentificationTypeEnum;
use GloCurrency\Bancore\Enums\IdentificationTypeFactory;
use BrokeYourBike\Bancore\Enums\IdentificationTypeEnum;

class IdentificationTypeFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider idTypeProvider
     * */
    public function it_can_return_enum_from(MIdentificationTypeEnum $sourceIdType, bool $shouldReturn)
    {
        $idType = IdentificationTypeFactory::makeFrom($sourceIdType);

        if ($shouldReturn) {
            $this->assertInstanceOf(IdentificationTypeEnum::class, $idType);
        } else {
            $this->assertNull($idType);
        }
    }

    public function idTypeProvider(): array
    {
        $data = collect(MIdentificationTypeEnum::cases())
            ->filter(fn($c) => !in_array($c, [
                MIdentificationTypeEnum::PASSPORT,
                MIdentificationTypeEnum::DRIVING_LICENSE,
                MIdentificationTypeEnum::NATIONAL_ID,
            ]))
            ->flatten()
            ->map(fn($c) => [$c, false])
            ->toArray();

        $data[] = [MIdentificationTypeEnum::PASSPORT, true];
        $data[] = [MIdentificationTypeEnum::DRIVING_LICENSE, true];
        $data[] = [MIdentificationTypeEnum::NATIONAL_ID, true];

        return $data;
    }
}
