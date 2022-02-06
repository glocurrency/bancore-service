<?php

namespace GloCurrency\Bancore\Tests\Feature\Helpers;

use Illuminate\Support\Carbon;
use GloCurrency\MiddlewareBlocks\Enums\IdentificationTypeEnum as MIdentificationTypeEnum;
use GloCurrency\MiddlewareBlocks\Enums\GenderTypeEnum as MGenderTypeEnum;
use GloCurrency\MiddlewareBlocks\Contracts\SenderInterface as MSenderInterface;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Sender;
use GloCurrency\Bancore\Helpers\SenderFactory;
use BrokeYourBike\Bancore\Enums\IdentificationTypeEnum;
use BrokeYourBike\Bancore\Enums\GenderTypeEnum;

class MakeSenderTest extends FeatureTestCase
{
    /** @test */
    public function it_can_make_sender(): void
    {
        $sender = $this->getMockBuilder(MSenderInterface::class)->getMock();
        $sender->method('getFirstName')->willReturn($this->faker->firstName());
        $sender->method('getLastName')->willReturn($this->faker->lastName());
        $sender->method('getBirthDate')->willReturn(Carbon::now());
        $sender->method('getGender')->willReturn(MGenderTypeEnum::MALE);
        $sender->method('getCountryCode')->willReturn($this->faker->countryISOAlpha3());
        $sender->method('getStreet')->willReturn($this->faker->streetAddress());
        $sender->method('getPostalCode')->willReturn($this->faker->postcode());
        $sender->method('getCity')->willReturn($this->faker->city());
        $sender->method('getPhoneNumber')->willReturn($this->faker->phoneNumber());
        $sender->method('getEmail')->willReturn($this->faker->email());
        $sender->method('getIdentificationType')->willReturn(MIdentificationTypeEnum::PASSPORT);

        /** @var MSenderInterface $sender */
        $targetSender = SenderFactory::makeFrom($sender);

        $this->assertInstanceOf(Sender::class, $targetSender);
        $this->assertSame($sender->getFirstName(), $targetSender->first_name);
        $this->assertSame($sender->getLastName(), $targetSender->last_name);
        $this->assertEquals($sender->getBirthDate(), $targetSender->birth_date);
        $this->assertEquals(GenderTypeEnum::MALE, $targetSender->gender);
        $this->assertEquals($sender->getCountryCode(), $targetSender->country_code);
        $this->assertEquals($sender->getStreet(), $targetSender->street);
        $this->assertEquals($sender->getPostalCode(), $targetSender->postal_code);
        $this->assertEquals($sender->getCity(), $targetSender->city);
        $this->assertEquals($sender->getPhoneNumber(), $targetSender->phone_number);
        $this->assertEquals(IdentificationTypeEnum::PASSPORT, $targetSender->identification_type);
        $this->assertEquals($sender->getIdentificationNumber(), $targetSender->identification_number);
        $this->assertEquals($sender->getIdentificationExpiry(), $targetSender->identification_expiry);
    }
}
