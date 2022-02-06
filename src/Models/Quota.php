<?php

namespace GloCurrency\Bancore\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\Bancore\Database\Factories\QuotaFactory;
use BrokeYourBike\BaseModels\BaseUuid;
use BrokeYourBike\Bancore\Interfaces\QuotaInterface;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;

/**
 * GloCurrency\Bancore\Models\Quota
 *
 * @property string $id
 * @property string $bancore_transaction_id
 * @property \BrokeYourBike\Bancore\Enums\ErrorCodeEnum|null $error_code
 * @property string|null $error_code_description
 * @property string $reference
 * @property float|null $rate
 * @property string $send_currency_code
 * @property float $send_amount
 * @property string $receive_currency_code
 * @property float|null $receive_amount
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Quota extends BaseUuid implements QuotaInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bancore_quotas';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<mixed>
     */
    protected $casts = [
        'error_code' => ErrorCodeEnum::class,
        'rate' => 'double',
        'send_amount' => 'double',
        'receive_amount' => 'double',
        'expires_at' => 'datetime',
    ];

    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Get Transaction that the Quota has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transaction()
    {
        return $this->hasOne(
            Transaction::class,
            'id',
            'bancore_transaction_id',
        );
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return QuotaFactory::new();
    }
}
