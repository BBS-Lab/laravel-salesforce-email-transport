<?php

declare(strict_types=1);

return [

    'auth' => [
        'url' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_URL'),
        'client_id' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CLIENT_ID'),
        'client_secret' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CLIENT_SECRET'),
        'grant_type' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_GRANT_TYPE'),
        'resource' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_RESOURCE'),
        'cache' => [
            'enabled' => (bool)env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CACHE_ENABLED', true),
            'key' => env('SALESFORCE_EMAIL_TRANSPORT_AUTH_CACHE_KEY', 'salesforce-email-transport-token'),
        ],
    ],

    'api' => [
        'url' => env('SALESFORCE_EMAIL_TRANSPORT_API_URL'),
        'definition_key' => env('SALESFORCE_EMAIL_TRANSPORT_API_DEFINITION_KEY'),
    ],

];
