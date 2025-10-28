# ⚠️ Billplz Authentication Error - Access Denied

## Problem

Your API connection is returning **401 Unauthorized** - "Access denied".

This means the API key is either:
- ❌ Wrong or invalid
- ❌ From the wrong environment (production vs sandbox)
- ❌ Not authorized for your account

---

## 🔍 Diagnose the Issue

The error you're seeing:
```
HTTP Status: 401
Response: {"error":{"type":"Unauthorized","message":"Access denied"}}
```

---

## ✅ Solution

### 1. Get Your Sandbox API Key

**Important**: Sandbox and production have **separate accounts and API keys**.

You **MUST** use the sandbox API key for testing:

1. **Log in to Billplz Sandbox**: https://www.billplz-sandbox.com/
2. **Go to Settings** → **API Keys**
3. **Copy your Sandbox API Key**
4. **Copy your Sandbox Collection ID**

### 2. Update Your .env File

Make sure your `.env` file has the **sandbox** API key:

```env
BILLPLZ_API_KEY=your-sandbox-api-key-here
BILLPLZ_COLLECTION_ID=your-sandbox-collection-id
BILLPLZ_SANDBOX=true
```

### 3. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Test Again

Try the test page again:
```
http://localhost:8000/billplz-test
```

---

## 🔑 How to Get Your Sandbox Credentials

### Step 1: Create Sandbox Account (if you don't have one)

1. Go to: https://www.billplz-sandbox.com/
2. Sign up or log in
3. Use this for testing only (no real payments)

### Step 2: Get API Key

1. Log in to sandbox dashboard
2. Click **Settings** (gear icon)
3. Click **API Keys**
4. You'll see your **Secret Key** - this is your API key
5. Copy it to your `.env` file as `BILLPLZ_API_KEY`

### Step 3: Get Collection ID

1. Click **Collections** in the sidebar
2. Either select an existing collection or create a new one
3. Copy the **Collection ID**
4. Add to `.env` as `BILLPLZ_COLLECTION_ID`

### Step 4: Get X-Signature Key

1. Go to **Settings** → **Webhooks**
2. Look for **X-Signature Key**
3. Copy it to your `.env` as `BILLPLZ_X_SIGNATURE_KEY`

---

## ⚠️ Common Mistakes

### ❌ Wrong: Using Production API Key in Sandbox

```env
BILLPLZ_API_KEY=production-key-here  # This won't work in sandbox!
BILLPLZ_SANDBOX=true  # But sandbox is enabled
```

**Result**: 401 Unauthorized

### ✅ Correct: Using Sandbox API Key

```env
BILLPLZ_API_KEY=sandbox-key-here
BILLPLZ_SANDBOX=true
```

**Result**: Works!

---

## 🧪 Verify Your Setup

Run this command to check your configuration:

```bash
php artisan tinker
```

Then:
```php
echo "API Key: " . config('billplz.api_key') . "\n";
echo "Collection ID: " . config('billplz.collection_id') . "\n";
echo "Sandbox: " . (config('billplz.sandbox') ? 'true' : 'false') . "\n";
```

---

## 📝 Complete .env Setup

Your `.env` file should have:

```env
# Billplz Sandbox Configuration
BILLPLZ_API_KEY=your-sandbox-api-key
BILLPLZ_COLLECTION_ID=your-sandbox-collection-id
BILLPLZ_X_SIGNATURE_KEY=your-x-signature-key
BILLPLZ_SANDBOX=true
BILLPLZ_LOGGING=true
```

---

## 🎯 Next Steps

1. ✅ Log in to https://www.billplz-sandbox.com/
2. ✅ Copy your **sandbox** API key
3. ✅ Copy your **sandbox** collection ID
4. ✅ Update `.env` file
5. ✅ Run `php artisan config:clear`
6. ✅ Test again at `http://localhost:8000/billplz-test`

---

## 💡 Production vs Sandbox

Remember:
- **Sandbox**: Testing environment, use sandbox API key
- **Production**: Live payments, use production API key
- **They are SEPARATE accounts and keys!**
- **NEVER mix them up**

---

**Once you update your `.env` with the correct sandbox credentials, the 400 error will be fixed!** ✅

