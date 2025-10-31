<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Payment Creation ===\n\n";

$billplz = new \App\Services\BillplzService();

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
    echo "✅ Payment created successfully!\n";
    echo "Bill ID: " . $result['data']['id'] . "\n";
    echo "Payment URL: " . $result['data']['url'] . "\n";
    echo "\n✅ Everything is working! You can now use the test interface.\n";
} else {
    echo "❌ Failed to create payment\n";
    echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n";
}

echo "\n=== Test Complete ===\n";

