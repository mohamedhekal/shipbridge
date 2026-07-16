<?php

declare(strict_types=1);

return [

    'default' => env('SHIPBRIDGE_DRIVER', 'fake'),

    'drivers' => [

        'fake' => [
            'driver' => 'fake',
        ],

        'http' => [
            'driver' => 'http',
            'base_url' => env('SHIPBRIDGE_HTTP_BASE_URL', 'https://carrier.example/v1'),
            'token' => env('SHIPBRIDGE_HTTP_TOKEN'),
            'timeout' => 15,
            'status_map' => [
                'CREATED' => 'created',
                'LABEL_PRINTED' => 'labeled',
                'PICKED_UP' => 'picked_up',
                'IN_TRANSIT' => 'in_transit',
                'OFD' => 'out_for_delivery',
                'DELIVERED' => 'delivered',
                'EXCEPTION' => 'exception',
                'CANCELLED' => 'cancelled',
                'RETURNED' => 'returned',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global status aliases
    |--------------------------------------------------------------------------
    |
    | Carrier-agnostic aliases merged after the active driver's status_map.
    |
    */
    'status_aliases' => [
        'shipped' => 'in_transit',
        'transit' => 'in_transit',
        'out for delivery' => 'out_for_delivery',
        'failed' => 'exception',
        'rto' => 'returned',
    ],

];
