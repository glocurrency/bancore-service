<?php

return [
    'api' => [
        'url' => env('BANCORE_API_URL'),
        'username' => env('BANCORE_API_USERNAME'),
        'password' => env('BANCORE_API_PASSWORD'),
    ],
    'sender_phone_number' => env('BANCORE_SENDER_PHONE_NUMBER'),
    'recipient_phone_number' => env('BANCORE_RECIPIENT_PHONE_NUMBER'),
];
