<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ALL SHEETS IMPORT ===\n\n";

use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

// Clear existing students
User::where('role', 'student')->delete();
echo "Cleared existing students\n";

$localFilePath = 'Enrollment OEM.xlsx';

if (!file_exists($localFilePath)) {
    echo "❌ Local file '{$localFilePath}' not found\n";
    exit;
}

echo "✅ Local file found: {$localFilePath}\n";
echo "File size: " . number_format(filesize($localFilePath)) . " bytes\n\n";

// Get all sheet names
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load($localFilePath);
$sheetNames = $spreadsheet->getSheetNames();

echo "Available sheets: " . implode(', ', $sheetNames) . "\n\n";

$totalCreated = 0;
$totalUpdated = 0;
$totalErrors = 0;

// Test each LMS sheet
$lmsSheets = ['DHU LMS', 'IUC LMS', 'VIVA-IUC LMS', 'LUC LMS', 'EXECUTIVE LMS', 'UPM LMS', 'TVET LMS'];

foreach ($lmsSheets as $sheetName) {
    if (!in_array($sheetName, $sheetNames)) {
        echo "❌ Sheet '{$sheetName}' not found\n";
        continue;
    }
    
    echo "=== TESTING SHEET: {$sheetName} ===\n";
    
    try {
        $import = new StudentsImport();
        $import->setCurrentSheet($sheetName);
        
        // Use the correct method to import a specific sheet
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly([$sheetName]);
        $spreadsheet = $reader->load($localFilePath);
        $worksheet = $spreadsheet->getSheetByName($sheetName);
        
        if ($worksheet) {
            $import->collection(collect($worksheet->toArray()));
        } else {
            throw new \Exception("Sheet '{$sheetName}' not found");
        }
        
        $stats = $import->getStats();
        echo "Created: " . $stats['created'] . "\n";
        echo "Updated: " . $stats['updated'] . "\n";
        echo "Errors: " . $stats['errors'] . "\n";
        
        $totalCreated += $stats['created'];
        $totalUpdated += $stats['updated'];
        $totalErrors += $stats['errors'];
        
        if ($stats['created'] > 0) {
            echo "✅ SUCCESS: {$stats['created']} students imported from {$sheetName}\n";
        } else {
            echo "⚠️  No students imported from {$sheetName}\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Import error for {$sheetName}: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Check final student count
$finalCount = User::where('role', 'student')->count();
echo "=== FINAL RESULTS ===\n";
echo "Total students created: {$totalCreated}\n";
echo "Total students updated: {$totalUpdated}\n";
echo "Total errors: {$totalErrors}\n";
echo "Final student count: {$finalCount}\n";

if ($finalCount > 0) {
    echo "✅ SUCCESS: Total students imported!\n";
    $sampleStudents = User::where('role', 'student')->take(5)->get();
    foreach ($sampleStudents as $student) {
        echo "  - {$student->name} ({$student->ic}) - {$student->source_sheet}\n";
    }
} else {
    echo "❌ No students were imported\n";
}

echo "\n=== TEST COMPLETED ===\n";
