# Billplz X-Signature Implementation Guide

## ✅ X-Signature Verification Implemented

Your Billplz integration now supports **complete X-Signature verification** for both callback and redirect URLs as per Billplz documentation.

---

## 🔐 How X-Signature Works

X-Signature is a security mechanism Billplz uses to verify that webhook requests come from their servers and data hasn't been tampered with.

### How It Works:

1. Billplz sends webhook data with X-Signature
2. Your server calculates expected signature
3. Compare signatures - if they match, data is authentic

---

## 📋 Implementation Details

### 1. Callback URL X-Signature (Webhook)

**File**: `app/Services/BillplzService.php` → `verifyWebhook()`

**Calculation Process:**
```php
// Step 1: Extract all parameters except x_signature
$params = ['id' => 'xxx', 'paid' => 'true', ...];
unset($params['x_signature']);

// Step 2: Build source strings (key + value)
// id + value, paid + value, etc.

// Step 3: Sort alphabetically (case-insensitive)
// amount100, collection_idxxx, due_atxxx, ...

// Step 4: Join with pipe (|)
$string = "amount100|collection_idxxx|due_atxxx|...";

// Step 5: HMAC-SHA256 with your X-Signature Key
$signature = hash_hmac('sha256', $string, $xSignatureKey);
```

**Example Callback Request:**
```
POST /payment/billplz/callback
id=zq0tm2wc
collection_id=yhx5t1pp
paid=true
state=paid
amount=100
paid_amount=100
x_signature=0fe0a20b8d557eeae570377783d062a3...
```

---

### 2. Redirect URL X-Signature

**File**: `app/Services/BillplzService.php` → `verifyRedirectSignature()`

**Calculation Process:**
```php
// Parameters come as billplz[id], billplz[paid], etc.
$params = [
    'billplz[id]' => 'xxx',
    'billplz[paid]' => 'true',
    'billplz[x_signature]' => 'xxx'
];

// Build source: billplz + key + value
// billplzidxxx, billplzpaidtrue, ...

// Sort and join with pipe
$string = "billplzidxxx|billplzpaidtrue|...";

// HMAC-SHA256
$signature = hash_hmac('sha256', $string, $xSignatureKey);
```

**Example Redirect Request:**
```
GET /payment/billplz/redirect?billplz[id]=zq0tm2wc&billplz[paid]=true&billplz[x_signature]=xxx
```

---

## ⚙️ Configuration in Billplz Dashboard

### Enable X-Signature:

1. **Log in** to Billplz dashboard:
   - Sandbox: https://www.billplz-sandbox.com/
   - Production: https://www.billplz.com/

2. **Go to Settings** → **Webhooks**

3. **Enable X-Signature:**
   - ✅ Enable **X-Signature Callback** (for callback URL)
   - ✅ Enable **X-Signature Redirect** (for redirect URL)

4. **Copy X-Signature Key:**
   - You'll see your X-Signature Key displayed
   - Add to `.env`: `BILLPLZ_X_SIGNATURE_KEY=your-key`

---

## 🔧 Current Implementation

### Callback Handler (`billplzCallback`)

✅ **Location**: `app/Http/Controllers/PaymentController.php`

✅ **Features**:
- Extracts `X-Signature` header
- Verifies signature using `verifyWebhook()`
- Updates payment status
- Handles failed verification
- Logs all verification attempts

### Redirect Handler (`billplzRedirect`)

✅ **Location**: `app/Http/Controllers/PaymentController.php`

✅ **Features**:
- Extracts `billplz[x_signature]` parameter
- Verifies signature using `verifyRedirectSignature()`
- Uses verified data directly if signature valid
- Falls back to API check if no signature
- Handles paid/failed states

---

## 🧪 Testing

### Test Callback Verification:

1. Create a payment via the test interface
2. Complete payment in Billplz
3. Check logs: `storage/logs/laravel.log`
4. Look for: `Webhook signature verification`

### Test Redirect Verification:

1. Create payment
2. Complete payment
3. Should redirect to success page
4. Check logs for redirect signature verification

---

## 📊 What Happens on Payment

### With X-Signature Enabled:

**Callback (Server-side):**
1. Payment completed → Billplz sends POST to callback URL
2. System extracts X-Signature from request
3. Calculates expected signature
4. Compares signatures
5. ✅ If match → Update payment status
6. ❌ If mismatch → Log warning, ignore request

