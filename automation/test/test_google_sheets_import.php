<?php
/**
 * Google Sheets Import Test
 * 
 * This script tests the Google Sheets import functionality
 * to ensure it's working correctly.
 * 
 * @author LMS Olympia Team
 * @version 1.0.0
 * @since 2025-09-24
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GoogleSheetsImportService;

class GoogleSheetsImportTest
{
    private $googleSheetsService;
    
    public function __construct()
    {
        $this->googleSheetsService = new GoogleSheetsImportService();
    }
    
    public function run()
    {
        echo "Google Sheets Import Test\n";
        echo "========================\n\n";
        
        $this->testConnection();
        $this->testChangeDetection();
        $this->testImport();
        
        echo "\nTest completed.\n";
    }
    
    private function testConnection()
    {
        echo "1. Testing Google Sheets connection...\n";
        
        try {
            $result = $this->googleSheetsService->importFromGoogleSheets();
            
            if ($result['success']) {
                echo "   ✓ Connection successful!\n";
                echo "   Created: " . $result['created'] . " students\n";
                echo "   Updated: " . $result['updated'] . " students\n";
                echo "   Errors: " . $result['errors'] . " students\n";
            } else {
                echo "   ✗ Connection failed: " . ($result['message'] ?? 'Unknown error') . "\n";
            }
        } catch (Exception $e) {
            echo "   ✗ Connection error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function testChangeDetection()
    {
        echo "2. Testing change detection...\n";
        
        try {
            $hasChanges = $this->googleSheetsService->checkForChanges();
            echo "   " . ($hasChanges ? "✓ Changes detected" : "No changes detected") . "\n";
        } catch (Exception $e) {
            echo "   ✗ Change detection error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function testImport()
    {
        echo "3. Testing import process...\n";
        
        try {
            $result = $this->googleSheetsService->importFromGoogleSheets();
            
            if ($result['success']) {
                echo "   ✓ Import process successful!\n";
                echo "   Total processed: " . ($result['created'] + $result['updated']) . " students\n";
                
                if ($result['errors'] > 0) {
                    echo "   ⚠ " . $result['errors'] . " errors (expected for test data)\n";
                }
            } else {
                echo "   ✗ Import process failed: " . ($result['message'] ?? 'Unknown error') . "\n";
            }
        } catch (Exception $e) {
            echo "   ✗ Import process error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
}

// Run the test
$test = new GoogleSheetsImportTest();
$test->run();

