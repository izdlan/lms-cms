#!/usr/bin/env python3
"""
Test script for OneDrive to Laravel Bridge
Tests OneDrive connection and Laravel API
"""

import os
import sys
import json
import requests
from dotenv import load_dotenv
from msal import ConfidentialClientApplication

def test_environment():
    """Test if environment is properly configured"""
    print("🧪 Testing environment configuration...")
    
    # Load environment variables
    load_dotenv('config.env')
    
    required_vars = [
        'ONEDRIVE_CLIENT_ID',
        'ONEDRIVE_CLIENT_SECRET', 
        'ONEDRIVE_TENANT_ID',
        'ONEDRIVE_FOLDER_ID',
        'LARAVEL_API_URL'
    ]
    
    missing_vars = []
    for var in required_vars:
        if not os.getenv(var):
            missing_vars.append(var)
    
    if missing_vars:
        print(f"❌ Missing environment variables: {', '.join(missing_vars)}")
        print("📝 Please check your config.env file")
        return False
    
    print("✅ All required environment variables found")
    return True

def test_onedrive_connection():
    """Test OneDrive API connection"""
    print("\n🔗 Testing OneDrive connection...")
    
    try:
        client_id = os.getenv('ONEDRIVE_CLIENT_ID')
        client_secret = os.getenv('ONEDRIVE_CLIENT_SECRET')
        tenant_id = os.getenv('ONEDRIVE_TENANT_ID')
        
        # Initialize MSAL
        app = ConfidentialClientApplication(
            client_id=client_id,
            client_credential=client_secret,
            authority=f"https://login.microsoftonline.com/{tenant_id}"
        )
        
        # Get access token
        scopes = ["https://graph.microsoft.com/.default"]
        result = app.acquire_token_for_client(scopes=scopes)
        
        if "access_token" in result:
            print("✅ OneDrive access token obtained")
            
            # Test API call
            headers = {
                'Authorization': f'Bearer {result["access_token"]}',
                'Content-Type': 'application/json'
            }
            
            # Test folder access
            folder_id = os.getenv('ONEDRIVE_FOLDER_ID')
            url = f"https://graph.microsoft.com/v1.0/me/drive/items/{folder_id}"
            
            response = requests.get(url, headers=headers)
            
            if response.status_code == 200:
                folder_info = response.json()
                print(f"✅ OneDrive folder accessible: {folder_info.get('name', 'Unknown')}")
                return True
            else:
                print(f"❌ OneDrive folder access failed: {response.status_code}")
                print(f"Response: {response.text}")
                return False
        else:
            print(f"❌ Failed to get access token: {result.get('error_description', 'Unknown error')}")
            return False
            
    except Exception as e:
        print(f"❌ OneDrive connection error: {e}")
        return False

def test_laravel_api():
    """Test Laravel API connection"""
    print("\n🌐 Testing Laravel API connection...")
    
    try:
        laravel_url = os.getenv('LARAVEL_API_URL', 'http://127.0.0.1:8000')
        api_endpoint = os.getenv('LARAVEL_API_ENDPOINT', '/api/import-excel-data')
        
        # Test data
        test_data = {
            'timestamp': '2024-01-01T00:00:00',
            'source': 'python_test',
            'sheets': {
                'Test Sheet': {
                    'headers': ['Name', 'IC', 'Email'],
                    'data': [
                        ['John Doe', '123456789', 'john@example.com'],
                        ['Jane Smith', '987654321', 'jane@example.com']
                    ],
                    'row_count': 2
                }
            }
        }
        
        url = f"{laravel_url}{api_endpoint}"
        
        response = requests.post(url, json=test_data, timeout=10)
        
        if response.status_code == 200:
            result = response.json()
            print("✅ Laravel API connection successful")
            print(f"📊 Response: {result.get('message', 'No message')}")
            return True
        else:
            print(f"❌ Laravel API error: {response.status_code}")
            print(f"Response: {response.text}")
            return False
            
    except requests.exceptions.ConnectionError:
        print("❌ Cannot connect to Laravel API")
        print("💡 Make sure Laravel server is running on the configured URL")
        return False
    except Exception as e:
        print(f"❌ Laravel API error: {e}")
        return False

def test_file_processing():
    """Test Excel file processing capabilities"""
    print("\n📊 Testing Excel file processing...")
    
    try:
        import pandas as pd
        print("✅ Pandas library available")
        
        # Create a test Excel file
        test_data = {
            'Name': ['John Doe', 'Jane Smith'],
            'IC': ['123456789', '987654321'],
            'Email': ['john@example.com', 'jane@example.com']
        }
        
        df = pd.DataFrame(test_data)
        test_file = 'test_students.xlsx'
        df.to_excel(test_file, index=False)
        
        # Test reading
        df_read = pd.read_excel(test_file)
        print(f"✅ Excel file processing works: {len(df_read)} rows")
        
        # Clean up
        os.remove(test_file)
        print("✅ Test file cleaned up")
        
        return True
        
    except ImportError:
        print("❌ Pandas library not available")
        print("💡 Run: pip install pandas openpyxl")
        return False
    except Exception as e:
        print(f"❌ Excel processing error: {e}")
        return False

def main():
    """Main test function"""
    print("🧪 OneDrive to Laravel Bridge - Connection Test")
    print("=" * 60)
    
    tests = [
        ("Environment Configuration", test_environment),
        ("OneDrive Connection", test_onedrive_connection),
        ("Laravel API Connection", test_laravel_api),
        ("Excel File Processing", test_file_processing)
    ]
    
    results = []
    
    for test_name, test_func in tests:
        try:
            result = test_func()
            results.append((test_name, result))
        except Exception as e:
            print(f"❌ {test_name} failed with exception: {e}")
            results.append((test_name, False))
    
    print("\n" + "=" * 60)
    print("📋 Test Results Summary:")
    print("=" * 60)
    
    all_passed = True
    for test_name, result in results:
        status = "✅ PASS" if result else "❌ FAIL"
        print(f"{status} - {test_name}")
        if not result:
            all_passed = False
    
    print("=" * 60)
    
    if all_passed:
        print("🎉 All tests passed! Your bridge is ready to use.")
        print("\n🚀 Next steps:")
        print("1. Run: python onedrive_bridge.py")
        print("2. Monitor the logs for any issues")
        print("3. Check your Laravel application for imported data")
    else:
        print("❌ Some tests failed. Please fix the issues above.")
        print("\n🔧 Troubleshooting:")
        print("1. Check your config.env file")
        print("2. Verify Azure App Registration setup")
        print("3. Ensure Laravel server is running")
        print("4. Install missing Python packages")

if __name__ == "__main__":
    main()


