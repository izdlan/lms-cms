# 🔧 Production .env Fixes Required

## ❌ **Current Issues & Fixes:**

### 1. **Log Level**
```env
# CHANGE FROM:
LOG_LEVEL=debug

# TO:
LOG_LEVEL=error
```

### 2. **Database Host**
```env
# CHANGE FROM:
DB_HOST=127.0.0.1

# TO:
DB_HOST=your_production_database_server
# OR if using same server:
DB_HOST=localhost
```

### 3. **Mail Configuration**
```env
# CHANGE FROM:
MAIL_HOST=mailpit
MAIL_FROM_ADDRESS="hello@example.com"

# TO:
MAIL_HOST=your_smtp_server
MAIL_FROM_ADDRESS="noreply@olympia-education.com"
MAIL_FROM_NAME="Olympia Education LMS"
```

### 4. **API URLs**
```env
# CHANGE FROM:
LARAVEL_API_URL=http://127.0.0.1:8000

# TO:
LARAVEL_API_URL=https://lms.olympia-education.com
```

### 5. **Webhook URLs**
```env
# CHANGE FROM:
BILLPLZ_CALLBACK_URL=https://unjeopardized-edwin-taillessly.ngrok-free.dev/payment/billplz/callback
BILLPLZ_REDIRECT_URL=https://unjeopardized-edwin-taillessly.ngrok-free.dev/payment/billplz/redirect

# TO:
BILLPLZ_CALLBACK_URL=https://lms.olympia-education.com/payment/billplz/callback
BILLPLZ_REDIRECT_URL=https://lms.olympia-education.com/payment/billplz/redirect
```

### 6. **Get Real API Keys**
You need to replace these placeholder values:

```env
# OneDrive (Azure App Registration)
ONEDRIVE_CLIENT_ID=your_real_azure_app_client_id
ONEDRIVE_CLIENT_SECRET=your_real_azure_app_client_secret
ONEDRIVE_TENANT_ID=your_real_azure_tenant_id

# Billplz Payment Gateway
BILLPLZ_API_KEY=your_real_billplz_api_key
BILLPLZ_COLLECTION_ID=your_real_collection_id
BILLPLZ_WEBHOOK_KEY=your_real_webhook_key

# Google Sheets
GOOGLE_SHEETS_API_KEY=your_real_google_api_key
```

## 🚨 **Security Recommendations:**

### 1. **Use Redis for Production**
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. **Set Up Proper File Storage**
```env
FILESYSTEM_DISK=s3
# OR
FILESYSTEM_DISK=local
```

### 3. **Configure AWS S3 (Recommended)**
```env
AWS_ACCESS_KEY_ID=your_aws_access_key
AWS_SECRET_ACCESS_KEY=your_aws_secret_key
AWS_DEFAULT_REGION=your_aws_region
AWS_BUCKET=your_s3_bucket
```

## ✅ **Current Status:**
- **Basic Security**: ✅ Good
- **Database**: ⚠️ Needs host fix
- **Mail**: ❌ Needs configuration
- **APIs**: ❌ Needs real keys
- **Webhooks**: ❌ Needs domain fix
- **Logging**: ❌ Needs level fix

## 🎯 **Priority Order:**
1. Fix log level (immediate)
2. Fix webhook URLs (immediate)
3. Get real API keys (before go-live)
4. Configure mail server (before go-live)
5. Set up Redis (performance)
6. Configure S3 (scalability)

