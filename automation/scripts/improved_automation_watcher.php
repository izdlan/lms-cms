<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$filePath = storage_path('app/students/Enrollment OEM.xlsx');

echo "=== IMPROVED AUTOMATION WATCHER ===\n";
echo "Watching file: " . $filePath . "\n";
echo "Press Ctrl+C to stop\n\n";

if (!file_exists($filePath)) {
    echo "❌ ERROR: Excel file not found at: $filePath\n";
    echo "Please make sure the file exists before starting automation.\n";
    exit(1);
}

$lastModified = filemtime($filePath);
$lastFileSize = filesize($filePath);
$checkInterval = 30; // Check every 30 seconds for faster detection
$importCount = 0;

echo "✅ File found! Starting monitoring...\n";
echo "📅 Last modified: " . date('Y-m-d H:i:s', $lastModified) . "\n";
echo "📊 File size: " . number_format($lastFileSize) . " bytes\n";
echo "⏱️  Check interval: {$checkInterval} seconds\n\n";

while (true) {
    if (file_exists($filePath)) {
        $currentModified = filemtime($filePath);
        $currentFileSize = filesize($filePath);
        
        // Check both modification time and file size for better detection
        if ($currentModified > $lastModified || $currentFileSize !== $lastFileSize) {
            $importCount++;
            echo "[" . date('Y-m-d H:i:s') . "] 🔄 CHANGE DETECTED! (Import #{$importCount})\n";
            echo "  📅 Previous: " . date('Y-m-d H:i:s', $lastModified) . "\n";
            echo "  📅 Current:  " . date('Y-m-d H:i:s', $currentModified) . "\n";
            echo "  📊 Size: " . number_format($lastFileSize) . " → " . number_format($currentFileSize) . " bytes\n";
            echo "  🚀 Starting import...\n";
            
            try {
                // Clear cache to ensure fresh import
                \Illuminate\Support\Facades\Cache::forget('last_auto_import_time');
                
                // Run the auto-import command with force flag
                $exitCode = 0;
                $output = [];
                $command = 'php artisan students:auto-import --force 2>&1';
                exec($command, $output, $exitCode);
                
                if ($exitCode === 0) {
                    echo "  ✅ Import completed successfully!\n";
                    
                    // Parse output for results
                    $created = 0;
                    $updated = 0;
                    $errors = 0;
                    
                    foreach ($output as $line) {
                        if (strpos($line, 'Total Created:') !== false) {
                            $created = (int)trim(str_replace('Total Created:', '', $line));
                        } elseif (strpos($line, 'Total Updated:') !== false) {
                            $updated = (int)trim(str_replace('Total Updated:', '', $line));
                        } elseif (strpos($line, 'Total Errors:') !== false) {
                            $errors = (int)trim(str_replace('Total Errors:', '', $line));
                        }
                        
                        if (trim($line) && !strpos($line, 'Starting automatic student import')) {
                            echo "    " . $line . "\n";
                        }
                    }
                    
                    echo "  📊 Results: {$created} created, {$updated} updated, {$errors} errors\n";
                    
                    if ($created > 0) {
                        echo "  🎉 NEW STUDENTS ADDED: {$created}\n";
                    }
                    
                } else {
                    echo "  ❌ Import failed with exit code: $exitCode\n";
                    foreach ($output as $line) {
                        if (trim($line)) {
                            echo "    " . $line . "\n";
                        }
                    }
                }
                
                // Update tracking variables
                $lastModified = $currentModified;
                $lastFileSize = $currentFileSize;
                
                echo "  ✅ File tracking updated\n\n";
                
            } catch (Exception $e) {
                echo "  ❌ Error during import: " . $e->getMessage() . "\n\n";
            }
        } else {
            // Show status every 5 minutes
            if (time() % 300 === 0) {
                echo "[" . date('Y-m-d H:i:s') . "] 👀 Monitoring... (No changes detected)\n";
            }
        }
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] ❌ File not found: $filePath\n";
    }
    
    sleep($checkInterval);
}

