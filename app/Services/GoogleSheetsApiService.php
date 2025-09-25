<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetsApiService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = config('google_sheets.spreadsheet_id', '1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk');
        $this->initializeClient();
    }

    private function initializeClient()
    {
        try {
            $this->client = new Client();
            $this->client->setApplicationName('LMS Olympia');
            $this->client->setScopes([Sheets::SPREADSHEETS_READONLY]);
            
            // Try to use service account credentials if available
            $credentialsPath = storage_path('app/google-credentials.json');
            if (file_exists($credentialsPath)) {
                $this->client->setAuthConfig($credentialsPath);
                Log::info('Using service account credentials for Google Sheets API');
            } else {
                // Fallback to API key (limited access)
                $apiKey = config('google_sheets.api_key');
                if ($apiKey) {
                    $this->client->setDeveloperKey($apiKey);
                    Log::info('Using API key for Google Sheets access');
                } else {
                    throw new \Exception('No Google Sheets credentials configured');
                }
            }
            
            $this->service = new Sheets($this->client);
            Log::info('Google Sheets API client initialized successfully');
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Sheets API client', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getSheetData($sheetName, $range = null)
    {
        try {
            if ($range) {
                $fullRange = $sheetName . '!' . $range;
            } else {
                $fullRange = $sheetName;
            }
            
            Log::info('Fetching data from Google Sheets API', [
                'sheet' => $sheetName,
                'range' => $fullRange
            ]);
            
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $fullRange);
            $values = $response->getValues();
            
            Log::info('Google Sheets API data fetched successfully', [
                'sheet' => $sheetName,
                'rows' => count($values)
            ]);
            
            return $values;
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch data from Google Sheets API', [
                'sheet' => $sheetName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getSheetNames()
    {
        try {
            $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
            $sheets = $spreadsheet->getSheets();
            
            $sheetNames = [];
            foreach ($sheets as $sheet) {
                $sheetNames[] = $sheet->getProperties()->getTitle();
            }
            
            Log::info('Retrieved sheet names from Google Sheets', [
                'sheets' => $sheetNames
            ]);
            
            return $sheetNames;
            
        } catch (\Exception $e) {
            Log::error('Failed to get sheet names from Google Sheets', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function testConnection()
    {
        try {
            $sheetNames = $this->getSheetNames();
            return [
                'success' => true,
                'sheets' => $sheetNames,
                'message' => 'Successfully connected to Google Sheets API'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to connect to Google Sheets API'
            ];
        }
    }
}

