# ✅ Your Billplz Setup - Next Steps

## 1. Update Your .env File

Add these lines to your `.env` file in the root directory:

```env
BILLPLZ_API_KEY=12fd2c2b-9b60-44e2-a8ef-0a5c899b9017
BILLPLZ_COLLECTION_ID=goth7gdc
BILLPLZ_X_SIGNATURE_KEY=bbd52bb79dc9bb1901c5dd5c3f52046e625c2cea3563dc836ca30ce76fa6dffe4fc9a9a5cbb7dc9c7a93d303b5f3ba6569ce418965d52c475658fcc459e5cb8e
BILLPLZ_SANDBOX=true
BILLPLZ_LOGGING=true
```

---

## 2. Test Your Connection

Run this command to test if everything is configured correctly:

```bash
php artisan tinker
```

Then in tinker, run:

```php
$billplz = new App\Services\BillplzService();

// Test API connection
$result = $billplz->getWebhookRank();
if ($result['success']) {
    echo "✅ Connection successful! Your webhook rank: " . $result['data']['rank'];
} else {
    echo "❌ Error: " . $result['error'];
}

// Test getting your collection
$result = $billplz->getCollection($collectionId = 'goth7gdc');
if ($result['success']) {
    echo "✅ Collection found: " . $result['data']['title'];
} else {
    echo "❌ Collection error: " . $result['error'];
}
```

---

## 3. Configure Webhooks in Billplz Dashboard

### Important: You MUST configure webhooks for the system to work!

1. **Log in** to https://www.billplz-sandbox.com/
2. Go to **Settings** → **Webhooks**
3. Configure these URLs:

   **For Local Development:**
   - If using ngrok: `https://your-ngrok-url.ngrok.io/payment/billplz/callback`
   - If testing locally: leave as default for now

   **For Production:**
   - Callback URL: `https://yourdomain.com/payment/billplz/callback`
   - Redirect URL: `https://yourdomain.com/payment/billplz/redirect`

4. **Enable X-Signature** for both callback and redirect

---

## 4. Create Your First Test Payment

Test the payment system:

```php
$billplz = new App\Services\BillplzService();

$result = $billplz->createBill([
    'email' => 'test@example.com',
    'name' => 'Test Student',
    'mobile' => '0123456789',
    'amount' => 10.00,
    'description' => 'Test Payment',
    'reference_1' => 'TEST001',
]);

if ($result['success']) {
    echo "✅ Payment URL: " . $result['data']['url'];
    // Open this URL in browser to test
} else {
    echo "❌ Error: " . $result['error'];
}
```

---

## 5. Test Payment Flow

1. **Create the payment** using the code above
2. **Copy the payment URL** from the output
3. **Open the URL** in your browser
4. **Use test card**: `4000 0000 0000 0002` (for successful payment)
5. **Complete the payment**
6. **Check your logs**: `storage/logs/laravel.log`

---

## 6. Test in Your Application

If you have student bills system, you can test from the UI:

1. Log in as a student
2. Go to Bills/Payments
3. Click "Pay Now" on any bill
4. Should redirect to Billplz payment page
5. Complete payment
6. Check if bill status updates

---

## Troubleshooting

### If connection fails:
```bash
# Clear config cache
php artisan config:clear

# Test again
php artisan tinker
```

### If webhook not receiving:
1. Check Billplz dashboard webhook settings
2. Verify URLs are correct
3. Check `storage/logs/laravel.log`
4. Make sure X-Signature is enabled

### If payment not updating:
1. Check webhook endpoint is accessible
2. Verify X-Signature verification in logs
3. Check payment record in database

---

## Your Credentials Summary

✅ **API Key**: `12fd2c2b-9b60-44e2-a8ef-0a5c899b9017`  
✅ **Collection ID**: `goth7gdc`  
✅ **X-Signature Key**: Configured  
✅ **Sandbox Mode**: Enabled  
✅ **Logging**: Enabled  

---

## Quick Test Script

Save this as `test_billplz.php` in your project root:

```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Billplz Connection...\n\n";

$billplz = new App\Services\BillplzService();

// Test webhook rank
echo "1. Testing API connection...\n";
$result = $billplz->getWebhookRank();
if ($result['success']) {
    echo "   ✅ Connected! Rank: {$result['data']['rank']}\n";
} else {
    echo "   ❌ Failed: {$result['error']}\n";
    exit(1);
}

// Test collection
echo "\n2. Testing collection...\n";
$result = $billplz->getCollection('goth7gdc');
if ($result['success']) {
    echo "   ✅ Collection found: {$result['data']['title']}\n";
} else {
    echo "   ❌ Failed: {$result['error']}\n";
}

// Test creating a bill
echo "\n3. Creating test bill...\n";
$result = $billplz->createBill([
    'email' => 'test@example.com',
    'name' => 'Test User',
    'amount' => 10.00,
    'description' => 'Test Payment',
]);

if ($result['success']) {
    echo "   ✅ Bill created!\n";
    echo "   Payment URL: {$result['data']['url']}\n";
} else {
    echo "   ❌ Failed: {$result['error']}\n";
}

echo "\n✅ All tests complete!\n";
```

Run it:

```bash
php test_billplz.php
```

---

## Next: Go Live Checklist

When ready for production:

1. [ ] Apply for Billplz production access
2. [ ] Get production API key
3. [ ] Set `BILLPLZ_SANDBOX=false`
4. [ ] Update webhook URLs to production domain
5. [ ] Test with real payment (small amount)
6. [ ] Monitor logs and webhook performance
7. [ ] Set up collection receipt preferences

---

**You're all set!** 🎉

Start testing your Billplz integration now!



