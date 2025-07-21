<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "ably", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [
        'pusher' => [
            'app_id' => env('PUSHER_APP_ID'),
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
            'empty' => [
                // This is a multi line comment
                // See, another line
            ],
            'empty_star' => [
                /**
                 * This is many line
                 */
            ],
            'empty_star_multiple' => [
                /**
                 * This is many line
                 */
                /**
                 * This is many many line
                 */
            ],
            'options' => [

                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html

                [
                    // Ahead Comment
                    'driver' => 'pusher',
                    // Under
                    'options' => [
                        // Inside
                    ],
                ],
            ],
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'options2' => [
                // This is a test
                'cluster' => env('PUSHER_APP_CLUSTER'),
                // Testing 1234
                'useTLS' => true,
            ],
            'secret' => env('PUSHER_APP_SECRET'),
        ],
        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],
        'redis' => [
            'connection' => 'default',
            'driver' => 'redis',
        ],
        'log' => [
            'driver' => 'log',
        ],
        'null' => [
            'driver' => 'null',
        ],
    ],
];
