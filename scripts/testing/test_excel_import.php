<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING EXCEL FILE IMPORT ===\n\n";

use App\Models\User;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

// Check if the Excel file exists
$excelFile = 'enrollment.xlsx';
if (!file_exists($excelFile)) {
    echo "❌ Excel file '{$excelFile}' not found!\n";
    echo "Please download the Excel file from OneDrive and place it in the project root.\n";
    echo "Rename it to 'enrollment.xlsx'\n";
    exit;
}

echo "✅ Excel file found: {$excelFile}\n";
echo "File size: " . number_format(filesize($excelFile)) . " bytes\n\n";

// Clear existing users to test fresh import
echo "Clearing existing users...\n";
User::where('role', 'student')->delete();
echo "Existing students cleared.\n\n";

// Check current user count
$totalUsers = User::where('role', 'student')->count();
echo "Current total students in database: {$totalUsers}\n\n";

echo "=== RUNNING EXCEL IMPORT ===\n";

try {
    $allowedSheets = ['DHU LMS', 'IUC LMS', 'VIVA-IUC LMS', 'LUC LMS'];
    $totalCreated = 0;
    $totalUpdated = 0;
    $totalErrors = 0;

    foreach ($allowedSheets as $sheetName) {
        echo "Processing sheet: {$sheetName}\n";
        try {
            $import = new StudentsImport();
            $import->setCurrentSheet($sheetName);
            Excel::import($import, $excelFile, $sheetName);
            
            $stats = $import->getStats();
            $totalCreated += $stats['created'];
            $totalUpdated += $stats['updated'];
            $totalErrors += $stats['errors'];
            
            echo "  ✅ {$sheetName}: Created={$stats['created']}, Updated={$stats['updated']}, Errors={$stats['errors']}\n";
            
        } catch (\Exception $e) {
            echo "  ❌ Error processing sheet {$sheetName}: " . $e->getMessage() . "\n";
            $totalErrors++;
        }
    }

    // Check user count after import
    $newTotalUsers = User::where('role', 'student')->count();
    echo "\n=== IMPORT RESULTS ===\n";
    echo "Total created: {$totalCreated}\n";
    echo "Total updated: {$totalUpdated}\n";
    echo "Total errors: {$totalErrors}\n";
    echo "Total students after import: {$newTotalUsers}\n";
    echo "Difference: " . ($newTotalUsers - $totalUsers) . "\n";
    
    // Check users by source sheet
    $usersBySource = User::where('role', 'student')
        ->selectRaw('source_sheet, COUNT(*) as count')
        ->groupBy('source_sheet')
        ->get();
    
    echo "\nStudents by source sheet:\n";
    foreach ($usersBySource as $source) {
        echo "  {$source->source_sheet}: {$source->count} students\n";
    }
    
    // Show all students
    echo "\nAll students:\n";
    $allStudents = User::where('role', 'student')->get();
    foreach ($allStudents as $student) {
        echo "  - {$student->name} ({$student->ic}) - {$student->source_sheet}\n";
    }
    
    if ($newTotalUsers >= 20) {
        echo "\n✅ SUCCESS: Imported {$newTotalUsers} students (should be around 20)!\n";
    } else {
        echo "\n❌ ISSUE: Only imported {$newTotalUsers} students (expected ~20)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";

