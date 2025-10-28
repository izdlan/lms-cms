# Billplz Payment Gateway Integration Guide - API v4

## Overview

This guide provides setup instructions for integrating Billplz payment gateway with your LMS system using the latest **Billplz API v4**.

## Key Features Implemented

✅ **API v4 Support** - Updated to latest Billplz API version  
✅ **X-Signature Verification** - Secure webhook verification  
✅ **Direct Payment Gateway** - Bypass payment selection page  
✅ **Sandbox & Production** - Support for both environments  
✅ **Accounting Integration** - Automatic payment sync  

## Step 1: Get Your Billplz Credentials

1. **Register for Billplz**: 
   - Sandbox: https://www.billplz-sandbox.com/
   - Production: https://www.billplz.com/

2. **Get API Credentials**:
   - Log in to your Billplz dashboard
   - Go to **Settings** > **API Keys**
   - Copy your **API Secret Key** (e.g., `73eb57f0-7d4e-42b9-a544-aeac6e4b0f81`)

3. **Create a Collection**:
   - Go to **Collections** in your dashboard
   - Click **Create New Collection**
   - Give it a name (e.g., "Tuition Fees")
   - Copy the **Collection ID** (e.g., `inbmmepb`)

4. **Get X-Signature Key** (for webhook security):
   - Go to **Settings** > **Webhooks**
   - Enable X-Signature callback
   - Copy the X-Signature Key

## Step 2: Configure Environment Variables

Add these to your `.env` file:

```env
# Billplz API v4 Configuration
BILLPLZ_API_KEY=73eb57f0-7d4e-42b9-a544-aeac6e4b0f81
BILLPLZ_COLLECTION_ID=inbmmepb
BILLPLZ_X_SIGNATURE_KEY=your-x-signature-key-here

# Environment
BILLPLZ_SANDBOX=true  # Set to false for production

# URLs (will be auto-configured)
BILLPLZ_CALLBACK_URL=/payment/billplz/callback
BILLPLZ_REDIRECT_URL=/payment/billplz/redirect

# Logging
BILLPLZ_LOGGING=true
```

### Important Notes:

1. **Sandbox vs Production**:
   - Sandbox endpoint: `https://www.billplz-sandbox.com/api/v4`
   - Production endpoint: `https://www.billplz.com/api/v4`
   - **USE SEPARATE ACCOUNTS** for sandbox and production

2. **MYR Currency Only**:
   - Billplz only accepts Malaysian Ringgit (MYR)
   - Amounts are converted to cents automatically (e.g., RM 2.00 = 200 cents)

## Step 3: Set Up Webhooks

### For Local Development (using ngrok)

1. Start your Laravel server:
   ```bash
   php artisan serve
   ```

2. Start ngrok in another terminal:
   ```bash
   ngrok http 8000
   ```

3. Copy the ngrok URL (e.g., `https://abc123.ngrok.io`)

4. In Billplz dashboard → Settings → Webhooks:
   - Callback URL: `https://abc123.ngrok.io/payment/billplz/callback`
   - Enable **X-Signature Callback**
   - Enable **X-Signature Redirect** (for redirect verification)

### For Production

1. In Billplz dashboard → Settings → Webhooks:
   - Callback URL: `https://yourdomain.com/payment/billplz/callback`
   - Redirect URL: `https://yourdomain.com/payment/billplz/redirect`
   - Enable X-Signature for both

## Step 4: X-Signature Webhook Security

X-Signature is Billplz's secure webhook verification method:

```
X-Signature = SHA256(http_build_query(data) + X-Signature-Key)
```

This is already implemented in `BillplzService::verifyWebhook()`

## Step 5: Payment Flow

### Normal Flow (with redirect)

1. Student clicks "Pay Now"
2. System creates Bill via API
3. Student redirected to Billplz payment page
4. Student selects payment method and pays
5. **Callback** (server-side): Billplz sends webhook to your server
6. **Redirect** (client-side): Student redirected back to your success page

### Direct Payment Gateway (bypass selection page)

To bypass the payment selection page:

```php
// In BillplzService
$billData = $this->createBill([
    'email' => $student->email,
    'name' => $student->name,
    'amount' => 100.00,
    'description' => 'Course Fee',
    'bank_code' => 'BP-FKR01', // Get code from /payment_gateways API
]);

// If bank_code is provided, direct_url will be available
$paymentUrl = $billData['direct_url'] ?? $billData['url'];
```

The `direct_url` automatically includes `?auto_submit=true` parameter.

## Step 6: Get Payment Gateway Bank Codes

To get available payment gateways and their codes:

```php
$gateways = $billplzService->getPaymentGateways();
// Returns list of gateways with their bank codes
```

Or via API directly:

```bash
curl https://www.billplz.com/api/v4/payment_gateways \
  -u your-api-key:
```

## Step 7: Testing

### Test Cards (Sandbox)

- **Successful Payment**: 4000 0000 0000 0002
- **Failed Payment**: 4000 0000 0000 0119
- **Expired Card**: 4000 0000 0000 0069

### Test Flow

1. Make sure `BILLPLZ_SANDBOX=true` in `.env`
2. Create a payment
3. Use test card above
4. Check logs: `storage/logs/laravel.log`

## API Authentication

