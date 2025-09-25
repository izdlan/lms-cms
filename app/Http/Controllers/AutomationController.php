<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleSheetsImportService;

class AutomationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkAdminAccess()
    {
        if (!\Illuminate\Support\Facades\Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function status()
    {
        $this->checkAdminAccess();
        
        $status = [
            'google_sheets' => [
                'running' => Cache::get('google_sheets_automation_status', 'stopped') === 'running',
                'last_check' => Cache::get('google_sheets_last_check', null),
                'last_import' => Cache::get('last_google_sheets_import_time', null),
                'total_imports' => Cache::get('google_sheets_total_imports', 0),
                'last_error' => Cache::get('google_sheets_last_error', null)
            ]
        ];
        
        return response()->json($status);
    }

    public function start(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $type = $request->input('type', 'google_sheets');
            $interval = $request->input('interval', 300);
            
            if ($type === 'google_sheets') {
                // Start Google Sheets automation (web-based)
                Cache::put('google_sheets_automation_status', 'running', now()->addDays(30));
                Cache::put('google_sheets_automation_interval', $interval, now()->addDays(30));
                Cache::put('google_sheets_automation_last_check', now(), now()->addDays(30));
                
                // Run immediate test
                $googleSheetsService = new GoogleSheetsImportService();
                $testResult = $googleSheetsService->importFromGoogleSheets();
                
                if ($testResult['success']) {
                    Cache::put('last_google_sheets_import_time', now(), now()->addDays(30));
                    Cache::put('last_google_sheets_import_results', $testResult, now()->addDays(30));
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Google Sheets automation started successfully! Web-based monitoring is now active.',
                    'type' => $type,
                    'interval' => $interval,
                    'test_result' => $testResult
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unknown automation type: ' . $type
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error starting automation', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error starting automation: ' . $e->getMessage()
            ]);
        }
    }

    public function stop(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $type = $request->input('type', 'google_sheets');
            
            if ($type === 'google_sheets') {
                // Stop Google Sheets automation
                Cache::put('google_sheets_automation_status', 'stopped', now()->addDays(30));
                
                // Kill any running processes
                if (PHP_OS_FAMILY === 'Windows') {
                    exec('taskkill /F /IM php.exe /FI "COMMANDLINE eq *google_sheets_automation_watcher*" 2>nul');
                } else {
                    exec('pkill -f "google_sheets_automation_watcher" 2>/dev/null');
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Google Sheets automation stopped successfully!',
                    'type' => $type
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unknown automation type: ' . $type
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error stopping automation', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error stopping automation: ' . $e->getMessage()
            ]);
        }
    }

    public function test(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $type = $request->input('type', 'google_sheets');
            
            if ($type === 'google_sheets') {
                $googleSheetsService = new GoogleSheetsImportService();
                $result = $googleSheetsService->importFromGoogleSheets();
                
                // Update cache with test results
                Cache::put('last_google_sheets_import_time', now(), now()->addDays(30));
                Cache::put('last_google_sheets_import_results', $result, now()->addDays(30));
                
                if ($result['success']) {
                    $totalImports = Cache::get('google_sheets_total_imports', 0);
                    Cache::put('google_sheets_total_imports', $totalImports + 1, now()->addDays(30));
                }
                
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['success'] ? 'Google Sheets test completed successfully!' : 'Google Sheets test failed: ' . ($result['message'] ?? 'Unknown error'),
                    'created' => $result['created'] ?? 0,
                    'updated' => $result['updated'] ?? 0,
                    'errors' => $result['errors'] ?? 0,
                    'result' => $result
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unknown automation type: ' . $type
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error testing automation', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error testing automation: ' . $e->getMessage()
            ]);
        }
    }

    // Web-based automation - no background processes needed

    public function runAutomationCheck()
    {
        // This method can be called by a cron job or scheduled task
        if (!Cache::get('google_sheets_web_automation', false)) {
            return response()->json(['message' => 'Automation not running']);
        }
        
        try {
            $googleSheetsService = new GoogleSheetsImportService();
            
            // Check for changes
            if ($googleSheetsService->checkForChanges()) {
                $result = $googleSheetsService->importFromGoogleSheets();
                
                if ($result['success']) {
                    Cache::put('last_google_sheets_import_time', now(), now()->addDays(30));
                    Cache::put('last_google_sheets_import_results', $result, now()->addDays(30));
                    
                    $totalImports = Cache::get('google_sheets_total_imports', 0);
                    Cache::put('google_sheets_total_imports', $totalImports + 1, now()->addDays(30));
                    
                    Log::info('Google Sheets automation: Import completed', $result);
                } else {
                    Cache::put('google_sheets_last_error', $result['message'] ?? 'Unknown error', now()->addDays(30));
                    Log::error('Google Sheets automation: Import failed', $result);
                }
            }
            
            Cache::put('google_sheets_last_check', now(), now()->addDays(30));
            
            return response()->json([
                'success' => true,
                'message' => 'Automation check completed',
                'last_check' => now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            Cache::put('google_sheets_last_error', $e->getMessage(), now()->addDays(30));
            Log::error('Google Sheets automation: Error during check', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error during automation check: ' . $e->getMessage()
            ]);
        }
    }
}
