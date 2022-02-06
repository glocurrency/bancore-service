<?php

namespace GloCurrency\Bancore\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\Bancore\Database\Factories\SenderFactory;
use BrokeYourBike\CountryCasts\Alpha2Cast;
use BrokeYourBike\BaseModels\BaseUuid;
use BrokeYourBike\Bancore\Interfaces\SenderInterface;
use BrokeYourBike\Bancore\Enums\IdentificationTypeEnum;
use BrokeYourBike\Bancore\Enums\GenderTypeEnum;

/**
 * GloCurrency\Bancore\Models\Sender
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property \Illuminate\Support\Carbon $birth_date
 * @property \BrokeYourBike\Bancore\Enums\GenderTypeEnum|null $gender
 * @property \BrokeYourBike\Bancore\Enums\IdentificationTypeEnum|null $identification_type
 * @property string|null $identification_number
 * @property string|null $identification_expiry
 * @property string $country_code
 * @property string $country_code_alpha2
 * @property string|null $street
 * @property string|null $postal_code
 * @property string|null $city
 * @property string $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Sender extends BaseUuid implements SenderInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bancore_senders';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<mixed>
     */
    protected $casts = [
        'birth_date' => 'datetime:Y-m-d',
        'gender' => GenderTypeEnum::class,
        'identification_type' => IdentificationTypeEnum::class,
        'country_code_alpha2' => Alpha2Cast::class . ':country_code',
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function getCountryCode(): string
    {
        return $this->country_code_alpha2;
    }

    public function getIdentificationType(): ?IdentificationTypeEnum
    {
        return $this->identification_type;
    }

    public function getIdentificationNumber(): ?string
    {
        return $this->identification_number;
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return SenderFactory::new();
    }
}
