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

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    ],

    'telegram_bot' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'notifications_enabled' => env('TELEGRAM_NOTIFICATIONS_ENABLED', true),
    ],

    'legal_system' => [
        'url' => env('LEGAL_SYSTEM_URL'),
        'token' => env('LEGAL_SYSTEM_API_KEY'),
        'timeout' => (int) env('LEGAL_SYSTEM_TIMEOUT', 5),
        'concerned_party_endpoint' => env('LEGAL_SYSTEM_CONCERNED_PARTY_ENDPOINT', '/api/concerned-party/emails'),
        'fallback_email' => env('LEGAL_SYSTEM_FALLBACK_EMAIL', env('MAIL_FROM_ADDRESS')),
        'test_recipient' => env('LEGAL_SYSTEM_TEST_RECIPIENT', 'habtamudoc70@gmail.com'),
    ],

];
