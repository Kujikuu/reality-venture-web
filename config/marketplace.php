<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Commission Rate
    |--------------------------------------------------------------------------
    |
    | The platform commission percentage charged on each booking.
    |
    */

    'commission_rate' => env('COMMISSION_RATE', 15),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for Stripe payments.
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'SAR'),

    /*
    |--------------------------------------------------------------------------
    | Calendly
    |--------------------------------------------------------------------------
    */

    'calendly' => [
        'api_token' => env('CALENDLY_API_TOKEN'),
        'webhook_signing_key' => env('CALENDLY_WEBHOOK_SIGNING_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Unpaid Booking Timeout (minutes)
    |--------------------------------------------------------------------------
    */

    'unpaid_booking_timeout' => 30,

    /*
    |--------------------------------------------------------------------------
    | Cancellation Window (hours)
    |--------------------------------------------------------------------------
    |
    | Minimum hours before session start for full refund eligibility.
    |
    */

    'cancellation_window_hours' => 24,

    /*
    |--------------------------------------------------------------------------
    | Minimum Payout Amount (SAR)
    |--------------------------------------------------------------------------
    */

    'minimum_payout_amount' => env('MINIMUM_PAYOUT_AMOUNT', 100),

];
