# Accounting System Integration Guide

## Overview

This guide explains how to integrate your LMS payment system with external accounting software using Billplz payment gateway. The integration supports both **push** (automatic) and **pull** (manual) methods for syncing payment data.

## System Architecture

```
[Student LMS] → [Billplz Payment] → [LMS Database] → [Accounting System]
     ↓              ↓                    ↓              ↓
  Payment        Payment            Auto-sync      Manual Sync
  Request        Processing         (Webhook)      (API Pull)
```

## Features

✅ **Automatic Payment Sync** - Payments are automatically sent to accounting system via webhook  
✅ **Manual Sync API** - Accounting system can pull payment data on demand  
✅ **Payment Statistics** - Get payment summaries and analytics  
✅ **Error Handling** - Comprehensive logging and retry mechanisms  
✅ **Security** - API key authentication for all endpoints  

## Setup Instructions

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Accounting Integration Settings
ACCOUNTING_ENABLED=true
ACCOUNTING_API_URL=https://accounting.yourcompany.com
ACCOUNTING_API_KEY=your-secure-api-key-here
ACCOUNTING_TIMEOUT=30
ACCOUNTING_RETRY_ATTEMPTS=3
ACCOUNTING_RETRY_DELAY=5
ACCOUNTING_LOGGING=true
ACCOUNTING_AUTO_SYNC=true
ACCOUNTING_SYNC_DELAY=5
```

### 2. Database Migration

Run the migration to add accounting sync fields:

```bash
php artisan migrate
```

This adds the following fields to the `payments` table:
- `accounting_synced` (boolean) - Whether payment was synced to accounting
- `accounting_synced_at` (timestamp) - When it was synced
- `accounting_sync_error` (text) - Any sync errors

### 3. Billplz Webhook Configuration

In your Billplz dashboard, set the callback URL to:
```
https://your-lms-domain.com/payment/billplz/callback
```

## API Endpoints

### 1. Get Payment Data (Pull Method)

**Endpoint:** `GET /api/accounting/payments`

**Headers:**
```
X-API-Key: your-secure-api-key-here
Content-Type: application/json
```

**Query Parameters:**
- `from_date` (optional) - Start date (Y-m-d format)
- `to_date` (optional) - End date (Y-m-d format)  
- `payment_type` (optional) - Filter by payment type
- `accounting_synced` (optional) - Filter by sync status (true/false)

**Example Request:**
```bash
curl -H "X-API-Key: your-api-key" \
     "https://your-lms-domain.com/api/accounting/payments?from_date=2025-01-01&to_date=2025-01-31"
```

**Example Response:**
```json
{
    "success": true,
    "data": [
        {
            "lms_payment_id": 123,
            "billplz_id": "b12345xyz",
            "student_id": "ST001",
            "student_name": "Ahmad Ali",
            "student_email": "ahmad@student.edu",
            "student_phone": "0123456789",
            "amount": 20000.00,
            "currency": "MYR",
            "payment_status": "paid",
            "payment_method": "fpx",
            "transaction_id": "TXN123456",
            "description": "Payment for Course Fee - CF001",
            "payment_type": "course_fee",
            "reference_id": "1",
            "reference_type": "course",
            "paid_at": "2025-01-15T14:30:00.000000Z",
            "created_at": "2025-01-15T14:25:00.000000Z",
            "payment_details": {...},
            "billplz_response": {...}
        }
    ],
    "count": 1,
    "filters": {
        "from_date": "2025-01-01",
        "to_date": "2025-01-31"
    }
}
```

### 2. Get Payment Statistics

**Endpoint:** `GET /api/accounting/statistics`

**Example Response:**
```json
{
    "success": true,
    "data": {
        "statistics": {
            "total_payments": 150,
            "total_amount": 3000000.00,
            "average_amount": 20000.00,
            "synced_count": 145,
            "unsynced_count": 5
        },
        "payment_types": [
            {
                "type": "course_fee",
                "count": 100,
                "total": 2000000.00
            },
            {
                "type": "general_fee", 
                "count": 50,
                "total": 1000000.00
            }
        ],
        "period": {
            "from": "2025-01-01",
            "to": "2025-01-31"
        }
    }
}
```

### 3. Test Connection

**Endpoint:** `GET /api/accounting/test-connection`

**Example Response:**
```json
{
    "success": true,
    "message": "Connection successful",
    "data": {
        "status": "ok",
        "timestamp": "2025-01-15T14:30:00Z"
    }
}
```

### 4. Manual Sync (Push Method)

**Endpoint:** `POST /api/accounting/sync`

**Body:**
```json
{
    "payment_ids": [123, 124, 125]
}
```

**Example Response:**
```json
{
    "success": true,
    "message": "Payments synced successfully",
    "synced_count": 3
}
```

## Integration Methods

### Method 1: Automatic Push (Recommended)

When a student completes payment:

1. Billplz sends webhook to your LMS
2. LMS updates payment status in database
3. LMS automatically sends payment data to accounting system
4. Accounting system receives real-time payment data

**Advantages:**
- Real-time synchronization
- No manual intervention required
- Automatic error handling and retries

### Method 2: Manual Pull

Accounting system periodically fetches payment data:

1. Accounting system calls LMS API endpoint
2. LMS returns payment data in JSON format
3. Accounting system processes and stores the data

**Advantages:**
- No server load on LMS side
- Accounting system controls sync timing
- Can filter data by date, type, etc.

## Payment Data Structure

Each payment record includes:

| Field | Type | Description |
|-------|------|-------------|
| `lms_payment_id` | Integer | Unique payment ID in LMS |
| `billplz_id` | String | Billplz bill ID |
| `student_id` | String | Student identifier |
| `student_name` | String | Student full name |
| `student_email` | String | Student email address |
| `student_phone` | String | Student phone number |
| `amount` | Decimal | Payment amount (RM) |
| `currency` | String | Currency code (MYR) |
| `payment_status` | String | Payment status (paid/pending/failed) |
| `payment_method` | String | Payment method (fpx/card/ewallet) |
| `transaction_id` | String | Transaction reference |
| `description` | String | Payment description |
| `payment_type` | String | Type of payment (course_fee/general_fee) |
| `reference_id` | String | Related record ID |
| `reference_type` | String | Related record type |
| `paid_at` | DateTime | When payment was completed |
| `created_at` | DateTime | When payment was created |

## Security

- All API endpoints require `X-API-Key` header
- API keys should be stored securely
- Use HTTPS for all communications
- Implement rate limiting on accounting system side

## Error Handling

The system includes comprehensive error handling:

- Failed syncs are logged with error details
- Retry mechanism for temporary failures
- Manual sync option for failed payments
- Detailed error messages in API responses

## Monitoring

Check logs for integration status:

```bash
# View accounting integration logs
tail -f storage/logs/laravel.log | grep "Accounting"
```

## Example Integration Code

### PHP (Accounting System Side)

```php
<?php
class LMSIntegration {
    private $apiUrl = 'https://your-lms-domain.com/api/accounting';
    private $apiKey = 'your-secure-api-key-here';
    
