# Billplz API v4 - Quick Reference Guide

## Complete Implementation Summary

Your Billplz integration has been fully updated to support **API v4** with all new features. This document provides quick examples for immediate use.

---

## 1. Environment Setup

Add to your `.env` file:

```env
# Billplz API v4 Configuration
BILLPLZ_API_KEY=your-api-key-here
BILLPLZ_COLLECTION_ID=your-collection-id
BILLPLZ_X_SIGNATURE_KEY=your-x-signature-key
BILLPLZ_SANDBOX=true
BILLPLZ_LOGGING=true
```

---

## 2. Create Collections with Split Payments

### Basic Collection

```php
use App\Services\BillplzService;

$billplz = new BillplzService();

$result = $billplz->createCollection("Tuition Fees - January 2025");

if ($result['success']) {
    $collectionId = $result['data']['id'];
    echo "Collection ID: $collectionId";
}
```

### Collection with Split Payments (Revenue Sharing)

```php
$result = $billplz->createCollection("Tuition Fees", [
    [
        'email' => 'school@example.com',
        'fixed_cut' => 50000,        // RM 500 fixed
        'stack_order' => 0
    ],
    [
        'email' => 'platform@example.com',
        'variable_cut' => 3,        // 3% commission
        'stack_order' => 1
    ]
], true); // Show split header on templates

if ($result['success']) {
    echo "Collection created: " . $result['data']['id'];
}
```

**Split Payment Rules:**
- `fixed_cut`: Fixed amount in cents (e.g., 10000 = RM 100)
- `variable_cut`: Percentage cut (e.g., 2 = 2%)
- `stack_order`: Priority order (0 = first, 1 = second)
- **Maximum 2 split recipients per collection**

---

## 3. Create Bills (Payment Invoices)

### Basic Bill Creation

```php
$result = $billplz->createBill([
    'email' => 'student@example.com',
    'name' => 'Ahmad Ali',
    'mobile' => '0123456789',
    'amount' => 200.00,  // RM 200 (auto-converted to cents)
    'description' => 'Course Fee - Introduction to Programming',
    'reference_1' => 'ST001',  // Student ID
    'reference_2' => 'CF101',  // Course code
]);

if ($result['success']) {
    $paymentUrl = $result['data']['url'];
    echo "Payment URL: $paymentUrl";
}
```

### Bill with Direct Payment Gateway (Skip Selection Page)

```php
$result = $billplz->createBill([
    'email' => 'student@example.com',
    'name' => 'Ahmad Ali',
    'amount' => 200.00,
    'description' => 'Course Fee',
    'bank_code' => 'MB2U0227', // Maybank2u
]);

if ($result['success']) {
    // Use direct_url for auto-submit
    $directUrl = $result['data']['direct_url'] ?? $result['data']['url'];
    echo "Direct Payment URL: $directUrl";
}
```

### Get Available Payment Gateways

```php
$gateways = $billplz->getPaymentGateways();

if ($gateways['success']) {
    foreach ($gateways['data']['payment_gateways'] as $gateway) {
        echo "Code: {$gateway['code']} - Active: {$gateway['active']} - Category: {$gateway['category']}\n";
    }
}

// Common Malaysian Bank Codes:
// MB2U0227 - Maybank2u
// CIMBCLICKS - CIMB Clicks
// PBB0233 - PBe (Public Bank)
// RHB0218 - RHB Now
// BP-BILLPLZ1 - Visa/Mastercard
// BP-PPL01 - PayPal
```

---

## 4. Open Collections (Payment Forms)

Create a payment form that customers can access directly:

```php
$result = $billplz->createOpenCollection(
    'Course Registration',
    'Registration fee for all courses',
    5000, // RM 50
    [
        'fixed_amount' => true,
        'payment_button' => 'pay',
        'tax' => 6, // 6% SST
        'redirect_uri' => 'https://yourdomain.com/success'
    ]
);

if ($result['success']) {
    $formUrl = $result['data']['url'];
    echo "Share this URL: $formUrl";
}
```

### Open Amount Collections

```php
$result = $billplz->createOpenCollection(
    'Donation',
    'Any amount welcome',
    0,
    [
        'fixed_amount' => false,     // Allow any amount
        'fixed_quantity' => false,  // Allow any quantity
        'payment_button' => 'buy'
    ]
);
```

---

## 5. Webhook Handling

### Configure in Billplz Dashboard:
- Callback URL: `https://yourdomain.com/payment/billplz/callback`
- Redirect URL: `https://yourdomain.com/payment/billplz/redirect`
- **Enable X-Signature** for security

### Webhook Callback (Already Implemented)

