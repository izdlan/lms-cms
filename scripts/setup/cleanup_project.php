<?php
/**
 * Project Cleanup Script
 * Removes unnecessary files and organizes the project structure
 */

echo "=== LMS OLYMPIA PROJECT CLEANUP ===\n\n";

echo "This script will help you clean up unnecessary files from the project.\n\n";

echo "📋 CLEANUP OPTIONS:\n";
echo "1. Remove all debug scripts (scripts/debug/)\n";
echo "2. Remove all test scripts (scripts/testing/)\n";
echo "3. Remove temporary files (temp/)\n";
echo "4. Remove all cleanup files (scripts/setup/cleanup_*.php)\n";
echo "5. Full cleanup (options 1-4)\n";
echo "6. Show project statistics\n";
echo "7. Exit\n\n";

$choice = readline("Enter your choice (1-7): ");

switch ($choice) {
    case '1':
        echo "\n🗑️ REMOVING DEBUG SCRIPTS...\n";
        $debugDir = 'scripts/debug/';
        if (is_dir($debugDir)) {
            $files = glob($debugDir . '*.php');
            foreach ($files as $file) {
                if (unlink($file)) {
                    echo "  ✅ Removed: " . basename($file) . "\n";
                } else {
                    echo "  ❌ Failed to remove: " . basename($file) . "\n";
                }
            }
            if (count($files) > 0) {
                echo "\n✅ Debug scripts cleanup completed!\n";
            } else {
                echo "\nℹ️ No debug scripts found.\n";
            }
        } else {
            echo "\n❌ Debug directory not found.\n";
        }
        break;
        
    case '2':
        echo "\n🗑️ REMOVING TEST SCRIPTS...\n";
        $testDir = 'scripts/testing/';
        if (is_dir($testDir)) {
            $files = glob($testDir . '*.php');
            foreach ($files as $file) {
                if (unlink($file)) {
                    echo "  ✅ Removed: " . basename($file) . "\n";
                } else {
                    echo "  ❌ Failed to remove: " . basename($file) . "\n";
                }
            }
            if (count($files) > 0) {
                echo "\n✅ Test scripts cleanup completed!\n";
            } else {
                echo "\nℹ️ No test scripts found.\n";
            }
        } else {
            echo "\n❌ Test directory not found.\n";
        }
        break;
        
    case '3':
        echo "\n🗑️ REMOVING TEMPORARY FILES...\n";
        $tempDir = 'temp/';
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    if (unlink($file)) {
                        echo "  ✅ Removed: " . basename($file) . "\n";
                    } else {
                        echo "  ❌ Failed to remove: " . basename($file) . "\n";
                    }
                }
            }
            echo "\n✅ Temporary files cleanup completed!\n";
        } else {
            echo "\n❌ Temp directory not found.\n";
        }
        break;
        
    case '4':
        echo "\n🗑️ REMOVING CLEANUP SCRIPTS...\n";
        $cleanupFiles = glob('scripts/setup/cleanup_*.php');
        foreach ($cleanupFiles as $file) {
            if (unlink($file)) {
                echo "  ✅ Removed: " . basename($file) . "\n";
            } else {
                echo "  ❌ Failed to remove: " . basename($file) . "\n";
            }
        }
        if (count($cleanupFiles) > 0) {
            echo "\n✅ Cleanup scripts removed!\n";
        } else {
            echo "\nℹ️ No cleanup scripts found.\n";
        }
        break;
        
    case '5':
        echo "\n🗑️ FULL CLEANUP...\n";
        echo "This will remove debug scripts, test scripts, and temporary files.\n";
        $confirm = readline("Are you sure? (yes/no): ");
        if (strtolower($confirm) === 'yes') {
            // Remove debug scripts
            $debugDir = 'scripts/debug/';
            if (is_dir($debugDir)) {
                $files = glob($debugDir . '*.php');
                foreach ($files as $file) {
                    unlink($file);
                }
                echo "✅ Debug scripts removed\n";
            }
            
            // Remove test scripts
            $testDir = 'scripts/testing/';
            if (is_dir($testDir)) {
                $files = glob($testDir . '*.php');
                foreach ($files as $file) {
                    unlink($file);
                }
                echo "✅ Test scripts removed\n";
            }
            
            // Remove temp files
            $tempDir = 'temp/';
            if (is_dir($tempDir)) {
                $files = glob($tempDir . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                echo "✅ Temporary files removed\n";
            }
            
            echo "\n🎉 Full cleanup completed!\n";
        } else {
            echo "\n❌ Cleanup cancelled.\n";
        }
        break;
        
    case '6':
        echo "\n📊 PROJECT STATISTICS:\n";
        echo "====================\n";
        
        // Count files by category
        $debugFiles = glob('scripts/debug/*.php');
        $testFiles = glob('scripts/testing/*.php');
        $setupFiles = glob('scripts/setup/*.php');
        $docFiles = glob('docs/**/*.md');
        $dataFiles = glob('data/*');
        $tempFiles = glob('temp/*');
        
        echo "Debug Scripts: " . count($debugFiles) . " files\n";
        echo "Test Scripts: " . count($testFiles) . " files\n";
        echo "Setup Scripts: " . count($setupFiles) . " files\n";
        echo "Documentation: " . count($docFiles) . " files\n";
        echo "Data Files: " . count($dataFiles) . " files\n";
        echo "Temp Files: " . count($tempFiles) . " files\n";
        
        $totalSize = 0;
        $allFiles = array_merge($debugFiles, $testFiles, $setupFiles, $dataFiles, $tempFiles);
        foreach ($allFiles as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
            }
        }
        
        echo "\nTotal Size: " . number_format($totalSize / 1024, 2) . " KB\n";
        break;
        
    case '7':
        echo "\n👋 Goodbye!\n";
        break;
        
    default:
        echo "\n❌ Invalid choice. Please run the script again.\n";
        break;
}

echo "\n=== CLEANUP COMPLETED ===\n";
