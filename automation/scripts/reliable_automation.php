<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RELIABLE AUTOMATION WATCHER ===\n";
echo "===================================\n\n";

$filePath = storage_path('app/students/Enrollment OEM.xlsx');

if (!file_exists($filePath)) {
    echo "❌ Excel file not found: $filePath\n";
    exit(1);
}

echo "✅ Excel file found: $filePath\n";
echo "📅 Last modified: " . date('Y-m-d H:i:s', filemtime($filePath)) . "\n";
echo "📊 File size: " . number_format(filesize($filePath)) . " bytes\n\n";

// Set automation status
\Illuminate\Support\Facades\Cache::put('automation_status', 'running', now()->addDays(30));
\Illuminate\Support\Facades\Cache::put('automation_file', $filePath, now()->addDays(30));

$lastModified = filemtime($filePath);
$lastFileSize = filesize($filePath);
$checkInterval = 30; // Check every 30 seconds
$importCount = 0;

echo "🚀 Starting reliable automation watcher...\n";
echo "⏱️  Check interval: {$checkInterval} seconds\n";
echo "📁 Monitoring file: " . basename($filePath) . "\n";
echo "Press Ctrl+C to stop\n\n";

while (true) {
    try {
        if (file_exists($filePath)) {
            $currentModified = filemtime($filePath);
            $currentFileSize = filesize($filePath);
            
            // Check both modification time and file size
            if ($currentModified > $lastModified || $currentFileSize !== $lastFileSize) {
                $importCount++;
                
                echo "[" . date('Y-m-d H:i:s') . "] 🔄 CHANGE DETECTED! (Import #{$importCount})\n";
                echo "  📅 Previous: " . date('Y-m-d H:i:s', $lastModified) . "\n";
                echo "  📅 Current:  " . date('Y-m-d H:i:s', $currentModified) . "\n";
                echo "  📊 Size: " . number_format($lastFileSize) . " → " . number_format($currentFileSize) . " bytes\n";
                
                \Illuminate\Support\Facades\Log::info("Reliable automation: File change detected", [
                    'file' => $filePath,
                    'previous_modified' => date('Y-m-d H:i:s', $lastModified),
                    'current_modified' => date('Y-m-d H:i:s', $currentModified),
                    'previous_size' => $lastFileSize,
                    'current_size' => $currentFileSize,
                    'import_count' => $importCount
                ]);

                try {
                    // Clear cache to ensure fresh import
                    \Illuminate\Support\Facades\Cache::forget('last_auto_import_time');
                    
                    echo "  🔄 Running import...\n";
                    
                    // Run the import command directly
                    $exitCode = 0;
                    $output = [];
                    exec('php artisan students:auto-import --force 2>&1', $output, $exitCode);
                    
                    if ($exitCode === 0) {
                        // Parse the output to get results
                        $created = 0;
                        $updated = 0;
                        $errors = 0;
                        
                        foreach ($output as $line) {
                            if (strpos($line, 'Total Created:') !== false) {
                                $created = (int) trim(str_replace('Total Created:', '', $line));
                            } elseif (strpos($line, 'Total Updated:') !== false) {
                                $updated = (int) trim(str_replace('Total Updated:', '', $line));
                            } elseif (strpos($line, 'Total Errors:') !== false) {
                                $errors = (int) trim(str_replace('Total Errors:', '', $line));
                            }
                        }
                        
                        // Update cache
                        \Illuminate\Support\Facades\Cache::put('last_import_time', $currentModified, now()->addDays(30));
                        \Illuminate\Support\Facades\Cache::put('last_auto_import_time', $currentModified, now()->addDays(30));
                        
                        // Store import results for web display
                        \Illuminate\Support\Facades\Cache::put('last_import_results', [
                            'created' => $created,
                            'updated' => $updated,
                            'errors' => $errors,
                            'timestamp' => now()->toDateTimeString()
                        ], now()->addDays(30));
                        
                        \Illuminate\Support\Facades\Log::info("Reliable automation: Import completed successfully", [
                            'created' => $created,
                            'updated' => $updated,
                            'errors' => $errors,
                            'file' => $filePath
                        ]);
                        
                        echo "  ✅ Import completed: {$created} created, {$updated} updated, {$errors} errors\n";
                        
                    } else {
                        echo "  ❌ Import failed with exit code: $exitCode\n";
                        foreach ($output as $line) {
                            if (trim($line)) {
                                echo "    " . $line . "\n";
                            }
                        }
                        \Illuminate\Support\Facades\Log::error("Reliable automation: Import failed", [
                            'exit_code' => $exitCode,
                            'output' => $output,
                            'file' => $filePath
                        ]);
                    }
                    
                    // Update tracking variables
                    $lastModified = $currentModified;
                    $lastFileSize = $currentFileSize;
                    
                } catch (\Exception $e) {
                    echo "  ❌ Error during import: " . $e->getMessage() . "\n";
                    \Illuminate\Support\Facades\Log::error("Reliable automation: Import error", [
                        'error' => $e->getMessage(),
                        'file' => $filePath
                    ]);
                }
            } else {
                // Show status every 2 minutes
                if (time() % 120 === 0) {
                    echo "[" . date('Y-m-d H:i:s') . "] 👀 Monitoring... (No changes detected)\n";
                }
            }
        } else {
            echo "[" . date('Y-m-d H:i:s') . "] ⚠️  File not found: $filePath\n";
            \Illuminate\Support\Facades\Log::warning("Reliable automation: File not found", ['file' => $filePath]);
        }
        
    } catch (\Exception $e) {
        echo "[" . date('Y-m-d H:i:s') . "] ❌ Error: " . $e->getMessage() . "\n";
        \Illuminate\Support\Facades\Log::error("Reliable automation: General error", [
            'error' => $e->getMessage(),
            'file' => $filePath
        ]);
    }
    
    sleep($checkInterval);
}
