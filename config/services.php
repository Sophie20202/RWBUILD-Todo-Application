<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

'ist_africa' => [
    'client_id' => env('IST_AFRICA_CLIENT_ID'),
    'client_secret' => env('IST_AFRICA_CLIENT_SECRET'),
    'redirect' => env('IST_AFRICA_REDIRECT_URI'),
    'auth_url' => env('IST_AFRICA_AUTH_URL'),      // Port 3000 - Frontend login page
    'token_url' => env('IST_AFRICA_TOKEN_URL'),    // Port 5000 - Backend API
    'user_url' => env('IST_AFRICA_USER_URL'),
],

];