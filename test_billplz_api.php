<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Billplz API Direct Test ===\n\n";

$apiKey = config('billplz.api_key');
$sandbox = config('billplz.sandbox', true);
$baseUrl = $sandbox ? 'https://www.billplz-sandbox.com/api/v4' : 'https://www.billplz.com/api/v4';

echo "API Key: " . $apiKey . "\n";
echo "Base URL: " . $baseUrl . "\n\n";

// Test 1: Check authentication
echo "Test 1: Testing authentication...\n";
$response = Http::withBasicAuth($apiKey, '')->get($baseUrl . '/webhook_rank');

echo "HTTP Status: " . $response->status() . "\n";
echo "Response: " . $response->body() . "\n\n";

// Test 2: Try getting collection
echo "Test 2: Testing collection access...\n";
$collectionId = config('billplz.collection_id');
$response2 = Http::withBasicAuth($apiKey, '')->get($baseUrl . '/collections/' . $collectionId);

echo "HTTP Status: " . $response2->status() . "\n";
echo "Response: " . substr($response2->body(), 0, 200) . "\n\n";

echo "=== Test Complete ===\n";



