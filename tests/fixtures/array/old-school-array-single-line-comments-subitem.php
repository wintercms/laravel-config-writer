<?php

return array(

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

    'connections' => array(
        'pusher' => array(
            'app_id' => env('PUSHER_APP_ID'),
            'client_options' => array(
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ),
            'empty' => array(
                // This is a multi line comment
                // See, another line
            ),
            'empty_star' => array(
                /**
                 * This is many line
                 */
            ),
            'empty_star_multiple' => array(
                /**
                 * This is many line
                 */
                /**
                 * This is many many line
                 */
            ),
            'options' => array(

                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html

                array(
                    // Ahead Comment
                    'driver' => 'pusher',
                    // Under
                    'options' => array(
                        // Inside
                    ),
                ),
            ),
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'options2' => array(
                // This is a test
                'cluster' => env('PUSHER_APP_CLUSTER'),
                // Testing 1234
                'useTLS' => true,
            ),
            'secret' => env('PUSHER_APP_SECRET'),
        ),
        'ably' => array(
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ),
        'redis' => array(
            'connection' => 'default',
            'driver' => 'redis',
        ),
        'log' => array(
            'driver' => 'log',
        ),
        'null' => array(
            'driver' => 'null',
        ),
    ),
);
