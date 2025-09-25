<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ONEDRIVE FULL IMPORT PROCESS ===\n\n";

use App\Services\OneDriveExcelImportService;
use App\Models\User;

// Create OneDrive service instance
$onedriveService = new OneDriveExcelImportService();

// Override URL with working OneDrive URL
$reflection = new ReflectionClass($onedriveService);
$urlProperty = $reflection->getProperty('onedriveUrl');
$urlProperty->setAccessible(true);
$urlProperty->setValue($onedriveService, 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1');

echo "OneDrive URL: https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1\n\n";

// Clear existing students for clean test
echo "Clearing existing students...\n";
User::where('role', 'student')->delete();
$initialCount = User::where('role', 'student')->count();
echo "Initial student count: {$initialCount}\n\n";

// Test connection first
echo "=== TESTING CONNECTION ===\n";
try {
    $connectionResult = $onedriveService->testConnection();
    
    if ($connectionResult['success']) {
        echo "✅ Connection test passed!\n";
        echo "Message: " . $connectionResult['message'] . "\n\n";
    } else {
        echo "❌ Connection test failed!\n";
        echo "Error: " . $connectionResult['error'] . "\n";
        echo "Message: " . $connectionResult['message'] . "\n";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Connection test error: " . $e->getMessage() . "\n";
    exit;
}

// Run the full import
echo "=== RUNNING FULL IMPORT ===\n";
try {
    $importResult = $onedriveService->importFromOneDrive();
    
    if ($importResult['success']) {
        echo "✅ Import completed successfully!\n";
        echo "Created: " . $importResult['created'] . " students\n";
        echo "Updated: " . $importResult['updated'] . " students\n";
        echo "Errors: " . $importResult['errors'] . "\n";
        
        if (isset($importResult['processed_sheets']) && !empty($importResult['processed_sheets'])) {
            echo "\nProcessed sheets:\n";
            foreach ($importResult['processed_sheets'] as $sheet) {
                echo "  - {$sheet['sheet']}: Created={$sheet['created']}, Updated={$sheet['updated']}, Errors={$sheet['errors']}\n";
            }
        }
        
        // Check final student count
        $finalCount = User::where('role', 'student')->count();
        echo "\nFinal student count: {$finalCount}\n";
        echo "Total imported: " . ($finalCount - $initialCount) . "\n";
        
        // Show some sample students
        echo "\nSample imported students:\n";
        $sampleStudents = User::where('role', 'student')->take(5)->get();
        foreach ($sampleStudents as $student) {
            echo "  - {$student->name} ({$student->ic}) - {$student->source_sheet}\n";
        }
        
        if ($finalCount > $initialCount) {
            echo "\n🎉 SUCCESS: OneDrive import is working perfectly!\n";
        } else {
            echo "\n⚠️  WARNING: No students were imported. Check the Excel file content.\n";
        }
        
    } else {
        echo "❌ Import failed!\n";
        echo "Error: " . $importResult['error'] . "\n";
        echo "Created: " . $importResult['created'] . "\n";
        echo "Updated: " . $importResult['updated'] . "\n";
        echo "Errors: " . $importResult['errors'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Import error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
