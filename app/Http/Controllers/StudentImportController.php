<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SheetSpecificImportService;
use App\Services\GoogleSheetsImportService;
use App\Services\GoogleDriveImportService;

class StudentImportController extends Controller
{
    /**
     * Import students from Google Drive - HTTP endpoint for external cron jobs
     * (Switched from OneDrive to Google Drive for better reliability)
     */
    public function importFromOneDrive()
    {
        try {
            Log::info('HTTP Cron: Starting Google Drive import via StudentImportController');
            
            // Use Google Drive service instead of OneDrive
            $service = new GoogleDriveImportService();
            $result = $service->importFromGoogleDrive();
            
            Log::info('HTTP Cron: Google Drive import completed', $result);
            
            return response()->json([
                'status' => 'success',
                'message' => "Imported {$result['created']} new students, updated {$result['updated']} students. Errors: {$result['errors']}",
                'created' => $result['created'],
                'updated' => $result['updated'],
                'errors' => $result['errors'],
                'processed_sheets' => $result['processed_sheets'] ?? []
            ]);
            
        } catch (\Exception $e) {
            Log::error('HTTP Cron: Google Drive import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Test endpoint to verify the import is working
     */
    public function testImport()
    {
        try {
            Log::info('Test endpoint: Starting Google Drive import test');
            
            $service = new GoogleDriveImportService();
            $result = $service->importFromGoogleDrive();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Test Google Drive import completed successfully',
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test endpoint: Google Drive import failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
