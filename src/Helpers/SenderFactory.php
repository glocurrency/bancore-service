<?php

namespace GloCurrency\Bancore\Helpers;

use Illuminate\Support\Facades\Config;
use GloCurrency\MiddlewareBlocks\Enums\IdentificationTypeEnum as MIdentificationTypeEnum;
use GloCurrency\MiddlewareBlocks\Enums\GenderTypeEnum as MGenderTypeEnum;
use GloCurrency\MiddlewareBlocks\Contracts\SenderInterface as MSenderInterface;
use GloCurrency\Bancore\Models\Sender;
use GloCurrency\Bancore\Enums\IdentificationTypeFactory;
use GloCurrency\Bancore\Enums\GenderTypeFactory;

class SenderFactory
{
    public static function makeFrom(MSenderInterface $sender): Sender
    {
        $target = new Sender([
            'first_name' => $sender->getFirstName(),
            'last_name' => $sender->getLastName(),
            'birth_date' => $sender->getBirthDate(),
            'street' => $sender->getStreet(),
            'postal_code' => $sender->getPostalCode(),
            'city' => $sender->getCity(),
            'phone_number' => $sender->getPhoneNumber() ?? (string) Config::get('services.bancore.sender_phone_number'),
            'country_code' => $sender->getCountryCode(),
        ]);

        if ($sender->getGender() instanceof MGenderTypeEnum) {
            $target->gender = GenderTypeFactory::makeFrom($sender->getGender());
        }

        if ($sender->getIdentificationType() instanceof MIdentificationTypeEnum) {
            $target->identification_type = IdentificationTypeFactory::makeFrom($sender->getIdentificationType());
            $target->identification_number = $sender->getIdentificationNumber();
            $target->identification_expiry = $sender->getIdentificationExpiry();
        }

        return $target;
    }
}
