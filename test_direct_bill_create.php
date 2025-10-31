<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = 'aec6ef1f-175c-485c-b544-bbd9e49b1562';
$collectionId = 'ommjyl0o';
$baseUrl = 'https://www.billplz-sandbox.com/api/v4';

echo "=== Direct Bill Creation Test ===\n\n";

$postData = [
    'collection_id' => $collectionId,
    'email' => 'test@example.com',
    'name' => 'Test Student',
    'amount' => 1000, // RM 10 in cents
    'description' => 'Test Payment',
];

echo "Creating bill with data:\n";
print_r($postData);
echo "\n";

$response = Http::withBasicAuth($apiKey, '')
    ->post($baseUrl . '/bills', $postData);

echo "HTTP Status: " . $response->status() . "\n";
echo "Response: " . $response->body() . "\n\n";

if ($response->successful()) {
    echo "✅ Bill created successfully!\n";
    $data = $response->json();
    echo "Bill ID: " . ($data['id'] ?? 'N/A') . "\n";
    echo "Bill URL: " . ($data['url'] ?? 'N/A') . "\n";
} else {
    echo "❌ Failed to create bill\n";
    $error = $response->json();
    if (isset($error['error'])) {
        if (is_array($error['error'])) {
            echo "Error: " . ($error['error']['message'] ?? json_encode($error['error'])) . "\n";
        } else {
            echo "Error: " . $error['error'] . "\n";
        }
    }
}

echo "\n=== Test Complete ===\n";

