# Billplz Payment Gateway Integration Setup Guide

This guide will help you set up Billplz payment gateway integration for your LMS system.

## Prerequisites

1. **Billplz Account**: Sign up at [https://www.billplz.com/](https://www.billplz.com/)
2. **Sandbox Testing**: Use [https://www.billplz-sandbox.com/](https://www.billplz-sandbox.com/) for testing

## Step 1: Get Your API Credentials

1. Log in to your Billplz dashboard
2. Go to **Settings** > **API Keys**
3. Copy your **API Key**
4. Go to **Collections** and create a new collection (or use existing)
5. Copy your **Collection ID**

## Step 2: Configure Environment Variables

Add these variables to your `.env` file:

```env
# Billplz Payment Gateway Configuration
BILLPLZ_API_KEY=your_api_key_here
BILLPLZ_COLLECTION_ID=your_collection_id_here
BILLPLZ_WEBHOOK_KEY=your_webhook_key_here
BILLPLZ_SANDBOX=true
BILLPLZ_LOGGING=true
BILLPLZ_CALLBACK_URL=/payment/billplz/callback
BILLPLZ_REDIRECT_URL=/payment/billplz/redirect
```

## Step 3: Run Database Migration

```bash
php artisan migrate
```

This will create the `payments` table to track payment transactions.

## Step 4: Set Up Webhooks

### For Local Development (Before Deployment)

#### Option A: Using ngrok (Recommended)
1. Install ngrok: [https://ngrok.com/](https://ngrok.com/)
2. Start your Laravel server: `php artisan serve`
3. In another terminal: `ngrok http 8000`
4. Copy the ngrok URL (e.g., `https://abc123.ngrok.io`)
5. In Billplz dashboard, set webhook URL to: `https://abc123.ngrok.io/payment/billplz/callback`

#### Option B: Using localhost (Basic Testing)
1. In Billplz dashboard, set webhook URL to: `http://127.0.0.1:8000/payment/billplz/callback`
2. Update your `.env` file:
   ```env
   BILLPLZ_CALLBACK_URL=http://127.0.0.1:8000/payment/billplz/callback
   BILLPLZ_REDIRECT_URL=http://127.0.0.1:8000/payment/billplz/redirect
   ```

### For Production (After Deployment)
1. In your Billplz dashboard, go to **Settings** > **Webhooks**
2. Set the webhook URL to: `https://yourdomain.com/payment/billplz/callback`
3. Copy the webhook verification key and add it to your `.env` file as `BILLPLZ_WEBHOOK_KEY`

## Step 5: Test the Integration

### Test in Sandbox Mode

1. Make sure `BILLPLZ_SANDBOX=true` in your `.env` file
2. Use test cards from Billplz documentation
3. Test the payment flow:
   - Create a payment
   - Complete payment in Billplz
   - Check payment status in your system

### Testing Without Webhooks (Local Development)

If you can't set up webhooks locally, you can still test the payment flow:

1. **Test Payment Creation:**
   - Student clicks "Pay Now" on a bill
   - Should redirect to Billplz payment page
   - Payment page should load correctly

2. **Test Payment Completion:**
   - Complete payment in Billplz sandbox
   - You'll be redirected back to your success page
   - Check your database to see if payment was recorded

3. **Manual Status Check:**
   - Use the admin panel to check payment status
   - Or create a simple test route to check payment status

### Test Cards (Sandbox)

- **Successful Payment**: 4000 0000 0000 0002
- **Failed Payment**: 4000 0000 0000 0119
- **Expired Card**: 4000 0000 0000 0069

## Step 6: Go Live

1. Apply for production access in Billplz dashboard
2. Set `BILLPLZ_SANDBOX=false` in your `.env` file
3. Update webhook URLs to production URLs
4. Test with real payment methods

## Features Included

### Payment Types
- **Course Fees**: Payment for specific courses
- **General Payments**: Any other payment type
- **Assignment Fees**: Payment for assignments (extensible)

### Payment Methods Supported
- **FPX Online Banking**: All major Malaysian banks
- **Credit/Debit Cards**: Visa, Mastercard
- **E-wallets**: GrabPay, Boost, etc.

### Features
- Real-time payment status updates
- Webhook integration for instant notifications
- Payment history tracking
- Automatic payment URL generation
- Secure webhook verification
- Payment expiration handling

## Usage Examples

### Create Course Payment (JavaScript)

```javascript
fetch('/student/payments/course', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        course_id: 1,
        amount: 100.00,
        description: 'Course fee for Introduction to Programming'
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        window.open(data.payment_url, '_blank');
    }
});
```

### Create General Payment (JavaScript)

```javascript
fetch('/student/payments/general', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        amount: 50.00,
        description: 'Library fine payment',
        reference: 'LIB-2024-001'
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        window.open(data.payment_url, '_blank');
    }
});
```

## Security Notes

1. **Webhook Verification**: Always verify webhook signatures
2. **HTTPS Required**: Use HTTPS in production
3. **API Key Security**: Keep your API keys secure
4. **Input Validation**: Validate all payment data
5. **Logging**: Monitor payment logs for suspicious activity

## Troubleshooting

### Common Issues

1. **Webhook Not Working**: Check URL and webhook key
2. **Payment Not Updating**: Verify webhook endpoint is accessible
3. **Sandbox Issues**: Ensure you're using sandbox API keys
4. **Collection Issues**: Verify collection ID is correct

### Debug Mode

Enable logging by setting `BILLPLZ_LOGGING=true` in your `.env` file. Check `storage/logs/laravel.log` for detailed logs.

## Support

- **Billplz Documentation**: [https://www.billplz.com/api](https://www.billplz.com/api)
- **Billplz Support**: Contact through their dashboard
- **Laravel Documentation**: [https://laravel.com/docs](https://laravel.com/docs)

## File Structure

```
app/
├── Http/Controllers/PaymentController.php
├── Models/Payment.php
├── Services/BillplzService.php
config/
├── billplz.php
database/migrations/
├── create_payments_table.php
resources/views/student/
├── payments.blade.php
├── payment-success.blade.php
├── payment-failed.blade.php
└── payment-pending.blade.php
```
