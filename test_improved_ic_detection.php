<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Improved IC Detection...\n\n";

// Clear the log file to see only new entries
file_put_contents('storage/logs/laravel.log', '');

// Test the import
echo "1. Running OneDrive import with improved IC detection...\n";
try {
    $startTime = microtime(true);
    
    $sheetService = new \App\Services\SheetSpecificImportService();
    echo "✅ Service instantiated successfully\n";
    
    // Test the import
    $result = $sheetService->importFromOneDrive();
    
    $endTime = microtime(true);
    $executionTime = round($endTime - $startTime, 2);
    
    if ($result['success']) {
        echo "✅ OneDrive import successful!\n";
        echo "   Created: {$result['created']}\n";
        echo "   Updated: {$result['updated']}\n";
        echo "   Errors: {$result['errors']}\n";
        echo "   Execution time: {$executionTime} seconds\n";
    } else {
        echo "⚠️ OneDrive import failed: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
} catch (\Exception $e) {
    echo "❌ OneDrive import error: " . $e->getMessage() . "\n";
}

// Check IC mapping results
echo "\n2. Checking IC mapping results...\n";

// Check students with real ICs vs auto-generated ICs
$realIcStudents = \App\Models\User::where('role', 'student')
    ->where('ic', 'not like', 'AUTO-%')
    ->count();

$autoIcStudents = \App\Models\User::where('role', 'student')
    ->where('ic', 'like', 'AUTO-%')
    ->count();

echo "Students with real ICs: {$realIcStudents}\n";
echo "Students with auto-generated ICs: {$autoIcStudents}\n";

// Check IC mapping by source sheet
$bySource = \App\Models\User::where('role', 'student')
    ->groupBy('source_sheet')
    ->selectRaw('source_sheet, 
                count(*) as total,
                sum(case when ic like "AUTO-%" then 1 else 0 end) as auto_ic,
                sum(case when ic not like "AUTO-%" then 1 else 0 end) as real_ic')
    ->orderBy('source_sheet')
    ->get();

echo "\nIC Mapping by Sheet:\n";
foreach ($bySource as $source) {
    echo "\n{$source->source_sheet}:\n";
    echo "  Total: {$source->total} students\n";
    echo "  Real ICs: {$source->real_ic} students\n";
    echo "  Auto-generated ICs: {$source->auto_ic} students\n";
    echo "  Real IC %: " . round(($source->real_ic / $source->total) * 100, 1) . "%\n";
}

// Check for the specific students mentioned by the user
echo "\n3. Checking for specific students mentioned:\n";
$mohdAlKhafiz = \App\Models\User::where('role', 'student')
    ->where('name', 'like', '%MOHD AL KHAFIZ%')
    ->first();

if ($mohdAlKhafiz) {
    echo "  - MOHD AL KHAFIZ BIN ZAINAL: {$mohdAlKhafiz->ic}\n";
} else {
    echo "  - MOHD AL KHAFIZ BIN ZAINAL: Not found\n";
}

$liangLinxiang = \App\Models\User::where('role', 'student')
    ->where('name', 'like', '%LIANG LINXIANG%')
    ->first();

if ($liangLinxiang) {
    echo "  - MR. LIANG LINXIANG: {$liangLinxiang->ic}\n";
} else {
    echo "  - MR. LIANG LINXIANG: Not found\n";
}

// Show some examples of real ICs found
echo "\n4. Examples of real ICs found:\n";
$realIcExamples = \App\Models\User::where('role', 'student')
    ->where('ic', 'not like', 'AUTO-%')
    ->limit(15)
    ->get(['name', 'ic', 'source_sheet']);

foreach ($realIcExamples as $student) {
    echo "  - {$student->name} ({$student->ic}) - {$student->source_sheet}\n";
}

// Check if enhanced IC detection was triggered
echo "\n5. Checking if enhanced IC detection was triggered:\n";
$enhancedCount = \App\Models\User::where('role', 'student')
    ->where('ic', 'not like', 'AUTO-%')
    ->where('source_sheet', 'in', ['DHU LMS', 'IUC LMS', 'LUC LMS', 'UPM LMS'])
    ->count();

echo "Students with real ICs in problematic sheets: {$enhancedCount}\n";

echo "\nTest completed!\n";


