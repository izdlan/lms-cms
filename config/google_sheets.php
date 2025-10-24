<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Sheets Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Sheets integration. This allows you to specify
    | the Google Sheets URL and other settings for importing student data.
    |
    */
    
    'url' => env('GOOGLE_SHEETS_URL', 'https://docs.google.com/spreadsheets/d/1MnAeovkeOM_pZGx6DqS7hMvJQyaQDEu8/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true'),
    'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID', '1MnAeovkeOM_pZGx6DqS7hMvJQyaQDEu8'),
    'api_key' => env('GOOGLE_SHEETS_API_KEY'),
    'credentials_path' => env('GOOGLE_SHEETS_CREDENTIALS_PATH', 'storage/app/google-credentials.json'),
    'onedrive_url' => env('ONEDRIVE_EXCEL_URL', 'https://api.onedrive.com/v1.0/shares/u!aHR0cHM6Ly8xZHJ2Lm1zL3gvYy81N0U3QTQ3MkJFODkxRkZDL0VTSWRWX1ZiVGVKQmh2Z3h5LWl0cFhjQkhZMEh0X0dWbXBwTXRvbUJCVWRVM1E_ZT1xNFc4aWE/root/content'),
    'google_drive_url' => env('GOOGLE_DRIVE_EXCEL_URL', 'https://docs.google.com/spreadsheets/d/1MnAeovkeOM_pZGx6DqS7hMvJQyaQDEu8/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true'),
    
    /*
    |--------------------------------------------------------------------------
    | LMS Sheets Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the specific LMS sheets that should be imported.
    | Each sheet name maps to a default GID (Google Sheets tab ID).
    | The system will automatically detect the correct GIDs if they differ.
    |
    */
    'lms_sheets' => [
        'UPM LMS' => 'UPM LMS'  // Only process UPM data
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Import Settings
    |--------------------------------------------------------------------------
    |
    | Settings for the Google Sheets import process.
    |
    */
    'import' => [
        'timeout' => 30, // Request timeout in seconds
        'retry_attempts' => 3, // Number of retry attempts for failed requests
        'change_check_interval' => 300, // How often to check for changes (seconds)
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Settings for error handling and logging.
    |
    */
    'error_handling' => [
        'log_failed_requests' => true,
        'log_response_details' => true,
        'max_response_log_length' => 200,
    ],
];
