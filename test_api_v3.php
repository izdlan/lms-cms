<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = 'aec6ef1f-175c-485c-b544-bbd9e49b1562';
$collectionId = 'ommjyl0o';

echo "=== Testing API v3 vs v4 ===\n\n";

// Test v3
echo "Testing API v3...\n";
$response = Http::withBasicAuth($apiKey, '')
    ->post('https://www.billplz-sandbox.com/api/v3/bills', [
        'collection_id' => $collectionId,
        'email' => 'test@example.com',
        'name' => 'Test',
        'amount' => 1000,
        'description' => 'Test',
    ]);

echo "v3 Status: " . $response->status() . "\n";
if ($response->successful()) {
    echo "✅ v3 works! Response: " . substr($response->body(), 0, 200) . "\n";
} else {
    echo "❌ v3 failed\n";
}

echo "\nTesting API v4...\n";
$response2 = Http::withBasicAuth($apiKey, '')
    ->post('https://www.billplz-sandbox.com/api/v4/bills', [
        'collection_id' => $collectionId,
        'email' => 'test@example.com',
        'name' => 'Test',
        'amount' => 1000,
        'description' => 'Test',
    ]);

echo "v4 Status: " . $response2->status() . "\n";
if ($response2->successful()) {
    echo "✅ v4 works! Response: " . substr($response2->body(), 0, 200) . "\n";
} else {
    echo "❌ v4 failed\n";
    echo "Response: " . substr($response2->body(), 0, 200) . "\n";
}

echo "\n=== Test Complete ===\n";

