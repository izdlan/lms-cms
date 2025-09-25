<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging Excel Content...\n";
echo "=========================\n\n";

$filePath = 'C:/xampp/htdocs/LMS_Olympia/storage/app/students/Enrollment OEM.xlsx';

if (!file_exists($filePath)) {
    echo "File not found: $filePath\n";
    exit(1);
}

echo "File exists: $filePath\n";
echo "File size: " . filesize($filePath) . " bytes\n";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($filePath)) . "\n\n";

// Check current students in database
$currentStudents = App\Models\User::where('role', 'student')->get(['name', 'ic', 'created_at']);
echo "Current students in database: " . $currentStudents->count() . "\n";

// Show last 10 students
echo "\nLast 10 students in database:\n";
foreach ($currentStudents->take(10) as $student) {
    echo "- {$student->name} (IC: {$student->ic}) - Created: {$student->created_at}\n";
}

// Test the import service directly
echo "\nTesting XlsxImportService directly...\n";
try {
    $service = new App\Services\XlsxImportService();
    $result = $service->importFromXlsx($filePath);
    
    echo "Import result:\n";
    echo "- Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
    echo "- Created: " . $result['created'] . "\n";
    echo "- Updated: " . $result['updated'] . "\n";
    echo "- Errors: " . $result['errors'] . "\n";
    
    if (isset($result['message'])) {
        echo "- Message: " . $result['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check students again after import
$newStudents = App\Models\User::where('role', 'student')->get(['name', 'ic', 'created_at']);
echo "\nStudents after import: " . $newStudents->count() . "\n";

// Show any new students
$newStudentsList = $newStudents->where('created_at', '>', now()->subMinutes(5));
if ($newStudentsList->count() > 0) {
    echo "\nNew students added in last 5 minutes:\n";
    foreach ($newStudentsList as $student) {
        echo "- {$student->name} (IC: {$student->ic}) - Created: {$student->created_at}\n";
    }
} else {
    echo "\nNo new students added in last 5 minutes.\n";
}

echo "\nDebug completed!\n";

