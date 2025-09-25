<?php
require "vendor/autoload.php";

$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "Student Import Automation - Running...\n";
echo "=====================================\n\n";

$filePath = "C:/xampp/htdocs/LMS_Olympia/storage/app/students/Enrollment OEM.xlsx";
$lastModified = file_exists($filePath) ? filemtime($filePath) : 0;
$checkInterval = 30; // Check every 30 seconds

echo "Watching file: $filePath\n";
echo "Check interval: {$checkInterval} seconds\n";
echo "Press Ctrl+C to stop\n\n";

while (true) {
    if (file_exists($filePath)) {
        $currentModified = filemtime($filePath);
        
        if ($currentModified > $lastModified) {
            echo "[" . date("Y-m-d H:i:s", strtotime("+8 hours")) . "] File change detected! Starting import...\n";
            
            try {
                // Run the auto-import command
                $exitCode = 0;
                $output = [];
                exec("php artisan students:auto-import --force 2>&1", $output, $exitCode);
                
                if ($exitCode === 0) {
                    echo "[" . date("Y-m-d H:i:s", strtotime("+8 hours")) . "] ✓ Import completed successfully\n";
                    foreach ($output as $line) {
                        if (trim($line) && !strpos($line, "Starting automatic student import")) {
                            echo "  " . $line . "\n";
                        }
                    }
                } else {
                    echo "[" . date("Y-m-d H:i:s", strtotime("+8 hours")) . "] ✗ Import failed with exit code: $exitCode\n";
                    foreach ($output as $line) {
                        if (trim($line)) {
                            echo "  " . $line . "\n";
                        }
                    }
                }
                
                $lastModified = $currentModified;
                
            } catch (Exception $e) {
                echo "[" . date("Y-m-d H:i:s", strtotime("+8 hours")) . "] ✗ Error during import: " . $e->getMessage() . "\n";
            }
        } else {
            echo "[" . date("Y-m-d H:i:s", strtotime("+8 hours")) . "] No changes detected\n";
        }
    } else {
        echo "[" . date("Y-m-d H:i:s", strtotime("+8 hours")) . "] File not found: $filePath\n";
    }
    
    sleep($checkInterval);
}
