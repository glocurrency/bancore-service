<?php

namespace GloCurrency\Bancore\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\Bancore\Database\Factories\BankFactory;
use BrokeYourBike\BaseModels\BaseUuid;
use BrokeYourBike\Bancore\Interfaces\IdentifierSourceInterface;

/**
 * GloCurrency\Bancore\Models\Bank
 *
 * @property string $id
 * @property string $bank_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Bank extends BaseUuid implements IdentifierSourceInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bancore_banks';

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return BankFactory::new();
    }
}
