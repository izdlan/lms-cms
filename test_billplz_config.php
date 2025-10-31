<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Billplz Configuration Test ===\n\n";

echo "API Key: " . (config('billplz.api_key') ? 'CONFIGURED (' . substr(config('billplz.api_key'), 0, 20) . '...)' : 'MISSING') . "\n";
echo "Collection ID: " . (config('billplz.collection_id') ?: 'MISSING') . "\n";
echo "X-Signature Key: " . (config('billplz.x_signature_key') ? 'CONFIGURED' : 'MISSING') . "\n";
echo "Sandbox: " . (config('billplz.sandbox') ? 'true' : 'false') . "\n";
echo "Base URL: " . (config('billplz.sandbox') ? 'https://www.billplz-sandbox.com/api/v4' : 'https://www.billplz.com/api/v4') . "\n\n";

echo "Testing API connection...\n";
try {
    $billplz = new \App\Services\BillplzService();
    $result = $billplz->getWebhookRank();
    
    if ($result['success']) {
        echo "✅ Connected! Webhook rank: " . $result['data']['rank'] . "\n";
    } else {
        echo "❌ Connection failed: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";



