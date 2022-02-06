<?php

namespace GloCurrency\Bancore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\Bancore\Models\Recipient;

class RecipientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recipient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'country_code' => $this->faker->countryISOAlpha3(),
            'phone_number' => $this->faker->phoneNumber(),
        ];
    }
}
