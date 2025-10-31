<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Final Payment Test ===\n\n";

$billplz = new \App\Services\BillplzService();

echo "Collection ID: " . config('billplz.collection_id') . "\n";
echo "Sandbox: " . (config('billplz.sandbox') ? 'true' : 'false') . "\n\n";

// Test creating a bill
echo "Creating test payment...\n";
$result = $billplz->createBill([
    'email' => 'test@example.com',
    'name' => 'Test Student',
    'mobile' => '0123456789',
    'amount' => 10.00,
    'description' => 'Test Payment',
    'reference_1' => 'TEST-' . time(),
]);

if ($result['success']) {
    echo "\n✅ SUCCESS! Payment created!\n\n";
    echo "Bill ID: " . $result['data']['id'] . "\n";
    echo "Payment URL: " . $result['data']['url'] . "\n";
    echo "\n✅ Everything is working! You can now use the test interface at:\n";
    echo "http://localhost:8000/billplz-test\n";
} else {
    echo "\n❌ Failed to create payment\n";
    echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    if (isset($result['http_status'])) {
        echo "HTTP Status: " . $result['http_status'] . "\n";
    }
    if (isset($result['response'])) {
        echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    }
}

echo "\n=== Test Complete ===\n";

