<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Students Excel File Path
    |--------------------------------------------------------------------------
    |
    | This is the path to the Excel file that contains student information.
    | The system will automatically sync students from this file.
    |
    */
    'excel_file_path' => env('STUDENTS_EXCEL_PATH', storage_path('app/students.xlsx')),
    
    /*
    |--------------------------------------------------------------------------
    | Auto Sync Interval
    |--------------------------------------------------------------------------
    |
    | How often to automatically sync students from Excel (in minutes).
    | Set to 0 to disable auto-sync.
    |
    */
    'auto_sync_interval' => env('STUDENTS_AUTO_SYNC_INTERVAL', 60), // 60 minutes
];