    public function getPayments($fromDate = null, $toDate = null) {
        $url = $this->apiUrl . '/payments';
        $params = [];
        
        if ($fromDate) $params['from_date'] = $fromDate;
        if ($toDate) $params['to_date'] = $toDate;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $response = $this->makeRequest('GET', $url);
        return $response['data'] ?? [];
    }
    
    public function getStatistics() {
        $url = $this->apiUrl . '/statistics';
        $response = $this->makeRequest('GET', $url);
        return $response['data'] ?? [];
    }
    
    private function makeRequest($method, $url, $data = null) {
        $headers = [
            'X-API-Key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("API request failed with code: $httpCode");
        }
        
        return json_decode($response, true);
    }
}
```

### Python (Accounting System Side)

```python
import requests
import json
from datetime import datetime

class LMSIntegration:
    def __init__(self, api_url, api_key):
        self.api_url = api_url
        self.api_key = api_key
        self.headers = {
            'X-API-Key': api_key,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    
    def get_payments(self, from_date=None, to_date=None):
        url = f"{self.api_url}/payments"
        params = {}
        
        if from_date:
            params['from_date'] = from_date
        if to_date:
            params['to_date'] = to_date
            
        response = requests.get(url, headers=self.headers, params=params)
        response.raise_for_status()
        
        return response.json()['data']
    
    def get_statistics(self):
        url = f"{self.api_url}/statistics"
        response = requests.get(url, headers=self.headers)
        response.raise_for_status()
        
        return response.json()['data']
    
    def sync_payments(self, payment_ids=None):
        url = f"{self.api_url}/sync"
        data = {}
        
        if payment_ids:
            data['payment_ids'] = payment_ids
            
        response = requests.post(url, headers=self.headers, json=data)
        response.raise_for_status()
        
        return response.json()

# Usage example
lms = LMSIntegration('https://your-lms-domain.com/api/accounting', 'your-api-key')

# Get payments for current month
payments = lms.get_payments(
    from_date='2025-01-01',
    to_date='2025-01-31'
)

# Get statistics
stats = lms.get_statistics()
print(f"Total payments: {stats['statistics']['total_payments']}")
print(f"Total amount: RM {stats['statistics']['total_amount']:,.2f}")
```

## Troubleshooting

### Common Issues

1. **API Key Authentication Failed**
   - Verify API key in `.env` file
   - Check `X-API-Key` header in requests

2. **Webhook Not Receiving Data**
   - Verify Billplz callback URL configuration
   - Check webhook endpoint accessibility
   - Review Laravel logs for errors

3. **Sync Failures**
   - Check accounting system API endpoint
   - Verify network connectivity
   - Review error logs for details

4. **Database Errors**
   - Run migrations: `php artisan migrate`
   - Check database connection
   - Verify table structure

### Debug Commands

```bash
# Test accounting connection
curl -H "X-API-Key: your-api-key" \
     "https://your-lms-domain.com/api/accounting/test-connection"

# Check payment data
curl -H "X-API-Key: your-api-key" \
     "https://your-lms-domain.com/api/accounting/payments"

# View logs
tail -f storage/logs/laravel.log | grep -i accounting
```

## Support

For technical support or questions about the integration:

1. Check the logs first: `storage/logs/laravel.log`
2. Verify configuration in `.env` file
3. Test API endpoints manually
4. Contact the development team with specific error messages

---

**Note:** This integration is designed to work with any accounting system that can receive HTTP requests and process JSON data. The accounting team can use the provided API endpoints to integrate with their existing software.
