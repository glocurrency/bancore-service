<?php

namespace GloCurrency\Bancore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Quota;

class QuotaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quota::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'bancore_transaction_id' => Transaction::factory(),
            'send_currency_code' => $this->faker->currencyCode(),
            'send_amount' => $this->faker->randomNumber(),
            'receive_currency_code' => $this->faker->currencyCode(),
            'reference' => $this->faker->uuid(),
        ];
    }
}
