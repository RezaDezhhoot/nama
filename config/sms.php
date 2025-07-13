<?php

return [
    'smsir' => [
        'api_key' => env('SMSIR_API_KEY'),
        'line_number' => env('SMSIR_LINE_NUMBER'),
        'base_url' => env('SMSIR_BASE_URL'),
        'templates' => [
            'auth' => '510345',
            'checkout' => '610407'
        ]
    ],
    'kaveh_negar' => [
        'api_key' => env('KAVEH_NEGAR_API_KEY'),
        'api_url' => env('KAVEH_NEGAR_API_URL'),

        'template1' => env('KAVEH_NEGAR_TEMPLATE1'),
        'template2' => env('KAVEH_NEGAR_TEMPLATE2'),
        'template3' => env('KAVEH_NEGAR_TEMPLATE3'),
        'template4' => env('KAVEH_NEGAR_TEMPLATE4'),
        'template5' => env('KAVEH_NEGAR_TEMPLATE5'),
        'template6' => env('KAVEH_NEGAR_TEMPLATE6'),
    ],
    'default_driver' => \App\Services\Notification\Channels\SMS\Drivers\KavehNegarDriver::class
];
