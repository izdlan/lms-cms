# OneDrive to Laravel Bridge Setup Guide

## 🎯 **Overview**

This Python-based solution bridges OneDrive and your Laravel application:
- **Monitors OneDrive** for Excel file changes
- **Downloads files** automatically when modified
- **Processes Excel data** and sends to Laravel API
- **Real-time sync** with configurable intervals

## 🚀 **Quick Start**

### **Step 1: Setup Python Environment**

```bash
# Navigate to the Python bridge directory
cd python_onedrive_bridge

# Run setup script
python setup.py

# Or install manually
pip install -r requirements.txt
```

### **Step 2: Configure OneDrive**

1. **Go to Azure Portal** (portal.azure.com)
2. **Create App Registration**:
   - Name: "LMS OneDrive Bridge"
   - Supported account types: "Accounts in this organizational directory only"
   - Redirect URI: `http://localhost:8080/auth/callback`

3. **Get Credentials**:
   - Copy **Application (client) ID**
   - Copy **Directory (tenant) ID**
   - Create **Client Secret** (copy the value)

4. **Set API Permissions**:
   - Microsoft Graph → Application permissions
   - Add: `Files.Read.All`, `Sites.Read.All`

### **Step 3: Get OneDrive Folder ID**

1. **Open OneDrive** in browser
2. **Navigate to your Excel file folder**
3. **Copy the folder ID** from URL:
   ```
   https://onedrive.live.com/?id=YOUR_FOLDER_ID&...
   ```

### **Step 4: Configure the Bridge**

Edit `config.env`:

```env
# OneDrive Configuration
ONEDRIVE_CLIENT_ID=your_azure_app_client_id
ONEDRIVE_CLIENT_SECRET=your_azure_app_client_secret
ONEDRIVE_TENANT_ID=your_azure_tenant_id
ONEDRIVE_FOLDER_ID=your_onedrive_folder_id

# Laravel API Configuration
LARAVEL_API_URL=http://127.0.0.1:8000
LARAVEL_API_ENDPOINT=/api/import-excel-data

# File Configuration
EXCEL_FILE_NAME=students.xlsx
TEMP_DIR=./temp
```

### **Step 5: Test the Bridge**

```bash
# Test with 1-minute intervals
python onedrive_bridge.py 1

# Test with 5-minute intervals (default)
python onedrive_bridge.py 5
```

## 🔧 **Detailed Setup**

### **Azure App Registration Setup**

1. **Go to Azure Portal** → **Azure Active Directory** → **App registrations**

2. **New registration**:
   - Name: `LMS OneDrive Bridge`
   - Supported account types: `Accounts in this organizational directory only`
   - Redirect URI: `Web` → `http://localhost:8080/auth/callback`

3. **After creation**:
   - Copy **Application (client) ID** → `ONEDRIVE_CLIENT_ID`
   - Copy **Directory (tenant) ID** → `ONEDRIVE_TENANT_ID`

4. **Create Client Secret**:
   - Go to **Certificates & secrets**
   - **New client secret**
   - Copy the **Value** → `ONEDRIVE_CLIENT_SECRET`

5. **Set API Permissions**:
   - Go to **API permissions**
   - **Add a permission** → **Microsoft Graph** → **Application permissions**
   - Add: `Files.Read.All`, `Sites.Read.All`
   - **Grant admin consent**

### **OneDrive Folder Setup**

1. **Create a dedicated folder** in OneDrive for your Excel files
2. **Upload your Excel file** (e.g., `students.xlsx`)
3. **Get the folder ID**:
   - Right-click folder → **Copy link**
   - Extract ID from URL: `https://onedrive.live.com/?id=YOUR_FOLDER_ID&...`

### **Laravel API Setup**

The Laravel API endpoint is already created at `/api/import-excel-data`.

**Test the endpoint**:
```bash
curl -X POST http://127.0.0.1:8000/api/import-excel-data \
  -H "Content-Type: application/json" \
  -d '{"timestamp":"2024-01-01T00:00:00","source":"test","sheets":{"Test Sheet":{"headers":["Name","IC"],"data":[["John Doe","123456789"]],"row_count":1}}}'
```

