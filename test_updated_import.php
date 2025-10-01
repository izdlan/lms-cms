<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing updated import with relaxed requirements...\n";

try {
    $service = new App\Services\SheetSpecificImportService();
    $result = $service->importFromOneDrive();
    
    echo "Import completed:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
    // Check current student count
    $totalStudents = App\Models\User::where('role', 'student')->count();
    echo "Total students in database: $totalStudents\n";
    
    // Check by sheet
    $sheets = ['DHU LMS', 'IUC LMS', 'LUC LMS', 'EXECUTIVE LMS', 'UPM LMS', 'TVET LMS'];
    foreach ($sheets as $sheet) {
        $count = App\Models\User::where('role', 'student')->where('source_sheet', $sheet)->count();
        echo "$sheet: $count students\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}


