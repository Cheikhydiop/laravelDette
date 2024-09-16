<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Notification Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the default notification channel that will be used
    | for notifications sent by the application. The default value is set to
    | the "mail" channel, but you may change it to "database" or others if needed.
    |
    */

    'default' => env('NOTIFICATIONS_CHANNEL', 'mail'),

    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    |
    | This option controls the channels available for sending notifications.
    | You may customize the channels and their configurations here.
    |
    */

    'channels' => [
        'mail' => [
            'driver' => 'mail',
            // Configuration mail...
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'notifications',
        ],
        // Autres canaux...
    ],
];
