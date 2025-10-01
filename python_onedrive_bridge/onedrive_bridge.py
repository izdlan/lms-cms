#!/usr/bin/env python3
"""
OneDrive to Laravel Bridge
Monitors OneDrive for Excel file changes and sends data to Laravel API
"""

import os
import sys
import json
import time
import schedule
import requests
import pandas as pd
from datetime import datetime
from pathlib import Path
from dotenv import load_dotenv
from msal import ConfidentialClientApplication

class OneDriveBridge:
    def __init__(self):
        # Load environment variables
        load_dotenv('config.env')
        
        # OneDrive configuration
        self.client_id = os.getenv('ONEDRIVE_CLIENT_ID')
        self.client_secret = os.getenv('ONEDRIVE_CLIENT_SECRET')
        self.tenant_id = os.getenv('ONEDRIVE_TENANT_ID')
        self.folder_id = os.getenv('ONEDRIVE_FOLDER_ID')
        
        # Laravel API configuration
        self.laravel_url = os.getenv('LARAVEL_API_URL', 'http://127.0.0.1:8000')
        self.api_endpoint = os.getenv('LARAVEL_API_ENDPOINT', '/api/import-excel-data')
        
        # File configuration
        self.excel_filename = os.getenv('EXCEL_FILE_NAME', 'students.xlsx')
        self.temp_dir = Path(os.getenv('TEMP_DIR', './temp'))
        self.temp_dir.mkdir(exist_ok=True)
        
        # State tracking
        self.last_modified = None
        self.access_token = None
        
        # Initialize MSAL
        self.app = ConfidentialClientApplication(
            client_id=self.client_id,
            client_credential=self.client_secret,
            authority=f"https://login.microsoftonline.com/{self.tenant_id}"
        )
        
        print(f"🚀 OneDrive Bridge initialized")
        print(f"📁 Monitoring folder: {self.folder_id}")
        print(f"🌐 Laravel API: {self.laravel_url}{self.api_endpoint}")
    
    def get_access_token(self):
        """Get access token for OneDrive API"""
        try:
            scopes = ["https://graph.microsoft.com/.default"]
            result = self.app.acquire_token_silent(scopes, account=None)
            
            if not result:
                result = self.app.acquire_token_for_client(scopes=scopes)
            
            if "access_token" in result:
                self.access_token = result["access_token"]
                print("✅ Access token obtained")
                return True
            else:
                print(f"❌ Failed to get access token: {result.get('error_description', 'Unknown error')}")
                return False
                
        except Exception as e:
            print(f"❌ Error getting access token: {e}")
            return False
    
    def check_file_changes(self):
        """Check if the Excel file has been modified"""
        try:
            if not self.access_token:
                if not self.get_access_token():
                    return False
            
            # Get file metadata
            headers = {
                'Authorization': f'Bearer {self.access_token}',
                'Content-Type': 'application/json'
            }
            
            # Search for the Excel file in the folder
            search_url = f"https://graph.microsoft.com/v1.0/me/drive/items/{self.folder_id}/children"
            params = {
                '$filter': f"name eq '{self.excel_filename}'"
            }
            
            response = requests.get(search_url, headers=headers, params=params)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('value'):
                    file_info = data['value'][0]
                    current_modified = file_info.get('lastModifiedDateTime')
                    
                    if current_modified != self.last_modified:
                        print(f"📝 File modified: {current_modified}")
                        self.last_modified = current_modified
                        return file_info
                else:
                    print(f"⚠️  File '{self.excel_filename}' not found in folder")
            else:
                print(f"❌ Error checking file: {response.status_code} - {response.text}")
                
        except Exception as e:
            print(f"❌ Error checking file changes: {e}")
        
        return False
    
    def download_excel_file(self, file_info):
        """Download the Excel file from OneDrive"""
        try:
            file_id = file_info['id']
            download_url = f"https://graph.microsoft.com/v1.0/me/drive/items/{file_id}/content"
            
            headers = {
                'Authorization': f'Bearer {self.access_token}'
            }
            
            response = requests.get(download_url, headers=headers)
            
            if response.status_code == 200:
                # Save to temporary file
                temp_file = self.temp_dir / f"temp_{int(time.time())}.xlsx"
                with open(temp_file, 'wb') as f:
                    f.write(response.content)
                
                print(f"✅ File downloaded: {temp_file}")
                return temp_file
            else:
                print(f"❌ Error downloading file: {response.status_code}")
                
        except Exception as e:
            print(f"❌ Error downloading file: {e}")
        
        return None
    
    def process_excel_file(self, file_path):
        """Process Excel file and extract data"""
        try:
            # Read Excel file
            excel_file = pd.ExcelFile(file_path)
            sheets_data = {}
            
            # Process each sheet
            for sheet_name in excel_file.sheet_names:
                print(f"📊 Processing sheet: {sheet_name}")
                
                # Read sheet data
                df = pd.read_excel(file_path, sheet_name=sheet_name)
                
                # Convert to JSON-serializable format
                sheets_data[sheet_name] = {
                    'headers': df.columns.tolist(),
                    'data': df.fillna('').values.tolist(),
                    'row_count': len(df)
                }
            
            print(f"✅ Processed {len(sheets_data)} sheets")
            return sheets_data
            
        except Exception as e:
            print(f"❌ Error processing Excel file: {e}")
            return None
    
    def send_to_laravel(self, sheets_data):
        """Send processed data to Laravel API"""
        try:
            url = f"{self.laravel_url}{self.api_endpoint}"
            
            payload = {
                'timestamp': datetime.now().isoformat(),
                'source': 'onedrive_python_bridge',
                'sheets': sheets_data
            }
            
            response = requests.post(url, json=payload, timeout=30)
            
            if response.status_code == 200:
                result = response.json()
                print(f"✅ Data sent to Laravel successfully")
                print(f"📊 Result: {result}")
                return True
            else:
                print(f"❌ Laravel API error: {response.status_code} - {response.text}")
                return False
                
        except Exception as e:
            print(f"❌ Error sending to Laravel: {e}")
            return False
    
    def cleanup_temp_files(self):
        """Clean up temporary files"""
        try:
            for file in self.temp_dir.glob("temp_*.xlsx"):
                file.unlink()
            print("🧹 Cleaned up temporary files")
        except Exception as e:
            print(f"⚠️  Error cleaning up files: {e}")
    
    def sync_onedrive_to_laravel(self):
        """Main sync function"""
        print(f"\n🔄 Starting OneDrive sync at {datetime.now()}")
        
        # Check for file changes
        file_info = self.check_file_changes()
        
        if file_info:
            # Download the file
            temp_file = self.download_excel_file(file_info)
            
            if temp_file:
                # Process the Excel file
                sheets_data = self.process_excel_file(temp_file)
                
                if sheets_data:
                    # Send to Laravel
                    success = self.send_to_laravel(sheets_data)
                    
                    if success:
                        print("🎉 Sync completed successfully!")
                    else:
                        print("❌ Sync failed at Laravel API")
                else:
                    print("❌ Sync failed at Excel processing")
                
                # Clean up
                self.cleanup_temp_files()
            else:
                print("❌ Sync failed at file download")
        else:
            print("ℹ️  No changes detected")
    
    def run_scheduler(self, interval_minutes=5):
        """Run the scheduler"""
        print(f"⏰ Starting scheduler (checking every {interval_minutes} minutes)")
        print("Press Ctrl+C to stop")
        
        # Schedule the sync function
        schedule.every(interval_minutes).minutes.do(self.sync_onedrive_to_laravel)
        
        # Run initial sync
        self.sync_onedrive_to_laravel()
        
        # Keep running
        try:
            while True:
                schedule.run_pending()
                time.sleep(60)  # Check every minute
        except KeyboardInterrupt:
            print("\n🛑 Scheduler stopped by user")
            self.cleanup_temp_files()

def main():
    """Main function"""
    print("🐍 OneDrive to Laravel Bridge")
    print("=" * 50)
    
    # Check if config file exists
    if not os.path.exists('config.env'):
        print("❌ config.env file not found!")
        print("📝 Please copy config.env.example to config.env and fill in your details")
        sys.exit(1)
    
    # Initialize bridge
    bridge = OneDriveBridge()
    
    # Get interval from command line or use default
    interval = 5
    if len(sys.argv) > 1:
        try:
            interval = int(sys.argv[1])
        except ValueError:
            print("⚠️  Invalid interval, using default 5 minutes")
    
    # Start scheduler
    bridge.run_scheduler(interval)

if __name__ == "__main__":
    main()


