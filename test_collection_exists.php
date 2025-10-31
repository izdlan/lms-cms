<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('billplz.api_key');
$collectionId = config('billplz.collection_id');
$baseUrl = 'https://www.billplz-sandbox.com/api/v4';

echo "=== Testing Collection ===\n\n";
echo "Collection ID from config: " . $collectionId . "\n\n";

// Test getting the collection
echo "Testing collection: " . $collectionId . "\n";
$response = Http::withBasicAuth($apiKey, '')
    ->get($baseUrl . '/collections/' . $collectionId);

echo "HTTP Status: " . $response->status() . "\n";
echo "Response: " . $response->body() . "\n\n";

if ($response->successful()) {
    echo "✅ Collection exists!\n";
    $data = $response->json();
    echo "Collection Title: " . ($data['title'] ?? 'N/A') . "\n";
} else {
    echo "❌ Collection not found!\n";
    echo "Please verify the Collection ID in your Billplz dashboard.\n";
    echo "Common typos: 0 (zero) vs O (letter), 1 (one) vs l (lowercase L)\n";
}

echo "\n=== Test Complete ===\n";

