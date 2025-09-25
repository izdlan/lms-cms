# Google Sheets API Setup Guide

## Problem
The current Google Sheets import only shows limited data (8 students) because it's accessing the sheets as a guest. When you're logged into your Google account, you see the full data (20 students), but the CSV export respects the same permissions.

## Solution: Google Sheets API Authentication

### Step 1: Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Note down your project ID

### Step 2: Enable Google Sheets API
1. In the Google Cloud Console, go to "APIs & Services" > "Library"
2. Search for "Google Sheets API"
3. Click on it and press "Enable"

### Step 3: Create Credentials
Choose one of these options:

#### Option A: Service Account (Recommended)
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "Service Account"
3. Fill in the details and create
4. Click on the created service account
5. Go to "Keys" tab > "Add Key" > "Create new key" > "JSON"
6. Download the JSON file
7. Save it as `storage/app/google-credentials.json` in your project

#### Option B: API Key (Simpler but less secure)
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "API Key"
3. Copy the API key
4. Add it to your `.env` file: `GOOGLE_SHEETS_API_KEY=your_api_key_here`

### Step 4: Share Google Sheets
1. Open your Google Sheets
2. Click "Share" button
3. Add the service account email (from the JSON file) or make it public
4. Give "Viewer" permissions

### Step 5: Update Configuration
Add these to your `.env` file:
```
GOOGLE_SHEETS_API_KEY=your_api_key_here
GOOGLE_SHEETS_SPREADSHEET_ID=1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk
```

### Step 6: Test the Setup
Run the test script:
```bash
php test_google_sheets_api.php
```

## Alternative: Quick Fix
If you don't want to set up API authentication, you can:
1. Make your Google Sheets publicly readable
2. Update the URL in the configuration
3. The system will then access the full data

## Benefits of API Authentication
- ✅ Access to full data even when not logged in
- ✅ More reliable than CSV export
- ✅ Better error handling
- ✅ Can access specific ranges and sheets
- ✅ No permission issues

