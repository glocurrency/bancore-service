<?php

namespace GloCurrency\Bancore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\Bancore\Models\Transaction;
use GloCurrency\Bancore\Models\Sender;
use GloCurrency\Bancore\Models\Recipient;
use GloCurrency\Bancore\Models\Bank;
use GloCurrency\Bancore\Enums\TransactionStateCodeEnum;
use GloCurrency\Bancore\Bancore;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'transaction_id' => (Bancore::$transactionModel)::factory(),
            'processing_item_id' => (Bancore::$processingItemModel)::factory(),
            'bancore_sender_id' => Sender::factory(),
            'bancore_recipient_id' => Recipient::factory(),
            'identifier_source_type' => Bank::class,
            'identifier_source_id' => Bank::factory(),
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'reference' => $this->faker->uuid(),
            'receive_currency_code' => $this->faker->currencyCode(),
            'receive_amount' => $this->faker->randomFloat(2, 1),
        ];
    }
}
