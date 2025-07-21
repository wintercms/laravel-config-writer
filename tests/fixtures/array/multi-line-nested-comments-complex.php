<?php

return [
    'throttle' => [

        /*
        |--------------------------------------------------------------------------
        | Enable throttling of Backend authentication attempts
        |--------------------------------------------------------------------------
        |
        | If set to true, users will be given a limited number of attempts to sign
        | in to the Backend before being blocked for a specified number of minutes.
        |
        */

        'enabled' => true,
        'database' => [
            'table' => 'comments',
            'columns' => [],
        ],

        // 'database' => [
        //     'table' => 'files',
        //     'columns' => []
        // ]

        'wintercms' => [
            'is_awesome' => true,
        ],
        //        'example' => [
        //            'testing' => 'test'
        //        ],
    ],
    'demo' => [
        'wintercms' => [
            'is_awesome' => true,
        ],
        // 'example' => [
        //     'foo' => 'bar',
        // ],
    ],
    'demo_example' => [
        'wintercms' => [
            'is_awesome' => true,
        ],

        // 'example' => [
        //     'foo' => 'bar',
        // ],
        array(
            'a',
        ),
        'b' => [
            'x' => 'y',
            '',
            // Hello world
            'Somerthing' => array(
                'test' => 'ing',
                /**
                 * This is a little test
                 * Maybe
                 */
            ),
            // How complex can we make it?
        ],
        /**
         * Very complex
         */
        // [
        //     'x' => 'y',
        // ]
    ],
];
