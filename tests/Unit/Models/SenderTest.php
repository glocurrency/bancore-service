<?php

namespace GloCurrency\Bancore\Tests\Unit\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\Bancore\Models\Sender;
use BrokeYourBike\BaseModels\BaseUuid;
use BrokeYourBike\Bancore\Enums\IdentificationTypeEnum;
use BrokeYourBike\Bancore\Enums\GenderTypeEnum;

class SenderTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Sender::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Sender::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_returns_gender_as_enum(): void
    {
        $sender = new Sender();
        $sender->setRawAttributes([
            'gender' => GenderTypeEnum::MALE->value,
        ]);

        $this->assertEquals(GenderTypeEnum::MALE, $sender->gender);
    }

    /** @test */
    public function it_returns_identification_type_as_enum(): void
    {
        $sender = new Sender();
        $sender->setRawAttributes([
            'identification_type' => IdentificationTypeEnum::PASSPORT->value,
        ]);

        $this->assertEquals(IdentificationTypeEnum::PASSPORT, $sender->identification_type);
    }

    /** @test */
    public function it_can_return_name(): void
    {
        $sender = new Sender();
        $sender->first_name = 'John';
        $sender->last_name = 'Doe';

        $this->assertSame('John Doe', $sender->name);
    }

    /** @test */
    public function it_returns_birth_date_as_carbon(): void
    {
        $sender = new Sender();
        $sender->birth_date = '2020-01-01';

        $this->assertInstanceOf(Carbon::class, $sender->birth_date);
    }

    /** @test */
    public function it_can_return_country_code_alpha2()
    {
        $sender = new Sender();
        $sender->country_code = 'USA';

        $this->assertSame('US', $sender->country_code_alpha2);
    }
}
