<?php

namespace GloCurrency\Bancore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\Bancore\Models\Sender;

class SenderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sender::class;

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
            'birth_date' => $this->faker->date('Y-m-d', now()->subYears(30)),
            'country_code' => $this->faker->countryISOAlpha3(),
            'phone_number' => $this->faker->phoneNumber(),
        ];
    }
}
