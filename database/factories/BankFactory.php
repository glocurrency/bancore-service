<?php

namespace GloCurrency\Bancore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\Bancore\Models\Bank;
use GloCurrency\Bancore\Bancore;

class BankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bank::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'bank_id' => (Bancore::$bankModel)::factory(),
            'code' => $this->faker->unique()->word(),
        ];
    }
}
