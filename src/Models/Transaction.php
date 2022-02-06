<?php

namespace GloCurrency\Bancore\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\MiddlewareBlocks\Contracts\ModelWithStateCodeInterface;
use GloCurrency\Bancore\Models\Quota;
use GloCurrency\Bancore\Events\TransactionUpdatedEvent;
use GloCurrency\Bancore\Events\TransactionCreatedEvent;
use GloCurrency\Bancore\Enums\TransactionStateCodeEnum;
use GloCurrency\Bancore\Database\Factories\TransactionFactory;
use GloCurrency\Bancore\Bancore;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\BaseModels\BaseUuid;
use BrokeYourBike\Bancore\Interfaces\TransactionInterface;
use BrokeYourBike\Bancore\Interfaces\SenderInterface;
use BrokeYourBike\Bancore\Interfaces\RecipientInterface;
use BrokeYourBike\Bancore\Interfaces\QuotaInterface;
use BrokeYourBike\Bancore\Interfaces\IdentifierSourceInterface;
use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;

/**
 * GloCurrency\Bancore\Models\Transaction
 *
 * @property string $id
 * @property string $transaction_id
 * @property string $processing_item_id
 * @property string $bancore_sender_id
 * @property string $bancore_recipient_id
 * @property string|null $bancore_quota_id
 * @property string $identifier_source_id
 * @property string $identifier_source_type
 * @property \GloCurrency\Bancore\Enums\TransactionStateCodeEnum $state_code
 * @property string|null $state_code_reason
 * @property \BrokeYourBike\Bancore\Enums\ErrorCodeEnum|null $error_code
 * @property string|null $error_code_description
 * @property string $reference
 * @property string $receive_currency_code
 * @property float $receive_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Transaction extends BaseUuid implements ModelWithStateCodeInterface, SourceModelInterface, TransactionInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bancore_transactions';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<mixed>
     */
    protected $casts = [
        'state_code' => TransactionStateCodeEnum::class,
        'error_code' => ErrorCodeEnum::class,
        'receive_amount' => 'double',
    ];

    /**
     * @var array<mixed>
     */
    protected $dispatchesEvents = [
        'created' => TransactionCreatedEvent::class,
        'updated' => TransactionUpdatedEvent::class,
    ];

    public function getStateCode(): TransactionStateCodeEnum
    {
        return $this->state_code;
    }

    public function getStateCodeReason(): ?string
    {
        return $this->state_code_reason;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getSendCurrencyCode(): string
    {
        return $this->receive_currency_code;
    }

    public function getReceiveCurrencyCode(): string
    {
        return $this->receive_currency_code;
    }

    public function getSendAmount(): float
    {
        return $this->receive_amount;
    }

    public function getReceiveAmount(): float
    {
        return $this->receive_amount;
    }

    public function getSender(): ?SenderInterface
    {
        return $this->sender;
    }

    public function getRecipient(): ?RecipientInterface
    {
        return $this->recipient;
    }

    public function getQuota(): ?QuotaInterface
    {
        return $this->quota;
    }

    public function getIdentifierSource(): ?IdentifierSourceInterface
    {
        return $this->identifierSource;
    }

    /**
     * The ProcessingItem that Transaction belong to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function processingItem()
    {
        return $this->belongsTo(Bancore::$processingItemModel, 'processing_item_id', 'id');
    }

    /**
     * The identifier source that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function identifierSource()
    {
        return $this->morphTo('identifier_source');
    }

    /**
     * The Quota that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quota()
    {
        return $this->hasOne(Quota::class, 'id', 'bancore_quota_id');
    }

    /**
     * The Recipient that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recipient()
    {
        return $this->hasOne(Recipient::class, 'id', 'bancore_recipient_id');
    }

    /**
     * The Sender that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender()
    {
        return $this->hasOne(Sender::class, 'id', 'bancore_sender_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TransactionFactory::new();
    }
}
