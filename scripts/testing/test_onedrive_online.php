<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ONEDRIVE ONLINE IMPORT ===\n\n";

use App\Models\User;
use App\Services\OneDriveExcelImportService;

// Clear existing users to test fresh import
echo "Clearing existing users...\n";
User::where('role', 'student')->delete();
echo "Existing students cleared.\n\n";

// Check current user count
$totalUsers = User::where('role', 'student')->count();
echo "Current total students in database: {$totalUsers}\n\n";

echo "=== TESTING ONEDRIVE CONNECTION ===\n";

try {
    $service = new OneDriveExcelImportService();
    
    // Test connection first
    echo "Testing OneDrive connection...\n";
    $connectionResult = $service->testConnection();
    
    if ($connectionResult['success']) {
        echo "✅ OneDrive connection successful!\n";
        echo "Message: {$connectionResult['message']}\n\n";
        
        echo "=== RUNNING ONEDRIVE IMPORT ===\n";
        $result = $service->importFromOneDrive();
        
        echo "Import result:\n";
        echo "  Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
        echo "  Created: {$result['created']}\n";
        echo "  Updated: {$result['updated']}\n";
        echo "  Errors: {$result['errors']}\n\n";
        
        if (!$result['success']) {
            echo "❌ Import failed: {$result['error']}\n";
        } else {
            echo "Per-sheet results:\n";
            foreach ($result['processed_sheets'] as $sheet) {
                echo "  {$sheet['sheet']}: Created={$sheet['created']}, Updated={$sheet['updated']}, Errors={$sheet['errors']}\n";
            }
            
            // Check user count after import
            $newTotalUsers = User::where('role', 'student')->count();
            echo "\nTotal students after import: {$newTotalUsers}\n";
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
                echo "🎉 Online OneDrive import is working perfectly!\n";
            } else {
                echo "\n❌ ISSUE: Only imported {$newTotalUsers} students (expected ~20)\n";
            }
        }
        
    } else {
        echo "❌ OneDrive connection failed!\n";
        echo "Error: {$connectionResult['error']}\n";
        echo "Message: {$connectionResult['message']}\n";
        
        echo "\n=== TROUBLESHOOTING ===\n";
        echo "The OneDrive link might have permission restrictions.\n";
        echo "Please try one of these solutions:\n";
        echo "1. Make the OneDrive file publicly accessible\n";
        echo "2. Share the file with 'Anyone with the link can view'\n";
        echo "3. Use a different file hosting service (Google Drive, Dropbox)\n";
        echo "4. Download the file manually and use local import\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";

