<?php

namespace GloCurrency\Bancore\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use GloCurrency\Bancore\Tests\TestCase;
use GloCurrency\Bancore\Config;
use BrokeYourBike\Bancore\Interfaces\ConfigInterface;

class ConfigTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_implemets_config_interface(): void
    {
        $this->assertInstanceOf(ConfigInterface::class, new Config());
    }

    /** @test */
    public function it_will_return_empty_string_if_value_not_found()
    {
        $configPrefix = 'services.bancore.api';

        // config is empty
        config([$configPrefix => []]);

        $config = new Config();

        $this->assertSame('', $config->getUrl());
        $this->assertSame('', $config->getUsername());
        $this->assertSame('', $config->getPassword());
    }

    /** @test */
    public function it_can_return_values()
    {
        $url = $this->faker->url();
        $username = $this->faker->userName();
        $password = $this->faker->password();
        $senderPhoneNumber = $this->faker->phoneNumber();
        $recipientPhoneNumber = $this->faker->phoneNumber();

        $configPrefix = 'services.bancore.api';

        config(["{$configPrefix}.url" => $url]);
        config(["{$configPrefix}.username" => $username]);
        config(["{$configPrefix}.password" => $password]);
        config(["{$configPrefix}.sender_phone_number" => $senderPhoneNumber]);
        config(["{$configPrefix}.recipient_phone_number" => $recipientPhoneNumber]);

        $config = new Config();

        $this->assertSame($url, $config->getUrl());
        $this->assertSame($username, $config->getUsername());
        $this->assertSame($password, $config->getPassword());
    }
}
