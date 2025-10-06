<?php

namespace App\Console\Commands;

use App\Services\BillplzService;
use Illuminate\Console\Command;

class TestBillplzConnection extends Command
{
    protected $signature = 'billplz:test';
    protected $description = 'Test Billplz API connection and configuration';

    public function handle()
    {
        $this->info('Testing Billplz API connection...');
        
        // Check configuration
        $this->info('Checking configuration...');
        $apiKey = config('billplz.api_key');
        $collectionId = config('billplz.collection_id');
        $sandbox = config('billplz.sandbox');
        
        if (empty($apiKey)) {
            $this->error('❌ BILLPLZ_API_KEY is not set in .env file');
            return 1;
        }
        
        if (empty($collectionId)) {
            $this->error('❌ BILLPLZ_COLLECTION_ID is not set in .env file');
            return 1;
        }
        
        $this->info("✅ API Key: " . substr($apiKey, 0, 8) . "...");
        $this->info("✅ Collection ID: {$collectionId}");
        $this->info("✅ Sandbox Mode: " . ($sandbox ? 'Yes' : 'No'));
        
        // Test API connection
        $this->info('Testing API connection...');
        $billplzService = new BillplzService();
        
        // Test getting collection details
        $result = $billplzService->getCollection();
        
        if ($result['success']) {
            $this->info('✅ API connection successful!');
            $collection = $result['data'];
            $this->info("Collection Title: {$collection['title']}");
            $this->info("Collection Status: {$collection['state']}");
        } else {
            $this->error('❌ API connection failed: ' . $result['error']);
            return 1;
        }
        
        // Test creating a sample bill (optional)
        if ($this->confirm('Do you want to test creating a sample bill?', false)) {
            $this->info('Creating sample bill...');
            
            $sampleData = [
                'email' => 'test@example.com',
                'name' => 'Test User',
                'amount' => 1.00,
                'description' => 'Test payment from Laravel command',
                'reference_1' => 'TEST-001',
                'reference_2' => 'Command Test',
            ];
            
            $billResult = $billplzService->createBill($sampleData);
            
            if ($billResult['success']) {
                $this->info('✅ Sample bill created successfully!');
                $bill = $billResult['data'];
                $this->info("Bill ID: {$bill['id']}");
                $this->info("Payment URL: {$bill['url']}");
                $this->warn('Note: This is a test bill and will expire in 30 minutes.');
            } else {
                $this->error('❌ Failed to create sample bill: ' . $billResult['error']);
            }
        }
        
        $this->info('Billplz integration test completed!');
        return 0;
    }
}