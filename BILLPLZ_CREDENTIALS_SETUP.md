# Billplz Credentials Setup Guide

You have your API Key. Now you need to complete the setup by getting your other credentials.

## Your Current Credentials

✅ **API Key**: `12fd2c2b-9b60-44e2-a8ef-0a5c899b9017`

---

## Step-by-Step Setup

### 1. Add to .env File

Open your `.env` file and add these lines:

```env
# Billplz API v4 Configuration
BILLPLZ_API_KEY=12fd2c2b-9b60-44e2-a8ef-0a5c899b9017
BILLPLZ_COLLECTION_ID=your-collection-id-here
BILLPLZ_X_SIGNATURE_KEY=your-x-signature-key-here

# Environment
BILLPLZ_SANDBOX=true
BILLPLZ_LOGGING=true

# URLs (auto-configured)
BILLPLZ_CALLBACK_URL=/payment/billplz/callback
BILLPLZ_REDIRECT_URL=/payment/billplz/redirect
```

---

## 2. Get Collection ID

### Option A: Use Existing Collection

If you already have a collection in Billplz:
1. Log in to https://www.billplz.com/ (or sandbox at https://www.billplz-sandbox.com/)
2. Go to **Collections**
3. Copy the **Collection ID**
4. Add it to `.env` as `BILLPLZ_COLLECTION_ID`

### Option B: Create New Collection

You can create a new collection programmatically:

```bash
# Run this in your Laravel tinker
php artisan tinker
```

Then in tinker:

```php
$billplz = new App\Services\BillplzService();

// Create a simple collection
$result = $billplz->createCollection("Tuition Fees");

if ($result['success']) {
    echo "Collection ID: " . $result['data']['id'];
    // Copy this ID to your .env file
}

// Or create with split payments
$result = $billplz->createCollection("Tuition Fees", [
    [
        'email' => 'accounting@yourschool.com',
        'variable_cut' => 100,  // 100% (all goes to you)
        'stack_order' => 0
    ]
]);
```

---

## 3. Get X-Signature Key

X-Signature is required for secure webhook verification.

### Steps:

1. Log in to your Billplz dashboard
2. Go to **Settings** → **Webhooks**
3. Look for **X-Signature** section
4. You'll find the X-Signature key displayed
5. Copy it and add to `.env` as `BILLPLZ_X_SIGNATURE_KEY`

**Note**: If you don't see X-Signature in your account, you may need to enable it first. Some features require a paid plan.

**Alternative for Development**: If X-Signature is not available, the system will handle it gracefully in sandbox mode, but you **MUST** enable it for production.

---

## 4. Configure Webhooks

In your Billplz dashboard:

### Development (Local with ngrok):

1. Start ngrok:
   ```bash
   ngrok http 8000
   ```

2. Copy the ngrok URL (e.g., `https://abc123.ngrok.io`)

3. In Billplz dashboard → Settings → Webhooks:
   - **Callback URL**: `https://abc123.ngrok.io/payment/billplz/callback`
   - **Redirect URL**: `https://abc123.ngrok.io/payment/billplz/redirect`
   - Enable **X-Signature Callback**
   - Enable **X-Signature Redirect**

### Production:

1. Update your `.env`:
   ```env
   BILLPLZ_SANDBOX=false
   ```

2. In Billplz dashboard → Settings → Webhooks:
   - **Callback URL**: `https://yourdomain.com/payment/billplz/callback`
   - **Redirect URL**: `https://yourdomain.com/payment/billplz/redirect`
   - Enable **X-Signature Callback**
   - Enable **X-Signature Redirect**

---

## 5. Test Your Connection

After adding credentials to `.env`:

```bash
php artisan tinker
```

Then test:

```php
$billplz = new App\Services\BillplzService();

// Test webhook rank (confirms API key works)
$result = $billplz->getWebhookRank();
if ($result['success']) {
    echo "✅ API connection successful!";
    echo "Rank: " . $result['data']['rank'];
} else {
    echo "❌ Error: " . $result['error'];
}

// Test getting collections
$result = $billplz->getCollectionIndex();
if ($result['success']) {
    echo "Collections: " . count($result['data']['collections']);
}
```

---

## 6. Create Your First Test Payment

```php
$billplz = new App\Services\BillplzService();

$result = $billplz->createBill([
    'email' => 'test@example.com',
    'name' => 'Test Student',
    'mobile' => '0123456789',
    'amount' => 10.00,  // RM 10 test payment
    'description' => 'Test Payment',
]);

if ($result['success']) {
    echo "Payment URL: " . $result['data']['url'];
    // Visit this URL to test payment
}
```

---

## Quick Checklist

- [ ] Add API key to `.env`
- [ ] Get Collection ID (create or use existing)
- [ ] Add Collection ID to `.env`
- [ ] Get X-Signature key from Billplz
- [ ] Add X-Signature key to `.env`
- [ ] Configure webhooks in Billplz dashboard
- [ ] Test connection with `php artisan tinker`
- [ ] Create test payment
- [ ] Verify webhook callback

---

## Need Help?

### Common Issues:

**1. "Invalid API Key"**
- Check you're using correct key for sandbox vs production
- Sandbox keys only work at https://www.billplz-sandbox.com/
- Production keys only work at https://www.billplz.com/

**2. "Collection not found"**
- Verify Collection ID is correct
- Ensure it exists in your account
- Check sandbox vs production accounts are separate

**3. "X-Signature verification failed"**
- Check X-Signature key is correct
- Ensure it's enabled in Billplz dashboard
- Verify webhook URL is accessible

**4. "Webhook not receiving"**
- Check your server is accessible
- Use ngrok for local development
- Verify webhook URL in Billplz matches your `.env`
- Check `storage/logs/laravel.log` for errors

---

## Next Steps

Once you have all credentials:

1. ✅ Test payment creation
2. ✅ Test payment completion
3. ✅ Verify webhook receives callbacks
4. ✅ Check payment status updates
5. ✅ Test with sandbox cards
6. ✅ Ready for production

---

**Support**: Check the detailed guides:
- `BILLPLZ_INTEGRATION_GUIDE_V4.md` - Full setup guide
- `BILLPLZ_V4_QUICK_REFERENCE.md` - Code examples
- `BILLPLZ_V4_IMPLEMENTATION_SUMMARY.md` - Overview