Billplz uses HTTP Basic Auth. Your API key is passed as username:

```bash
# Test authentication
curl https://www.billplz.com/api/v4/webhook_rank \
  -u 73eb57f0-7d4e-42b9-a544-aeac6e4b0f81:
```

If authenticated, you should not see "Unauthorized" response.

## Integration Code Examples

### Create a Bill with Direct Payment

```php
use App\Services\BillplzService;

$billplz = new BillplzService();

$result = $billplz->createBill([
    'email' => 'student@example.com',
    'name' => 'Ahmad Ali',
    'mobile' => '0123456789',
    'amount' => 200.00, // Will be converted to 20000 cents
    'description' => 'Course Fee - Introduction to Programming',
    'bank_code' => 'BP-FKR01', // Optional - for direct payment
]);

if ($result['success']) {
    $bill = $result['data'];
    echo "Bill URL: " . $bill['url'];
    
    // Use direct_url if bank_code was provided
    if (isset($bill['direct_url'])) {
        echo "Direct URL: " . $bill['direct_url'];
    }
}
```

### Handle Webhook Callback

```php
// This is already implemented in PaymentController::billplzCallback()
// It automatically:
// 1. Verifies X-Signature
// 2. Updates payment status
// 3. Marks related bill as paid
// 4. Syncs to accounting system
```

### Get Payment Status

```php
$result = $billplz->getBillStatus($billId);
if ($result['success']) {
    $status = $result['data']['state']; // 'paid', 'pending', 'cancelled'
}
```

## Security Best Practices

1. **Always verify X-Signature** - Never skip webhook verification
2. **Use HTTPS** - Billplz requires HTTPS for production
3. **Keep API keys secure** - Never commit to git
4. **Validate amounts** - Always validate payment amounts
5. **Log everything** - Enable `BILLPLZ_LOGGING=true`

## Troubleshooting

### Webhook Not Receiving

1. Check Billplz dashboard webhook settings
2. Verify your server is accessible (use ngrok for local dev)
3. Check X-Signature key in `.env`
4. Look at logs: `tail -f storage/logs/laravel.log`

### Payment Not Updating

1. Verify webhook endpoint is accessible
2. Check X-Signature verification logs
3. Ensure callback_url and redirect_url are correct
4. Check payment record in database

### API Errors

1. Verify API key is correct
2. Check you're using correct environment (sandbox vs production)
3. Ensure collection_id exists in your account
4. Verify amount is in cents (multiply by 100)

## New Features in API v4

### Split Payments Support

Collections now support up to **2 split payment recipients**. Perfect for revenue sharing:

```php
$result = $billplz->createCollection("Tuition Fees", [
    [
        'email' => 'school@example.com',
        'fixed_cut' => 10000,  // RM 100 in cents
        'variable_cut' => 2,   // 2% percentage
        'stack_order' => 0
    ],
    [
        'email' => 'platform@example.com',
        'variable_cut' => 5,   // 5% percentage
        'stack_order' => 1
    ]
], true); // true = show split header on templates
```

### Open Collections (Payment Forms)

Create payment forms that customers can use directly:

```php
$result = $billplz->createOpenCollection(
    'Course Registration Fee',
    'Payment for course registration',
    10000, // RM 100
    [
        'fixed_amount' => true,
        'fixed_quantity' => true,
        'payment_button' => 'pay',
        'tax' => 6, // 6% tax
        'redirect_uri' => 'https://yourdomain.com/success'
    ]
);

$formUrl = $result['data']['url']; // Share this URL with customers
```

### Customer Receipt Control

Control when customers receive receipts:

```php
// Always send receipt
$billplz->controlCustomerReceiptDelivery($collectionId, 'activate');

// Never send receipt
$billplz->controlCustomerReceiptDelivery($collectionId, 'deactivate');

// Follow global settings (default)
$billplz->controlCustomerReceiptDelivery($collectionId, 'global');

// Check current status
$status = $billplz->getCustomerReceiptDeliveryStatus($collectionId);
```

### Webhook Performance Monitoring

Check your webhook rank (0.0 = best, 10.0 = needs improvement):

```php
$rank = $billplz->getWebhookRank();
echo "Your webhook rank: " . $rank['data']['rank'];
// Rank resets daily at 17:00
```

## Updated Files

The following files have been updated for API v4:

- `app/Services/BillplzService.php` - Updated to API v4 with:
  - Split payments support (up to 2 recipients)
  - Open collections (payment forms)
  - Customer receipt delivery control
  - Webhook rank monitoring
  - X-Signature verification
  - Direct payment gateway support
- `app/Http/Controllers/PaymentController.php` - Enhanced webhook handling
- `config/billplz.php` - Added X-Signature key configuration

## Support & Resources

- **Billplz Documentation**: https://www.billplz.com/api
- **GitHub Integration Code**: https://github.com/billplz
- **Knowledge Base**: https://help.billplz.com/article/53-list-of-system-with-production-ready-integration
- **Billplz Support**: Contact through dashboard

## Next Steps

1. Update `.env` with your credentials
2. Configure webhooks in Billplz dashboard
3. Test with sandbox
4. Apply for production access
5. Deploy to production

---

**Note**: Integration with callback_url is **compulsory**. Use redirect_url for faster status updates and better user experience.

