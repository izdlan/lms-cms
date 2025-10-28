# 🧪 Billplz Sandbox Web Test Interface

## ✅ Setup Complete!

Your Billplz sandbox test interface is now ready to use!

---

## 🚀 How to Access

### 1. Make sure your credentials are in .env

Open your `.env` file and verify these lines exist:

```env
BILLPLZ_API_KEY=12fd2c2b-9b60-44e2-a8ef-0a5c899b9017
BILLPLZ_COLLECTION_ID=goth7gdc
BILLPLZ_X_SIGNATURE_KEY=bbd52bb79dc9bb1901c5dd5c3f52046e625c2cea3563dc836ca30ce76fa6dffe4fc9a9a5cbb7dc9c7a93d303b5f3ba6569ce418965d52c475658fcc459e5cb8e
BILLPLZ_SANDBOX=true
BILLPLZ_LOGGING=true
```

### 2. Clear config cache

```bash
php artisan config:clear
```

### 3. Start your Laravel server

```bash
php artisan serve
```

### 4. Open in browser

**Access the test page at:**

```
http://localhost:8000/billplz-test
```

Or if you're running on a different port:

```
http://localhost:YOUR_PORT/billplz-test
```

---

## 🎯 Features Available

### 1. ✅ Test Connection
- Tests your API key and connection to Billplz
- Shows your webhook rank
- Displays API version and environment

### 2. 📦 Test Collection
- Verifies your collection exists
- Shows collection details
- Checks collection status

### 3. 💳 Get Payment Gateways
- Lists all available payment methods
- Shows active gateways
- Displays bank codes for direct payment

### 4. 💰 Create Test Payment
- Create a real payment in sandbox
- Get a payment URL to test
- Use test credit cards

---

## 🧪 Testing the Payment Flow

### Step 1: Test Your Connection

Click the **"Test Connection"** button
- ✅ Should show: "Connected! Webhook rank: X.X"
- ❌ If error, check your API key in `.env`

### Step 2: Test Your Collection

Click the **"Test Collection"** button
- ✅ Should show your collection title and ID
- ❌ If error, check your collection ID

### Step 3: Create a Test Payment

1. Fill in the payment form (or use defaults):
   - Email: `test@example.com`
   - Name: `Test Student`
   - Amount: `10.00` (MYR)
   - Description: `Test Payment from Sandbox`

2. Click **"Create Test Payment"**

3. A payment URL will appear and open automatically

4. In the Billplz payment page:
   - Use test card: **4000 0000 0000 0002**
   - Any future expiry date (e.g., 12/2025)
   - Any 3-digit CVV (e.g., 123)
   - Name on card: Any name

5. Click **"Pay"**

6. You'll see a success page!

---

## 💳 Test Credit Cards

Use these cards in Billplz sandbox:

### Successful Payment
```
Card: 4000 0000 0000 0002
Expiry: Any future date
CVV: Any 3 digits
```

### Failed Payment
```
Card: 4000 0000 0000 0119
Expiry: Any future date
CVV: Any 3 digits
```

### Expired Card
```
Card: 4000 0000 0000 0069
Expiry: Any past date
CVV: Any 3 digits
```

---

## 📸 What to Expect

### Test Page Features:
- 🎨 Beautiful modern UI
- ✅ Real-time status updates
- 💰 Create payments instantly
- 🔗 Payment URLs that open automatically
- 📱 Mobile responsive

### After Creating Payment:
1. Billplz payment page opens in new tab
2. Select payment method (FPX/Card/etc.)
3. Complete payment
4. Redirected to success page
5. Check logs: `storage/logs/laravel.log`

---

## 🔍 Troubleshooting

### If connection test fails:

```bash
# Clear config cache
php artisan config:clear

# Check your .env file
cat .env | grep BILLPLZ

# Restart server
php artisan serve
```

### If payment creation fails:

1. Check logs: `storage/logs/laravel.log`
2. Verify API key is correct
3. Ensure collection ID exists in your account
4. Make sure you're in sandbox mode

### If webhook not receiving:

Webhooks are not needed for this test interface. The page just creates payments for testing. Webhook testing requires:
1. Using ngrok for local development
2. Configuring in Billplz dashboard
3. Testing the full payment flow

---

## 📊 What You Can Test

### ✅ Connection Tests
- API key authentication
- Sandbox environment
- Webhook rank

### ✅ Collection Tests
- Collection exists
- Collection status
- Collection configuration

### ✅ Payment Tests
- Create payments
- Get payment URLs
- Test payment methods
- Complete payment flow
- Test with different cards

### ✅ Integration Tests
- Test in your actual application
- Student bill payments
- Automatic status updates

---

## 🚦 Next Steps After Testing

### 1. Configure Webhooks (Optional)

For full integration:

1. Use ngrok for local testing:
   ```bash
   ngrok http 8000
   ```

2. Update Billplz dashboard webhooks:
   - Callback: `https://your-ngrok-url.ngrok.io/payment/billplz/callback`
   - Redirect: `https://your-ngrok-url.ngrok.io/payment/billplz/redirect`

3. Enable X-Signature in Billplz dashboard

### 2. Test in Your Application

Once sandbox tests pass:

1. Use the student payment flow in your LMS
2. Click "Pay Now" on a student bill
3. Complete payment in sandbox
4. Verify payment status updates automatically

### 3. Go to Production

When ready:

1. Get production API keys from Billplz
2. Update `.env`:
   ```env
   BILLPLZ_SANDBOX=false
   BILLPLZ_API_KEY=your-production-key
   BILLPLZ_COLLECTION_ID=your-production-collection
   ```
3. Configure production webhooks
4. Test with real payment (small amount)
5. Monitor logs and webhook performance

---

## 📝 Quick Commands

```bash
# Start server
php artisan serve

# Clear cache
php artisan config:clear

# View logs
tail -f storage/logs/laravel.log

# Access test page
# Open: http://localhost:8000/billplz-test
```

---

## 🎉 You're Ready!

**Access your test page now:**

```
http://localhost:8000/billplz-test
```

Start testing your Billplz sandbox integration! 🚀

---

**Note**: This test interface is for development only. Remove or protect these routes in production.

