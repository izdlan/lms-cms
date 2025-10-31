<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Testing New Sandbox API Key ===\n\n";

// Test with the new API key
$apiKey = 'aec6ef1f-175c-485c-b544-bbd9e49b1562';
$baseUrl = 'https://www.billplz-sandbox.com/api/v4';

echo "API Key: " . $apiKey . "\n";
echo "Testing URL: " . $baseUrl . "/webhook_rank\n\n";

// Test authentication
echo "Testing authentication...\n";
$response = Http::withBasicAuth($apiKey, '')->get($baseUrl . '/webhook_rank');

echo "HTTP Status: " . $response->status() . "\n";
echo "Response: " . $response->body() . "\n\n";

if ($response->successful()) {
    echo "✅ SUCCESS! API key is valid!\n";
    $data = $response->json();
    if (isset($data['rank'])) {
        echo "Webhook Rank: " . $data['rank'] . "\n";
    }
} else {
    echo "❌ FAILED: Still getting authentication error\n";
    echo "Make sure you've added the API key to your .env file\n";
}

echo "\n=== Test Complete ===\n";