## 🚀 **Running the Bridge**

### **Development Mode**
```bash
# Run with 1-minute intervals for testing
python onedrive_bridge.py 1
```

### **Production Mode**
```bash
# Run with 5-minute intervals
python onedrive_bridge.py 5

# Run in background (Linux/Mac)
nohup python onedrive_bridge.py 5 > bridge.log 2>&1 &

# Run as Windows service
# Use NSSM or Task Scheduler
```

### **Windows Service Setup**

1. **Download NSSM** (Non-Sucking Service Manager)
2. **Install as service**:
   ```cmd
   nssm install OneDriveBridge
   nssm set OneDriveBridge Application python
   nssm set OneDriveBridge AppParameters onedrive_bridge.py 5
   nssm set OneDriveBridge AppDirectory C:\path\to\python_onedrive_bridge
   nssm start OneDriveBridge
   ```

## 📊 **Monitoring & Logs**

### **View Logs**
```bash
# Python bridge logs
tail -f bridge.log

# Laravel logs
tail -f ../storage/logs/laravel.log
```

### **Test Endpoints**
```bash
# Test Laravel API
curl -X POST http://127.0.0.1:8000/api/import-excel-data \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'

# Test OneDrive connection
python -c "from onedrive_bridge import OneDriveBridge; bridge = OneDriveBridge(); print('✅ Connection test:', bridge.get_access_token())"
```

## 🔧 **Troubleshooting**

### **Common Issues**

1. **"Invalid client" error**
   - Check Azure App Registration credentials
   - Ensure client secret is not expired

2. **"Insufficient privileges" error**
   - Grant admin consent for API permissions
   - Check if permissions are application-level (not delegated)

3. **"File not found" error**
   - Verify folder ID is correct
   - Check if Excel file name matches configuration

4. **"Laravel API error"**
   - Ensure Laravel server is running
   - Check if API endpoint is accessible

### **Debug Mode**

Enable debug logging by modifying `onedrive_bridge.py`:

```python
# Add at the top
import logging
logging.basicConfig(level=logging.DEBUG)
```

## 📋 **Configuration Options**

### **Environment Variables**

| Variable | Description | Default |
|----------|-------------|---------|
| `ONEDRIVE_CLIENT_ID` | Azure App Client ID | Required |
| `ONEDRIVE_CLIENT_SECRET` | Azure App Client Secret | Required |
| `ONEDRIVE_TENANT_ID` | Azure Tenant ID | Required |
| `ONEDRIVE_FOLDER_ID` | OneDrive Folder ID | Required |
| `LARAVEL_API_URL` | Laravel API URL | `http://127.0.0.1:8000` |
| `LARAVEL_API_ENDPOINT` | API Endpoint | `/api/import-excel-data` |
| `EXCEL_FILE_NAME` | Excel file name | `students.xlsx` |
| `TEMP_DIR` | Temporary directory | `./temp` |

### **Command Line Options**

```bash
# Run with custom interval
python onedrive_bridge.py 10  # 10 minutes

# Run with default interval (5 minutes)
python onedrive_bridge.py
```

## 🎯 **Features**

- ✅ **Real-time monitoring** of OneDrive changes
- ✅ **Automatic file download** when modified
- ✅ **Excel processing** with multiple sheets support
- ✅ **Laravel API integration** for data import
- ✅ **Error handling** and logging
- ✅ **Configurable intervals** for checking
- ✅ **Temporary file cleanup**
- ✅ **Cross-platform** support (Windows, Linux, Mac)

## 🚀 **Next Steps**

1. **Set up Azure App Registration**
2. **Configure OneDrive folder**
3. **Update config.env** with your credentials
4. **Test the bridge** with short intervals
5. **Deploy to production** with appropriate intervals
6. **Monitor logs** for any issues

Your OneDrive to Laravel bridge is now ready! 🎉


