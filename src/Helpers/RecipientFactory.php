<?php

namespace GloCurrency\Bancore\Helpers;

use Illuminate\Support\Facades\Config;
use GloCurrency\MiddlewareBlocks\Contracts\RecipientInterface as MRecipientInterface;
use GloCurrency\Bancore\Models\Recipient;

class RecipientFactory
{
    public static function makeFrom(MRecipientInterface $recipient): Recipient
    {
        return new Recipient([
            'first_name' => $recipient->getFirstName(),
            'last_name' => $recipient->getLastName(),
            'bank_code' => $recipient->getBankCode(),
            'bank_account' => $recipient->getBankAccount(),
            'street' => $recipient->getStreet(),
            'postal_code' => $recipient->getPostalCode(),
            'city' => $recipient->getCity(),
            'phone_number' => $recipient->getPhoneNumber() ?? (string) Config::get('services.bancore.recipient_phone_number'),
            'email' => $recipient->getEmail(),
            'country_code' => $recipient->getCountryCode(),
        ]);
    }
}
