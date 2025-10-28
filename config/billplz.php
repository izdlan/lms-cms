<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Billplz Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Billplz payment gateway integration
    |
    */

    // API Key from Billplz dashboard
    'api_key' => env('BILLPLZ_API_KEY'),

    // Collection ID from Billplz dashboard
    'collection_id' => env('BILLPLZ_COLLECTION_ID'),

    // Webhook verification key
    'webhook_key' => env('BILLPLZ_WEBHOOK_KEY'),

    // X-Signature key for webhook verification
    'x_signature_key' => env('BILLPLZ_X_SIGNATURE_KEY'),

    // Sandbox mode (true for testing, false for production)
    'sandbox' => env('BILLPLZ_SANDBOX', true),

    // Default currency (Billplz uses MYR)
    'currency' => 'MYR',

    // Default payment timeout (in minutes)
    'timeout' => 30,

    // Auto redirect after payment
    'auto_redirect' => true,

    // Enable logging
    'logging' => env('BILLPLZ_LOGGING', true),

    // Payment methods to enable
    'payment_methods' => [
        'fpx' => true,      // Online banking
        'card' => true,     // Credit/Debit cards
        'ewallet' => true,  // E-wallets
    ],

    // Default callback and redirect URLs
    'callback_url' => env('BILLPLZ_CALLBACK_URL', '/payment/billplz/callback'),
    'redirect_url' => env('BILLPLZ_REDIRECT_URL', '/payment/billplz/redirect'),
];
