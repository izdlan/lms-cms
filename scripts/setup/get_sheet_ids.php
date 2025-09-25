<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GETTING GOOGLE SHEETS TAB IDs ===\n\n";

$baseUrl = 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk';

// Test different gid values to find the correct ones for each tab
$tabs = [
    'DHU LMS' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    'IUC LMS' => [11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
    'VIVA-IUC LMS' => [21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
    'LUC LMS' => [31, 32, 33, 34, 35, 36, 37, 38, 39, 40],
    'EXECUTIVE LMS' => [41, 42, 43, 44, 45, 46, 47, 48, 49, 50],
    'UPM LMS' => [51, 52, 53, 54, 55, 56, 57, 58, 59, 60],
    'TVET LMS' => [61, 62, 63, 64, 65, 66, 67, 68, 69, 70]
];

$foundTabs = [];

foreach ($tabs as $tabName => $gids) {
    echo "Testing $tabName...\n";
    
    foreach ($gids as $gid) {
        $url = $baseUrl . '/export?format=csv&gid=' . $gid;
        
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $csvData = str_getcsv($response->body(), "\n");
                
                // Check if this looks like student data (has NAME, ADDRESS, IC columns)
                $hasStudentData = false;
                foreach ($csvData as $row) {
                    if (stripos($row, 'NAME') !== false && 
                        stripos($row, 'ADDRESS') !== false && 
                        stripos($row, 'IC') !== false) {
                        $hasStudentData = true;
                        break;
                    }
                }
                
                if ($hasStudentData) {
                    echo "  ✅ Found $tabName at gid=$gid with student data\n";
                    $foundTabs[$tabName] = $gid;
                    break;
                }
            }
        } catch (Exception $e) {
            // Continue to next gid
        }
    }
    
    if (!isset($foundTabs[$tabName])) {
        echo "  ❌ Could not find $tabName\n";
    }
}

echo "\n=== FOUND TABS ===\n";
foreach ($foundTabs as $tabName => $gid) {
    echo "$tabName: gid=$gid\n";
}

echo "\n=== TESTING IMPORT FROM ALL TABS ===\n";
$totalCreated = 0;
$totalUpdated = 0;
$totalErrors = 0;

foreach ($foundTabs as $tabName => $gid) {
    echo "\nTesting import from $tabName (gid=$gid)...\n";
    
    try {
        $url = $baseUrl . '/export?format=csv&gid=' . $gid;
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $csvData = str_getcsv($response->body(), "\n");
            echo "  Rows in $tabName: " . count($csvData) . "\n";
            
            // Count potential students (rows with data)
            $studentRows = 0;
            foreach ($csvData as $row) {
                $cells = str_getcsv($row);
                if (count($cells) > 5 && !empty(trim($cells[0]))) {
                    $studentRows++;
                }
            }
            echo "  Potential students: $studentRows\n";
        }
    } catch (Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
}

