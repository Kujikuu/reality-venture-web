<?php

return [

    'dome' => [
        'url' => env('DOME_API_URL', 'https://the-dome.test'),
        'token' => env('DOME_API_TOKEN'),
        'connect_timeout' => env('DOME_CONNECT_TIMEOUT', 3),
        'timeout' => env('DOME_TIMEOUT', 8),
    ],

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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'google' => [
        'sheets_webhook_url' => env('GOOGLE_SHEETS_WEBHOOK_URL'),
        'calendar' => [
            'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
            'refresh_token' => env('GOOGLE_CALENDAR_REFRESH_TOKEN'),
            'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),
            'default_duration_minutes' => (int) env('GOOGLE_MEET_DEFAULT_DURATION_MINUTES', 30),
        ],
    ],

    'rv' => [
        'admin_email' => env('RV_ADMIN_EMAIL', 'be@rv.com.sa'),
    ],

    'rv_club' => [
        'whatsapp_link' => env('RV_CLUB_WHATSAPP_LINK'),
    ],

    'blog' => [
        'url' => env('BLOG_API_URL', 'https://blog.test'),
        'api_key' => env('BLOG_API_KEY'),
    ],

];
