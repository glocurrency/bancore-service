<?php

namespace GloCurrency\Bancore\Tests\Feature\Helpers;

use GloCurrency\MiddlewareBlocks\Contracts\RecipientInterface as MRecipientInterface;
use GloCurrency\Bancore\Tests\FeatureTestCase;
use GloCurrency\Bancore\Models\Recipient;
use GloCurrency\Bancore\Helpers\RecipientFactory;

class MakeRecipientTest extends FeatureTestCase
{
    /** @test */
    public function it_can_make_recipient(): void
    {
        $recipient = $this->getMockBuilder(MRecipientInterface::class)->getMock();
        $recipient->method('getFirstName')->willReturn($this->faker->firstName());
        $recipient->method('getLastName')->willReturn($this->faker->lastName());
        $recipient->method('getBankCode')->willReturn($this->faker->unique()->word());
        $recipient->method('getBankAccount')->willReturn($this->faker->numerify('##########'));
        $recipient->method('getCountryCode')->willReturn($this->faker->countryISOAlpha3());
        $recipient->method('getStreet')->willReturn($this->faker->streetAddress());
        $recipient->method('getPostalCode')->willReturn($this->faker->postcode());
        $recipient->method('getCity')->willReturn($this->faker->city());
        $recipient->method('getPhoneNumber')->willReturn($this->faker->phoneNumber());
        $recipient->method('getEmail')->willReturn($this->faker->email());

        /** @var MRecipientInterface $recipient */
        $targetRecipient = RecipientFactory::makeFrom($recipient);

        $this->assertInstanceOf(Recipient::class, $targetRecipient);
        $this->assertSame($recipient->getFirstName(), $targetRecipient->first_name);
        $this->assertSame($recipient->getLastName(), $targetRecipient->last_name);
        $this->assertSame($recipient->getBankCode(), $targetRecipient->bank_code);
        $this->assertSame($recipient->getBankAccount(), $targetRecipient->bank_account);
        $this->assertEquals($recipient->getCountryCode(), $targetRecipient->country_code);
        $this->assertEquals($recipient->getStreet(), $targetRecipient->street);
        $this->assertEquals($recipient->getPostalCode(), $targetRecipient->postal_code);
        $this->assertEquals($recipient->getCity(), $targetRecipient->city);
        $this->assertEquals($recipient->getPhoneNumber(), $targetRecipient->phone_number);
        $this->assertEquals($recipient->getEmail(), $targetRecipient->email);
    }
}
