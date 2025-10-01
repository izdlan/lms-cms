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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'onedrive' => [
        'excel_url' => env('ONEDRIVE_EXCEL_URL'),
        'timeout' => env('ONEDRIVE_TIMEOUT', 30),
    ],

    'google_sheets' => [
        'api_key' => env('GOOGLE_SHEETS_API_KEY'),
        'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID'),
        'credentials_path' => env('GOOGLE_SHEETS_CREDENTIALS_PATH'),
    ],

];
