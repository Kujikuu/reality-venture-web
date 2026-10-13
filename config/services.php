<?php

return [

    /*
    | Sniper Hub intake API. While off, stage 1 stays in this app. At cutover
    | it is turned on and stage 1 goes to the Reality Venture workspace.
    */
    'sniperhub' => [
        'intake_enabled' => (bool) env('SNIPERHUB_INTAKE_ENABLED', false),
        'intake_url' => env('SNIPERHUB_INTAKE_URL'),
        'intake_key' => env('SNIPERHUB_INTAKE_KEY'),
        'timeout' => (int) env('SNIPERHUB_INTAKE_TIMEOUT', 10),
        // Hub's RV applicant pages, e.g. https://hub.example/rv. UIDs this app doesn't hold go there.
        'applicant_url' => env('SNIPERHUB_APPLICANT_URL'),
        // Lets Sniper Hub copy application files during the migration. Off after cutover.
        'export_enabled' => (bool) env('SNIPERHUB_EXPORT_ENABLED', false),
        'export_token' => env('SNIPERHUB_EXPORT_TOKEN'),
        // On at cutover: the pipeline lives in Sniper Hub and this app is read-only for it.
        'pipeline_moved' => (bool) env('SNIPERHUB_PIPELINE_MOVED', false),
        'pipeline_moved_at' => env('SNIPERHUB_PIPELINE_MOVED_AT'),
    ],

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

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
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