**Redirect (Client-side):**
1. Payment completed → Billplz redirects to redirect URL
2. System extracts X-Signature from URL params
3. Calculates expected signature
4. Compares signatures
5. ✅ If match → Show success, update payment
6. ❌ If mismatch → Show failed page

### Without X-Signature (Sandbox/Fallback):

- System allows callback in sandbox mode
- Still verifies data integrity
- Logs warnings about missing signature
- Falls back to API call for status check

---

## ⚠️ Important Security Notes

### 1. Never Skip Signature Verification in Production

```php
// Always verify in production!
if (!$this->billplzService->verifyWebhook($signature, $data)) {
    // Reject the request
    return response()->json(['error' => 'Invalid signature'], 400);
}
```

### 2. Keep X-Signature Key Secure

- Never commit to version control
- Store in `.env` file only
- Use different keys for sandbox/production
- Rotate keys if compromised

### 3. Monitor Your Webhook Rank

```php
$rank = $billplz->getWebhookRank();
if ($rank['data']['rank'] > 5.0) {
    // Your callbacks are failing!
    // Check response times and status codes
}
```

---

## 🐛 Troubleshooting

### Signature Verification Failing?

**Check:**
1. X-Signature key is correct in `.env`
2. Key matches the one in Billplz dashboard
3. X-Signature is enabled in Billplz dashboard
4. You're using same environment (sandbox vs production)

### Debug Signature Calculation:

Look in logs for:
```
Webhook signature verification: {
    "provided": "abc123...",
    "expected": "def456...",
    "match": false
}
```

**Common Issues:**
- **Mismatch**: X-Signature key is wrong
- **Empty**: X-Signature not enabled in dashboard
- **Sandbox**: Using wrong environment keys

### Callback Not Receiving?

1. Check Billplz dashboard webhook settings
2. Verify callback URL is accessible
3. Check server logs for incoming requests
4. Test with ngrok for local development

---

## 📝 Example X-Signature Calculation

### Callback Request:

**Input:**
```
id = "zq0tm2wc"
collection_id = "yhx5t1pp"
paid = true
state = "paid"
amount = 100
due_at = "2018-9-27"
email = "tester@test.com"
mobile = ""
name = "TESTER"
```

**Step 1:** Remove x_signature

**Step 2:** Build source strings:
```
id = zq0tm2wc          → idzq0tm2wc
collection_id = yhx5t1pp → collection_idyhx5t1pp
paid = true             → paidtrue
state = paid            → statepaid
amount = 100            → amount100
due_at = 2018-9-27      → due_at2018-9-27
email = tester@test.com → emailtester@test.com
mobile = ""             → mobile
name = TESTER           → nameTESTER
```

**Step 3:** Sort (case-insensitive):
```
amount100
collection_idyhx5t1pp
due_at2018-9-27
emailtester@test.com
idzq0tm2wc
mobile
nameTESTER
paidtrue
statepaid
```

**Step 4:** Join with pipe:
```
amount100|collection_idyhx5t1pp|due_at2018-9-27|emailtester@test.com|idzq0tm2wc|mobile|nameTESTER|paidtrue|statepaid
```

**Step 5:** HMAC-SHA256:
```
X-Signature Key: S-s7b4yWpp9h7rrkNM1i3Z_g
Result: 0fe0a20b8d557eeae570377783d062a3816a9ea80f368860bacfa7ec3ca4d00e
```

**Step 6:** Compare:
```
Provided: abc123...
Expected: 0fe0a20...
Match: ✅ or ❌
```

---

## ✅ Verification Checklist

- [ ] X-Signature key added to `.env`
- [ ] X-Signature enabled in Billplz dashboard
- [ ] Callback URL configured in Billplz
- [ ] Redirect URL configured in Billplz
- [ ] Testing in sandbox mode first
- [ ] Monitoring webhook rank
- [ ] Checking logs for verification
- [ ] Production webhook URLs set
- [ ] HTTPS enabled for production
- [ ] Signature verification working

---

## 🎯 Best Practices

1. **Always Use X-Signature in Production**
   - Don't skip verification
   - Log all failures
   - Alert on mismatches

2. **Respond Quickly to Callbacks**
   - Return status 200 within 20 seconds
   - Don't do heavy processing in callback
   - Use queue jobs for heavy work

3. **Monitor Webhook Performance**
   - Check rank regularly
   - Fix failing callbacks immediately
   - Avoid rate limiting

4. **Test Both Callback and Redirect**
   - Test in sandbox first
   - Verify signature calculation
   - Check payment status updates

---

**Your X-Signature implementation is complete and secure! 🎉**