The callback handler in `PaymentController::billplzCallback()` automatically:
1. ✅ Verifies X-Signature
2. ✅ Updates payment status
3. ✅ Marks bills as paid
4. ✅ Syncs to accounting system

### Manual Status Check

```php
$result = $billplz->getBillStatus($billId);

if ($result['success']) {
    $status = $result['data']['state']; // 'paid', 'pending', 'cancelled'
    echo "Bill status: $status";
}
```

---

## 6. Customer Receipt Management

### Control Receipt Delivery

```php
// Always send receipt email
$billplz->controlCustomerReceiptDelivery($collectionId, 'activate');

// Never send receipt email
$billplz->controlCustomerReceiptDelivery($collectionId, 'deactivate');

// Use global settings
$billplz->controlCustomerReceiptDelivery($collectionId, 'global');

// Check current status
$result = $billplz->getCustomerReceiptDeliveryStatus($collectionId);
// Returns: 'active', 'inactive', or 'global'
```

---

## 7. Monitor Webhook Performance

```php
$result = $billplz->getWebhookRank();

if ($result['success']) {
    $rank = $result['data']['rank'];
    
    if ($rank == 0.0) {
        echo "Perfect! Your webhooks are performing optimally.";
    } elseif ($rank < 5.0) {
        echo "Good performance. Rank: $rank";
    } else {
        echo "Warning! Your webhook rank is $rank. Improve your callback response times.";
    }
}

// Rank resets daily at 17:00 Malaysia time
```

---

## 8. Get Collections List

```php
// Get all collections (page 1)
$result = $billplz->getCollectionIndex(1);

// Get active collections only
$result = $billplz->getCollectionIndex(1, 'active');

// Get inactive collections
$result = $billplz->getCollectionIndex(1, 'inactive');

if ($result['success']) {
    foreach ($result['data']['collections'] as $collection) {
        echo "ID: {$collection['id']} - Title: {$collection['title']} - Status: {$collection['status']}\n";
    }
    
    echo "Page: {$result['data']['page']}\n";
}
```

---

## 9. Collection Limits

⚠️ **Important: Collection Limits**
- Billplz has a **lifetime ceiling of 20,000 collections** per account
- This includes both standard and open collections
- Plan accordingly for long-term use

---

## 10. Payment Flow Example

### Complete Payment Flow

```php
use App\Services\BillplzService;

$billplz = new BillplzService();

// 1. Create a bill
$result = $billplz->createBill([
    'email' => 'student@example.com',
    'name' => 'Ahmad Ali',
    'mobile' => '0123456789',
    'amount' => 500.00,
    'description' => 'Tuition Fee - Semester 1',
]);

if ($result['success']) {
    $bill = $result['data'];
    
    // 2. Redirect user to payment URL
    return redirect($bill['url']);
    
    // Or use direct payment URL:
    // return redirect($bill['direct_url']);
}

// 3. Webhook will be received at /payment/billplz/callback
// 4. User will be redirected to success page after payment
```

---

## 11. Testing in Sandbox

### Test Cards

```php
// Successful payment
Card: 4000 0000 0000 0002

// Failed payment
Card: 4000 0000 0000 0119

// Expired card
Card: 4000 0000 0000 0069
```

### Test Payment Flow

1. Set `BILLPLZ_SANDBOX=true` in `.env`
2. Use test cards above
3. Check logs: `storage/logs/laravel.log`
4. Verify webhook receives callback

---

## 12. Production Checklist

Before going live:

- [ ] Set `BILLPLZ_SANDBOX=false` in `.env`
- [ ] Get production API keys from Billplz
- [ ] Configure webhooks in Billplz dashboard
- [ ] Set up X-Signature key
- [ ] Test with real payment (small amount)
- [ ] Monitor webhook rank
- [ ] Set up collection receipt preferences
- [ ] Enable HTTPS only
- [ ] Monitor logs for errors

---

## Quick Tips

1. **Amounts**: Always use cents (e.g., RM 100.00 = 10000 cents)
2. **Currency**: Only MYR supported
3. **Split Payments**: Max 2 recipients per collection
4. **Collections**: Max 20,000 lifetime per account
5. **Security**: Always use HTTPS in production
6. **Webhooks**: Must return status 200-308
7. **X-Signature**: Verify all webhook callbacks
8. **Logging**: Enable to debug issues

---

## Support

- Documentation: `BILLPLZ_INTEGRATION_GUIDE_V4.md`
- Original Guide: `BILLPLZ_SETUP_GUIDE.md`
- Laravel Logs: `storage/logs/laravel.log`
- Billplz Support: Contact through dashboard

---

**Last Updated**: January 2025  
**API Version**: v4



