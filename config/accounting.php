<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Accounting System Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for integrating with external accounting systems
    |
    */

    // Enable/disable accounting integration
    'enabled' => env('ACCOUNTING_ENABLED', false),

    // Accounting system API URL
    'api_url' => env('ACCOUNTING_API_URL'),

    // API Key for authentication
    'api_key' => env('ACCOUNTING_API_KEY'),

    // API timeout in seconds
    'timeout' => env('ACCOUNTING_TIMEOUT', 30),

    // Retry attempts for failed requests
    'retry_attempts' => env('ACCOUNTING_RETRY_ATTEMPTS', 3),

    // Retry delay in seconds
    'retry_delay' => env('ACCOUNTING_RETRY_DELAY', 5),

    // Enable logging
    'logging' => env('ACCOUNTING_LOGGING', true),

    // Auto-sync payments (push method)
    'auto_sync' => env('ACCOUNTING_AUTO_SYNC', true),

    // Sync delay in minutes (for batch processing)
    'sync_delay' => env('ACCOUNTING_SYNC_DELAY', 5),

    // Payment types to sync
    'sync_payment_types' => [
        'course_fee',
        'general_fee',
        'assignment_fee',
        'invoice_payment',
    ],

    // Fields to include in sync
    'sync_fields' => [
        'lms_payment_id',
        'billplz_id',
        'student_id',
        'student_name',
        'student_email',
        'student_phone',
        'amount',
        'currency',
        'payment_status',
        'payment_method',
        'transaction_id',
        'description',
        'payment_type',
        'reference_id',
        'reference_type',
        'paid_at',
        'created_at',
    ],

    // Webhook configuration
    'webhook' => [
        'enabled' => env('ACCOUNTING_WEBHOOK_ENABLED', false),
        'url' => env('ACCOUNTING_WEBHOOK_URL'),
        'secret' => env('ACCOUNTING_WEBHOOK_SECRET'),
    ],
];
