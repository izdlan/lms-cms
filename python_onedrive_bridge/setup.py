#!/usr/bin/env python3
"""
Setup script for OneDrive to Laravel Bridge
"""

import os
import sys
import subprocess
import shutil
from pathlib import Path

def check_python_version():
    """Check if Python version is compatible"""
    if sys.version_info < (3, 7):
        print("❌ Python 3.7 or higher is required")
        print(f"Current version: {sys.version}")
        return False
    print(f"✅ Python version: {sys.version}")
    return True

def install_requirements():
    """Install required packages"""
    print("📦 Installing required packages...")
    try:
        subprocess.check_call([sys.executable, "-m", "pip", "install", "-r", "requirements.txt"])
        print("✅ Requirements installed successfully")
        return True
    except subprocess.CalledProcessError as e:
        print(f"❌ Failed to install requirements: {e}")
        return False

def create_config_file():
    """Create config file from example"""
    if not os.path.exists('config.env'):
        if os.path.exists('config.env.example'):
            shutil.copy('config.env.example', 'config.env')
            print("✅ Created config.env from example")
            print("📝 Please edit config.env with your OneDrive credentials")
        else:
            print("❌ config.env.example not found")
            return False
    else:
        print("✅ config.env already exists")
    return True

def create_directories():
    """Create necessary directories"""
    directories = ['temp', 'logs']
    for directory in directories:
        Path(directory).mkdir(exist_ok=True)
        print(f"✅ Created directory: {directory}")
    return True

def test_import():
    """Test if all modules can be imported"""
    print("🧪 Testing imports...")
    try:
        import requests
        import msal
        import pandas
        import schedule
        from dotenv import load_dotenv
        print("✅ All modules imported successfully")
        return True
    except ImportError as e:
        print(f"❌ Import error: {e}")
        return False

def main():
    """Main setup function"""
    print("🐍 OneDrive to Laravel Bridge Setup")
    print("=" * 50)
    
    # Check Python version
    if not check_python_version():
        sys.exit(1)
    
    # Install requirements
    if not install_requirements():
        sys.exit(1)
    
    # Create config file
    if not create_config_file():
        sys.exit(1)
    
    # Create directories
    if not create_directories():
        sys.exit(1)
    
    # Test imports
    if not test_import():
        sys.exit(1)
    
    print("\n🎉 Setup completed successfully!")
    print("\n📋 Next steps:")
    print("1. Edit config.env with your OneDrive credentials")
    print("2. Get your OneDrive folder ID")
    print("3. Run: python onedrive_bridge.py")
    print("\n📚 For detailed setup instructions, see SETUP_GUIDE.md")

if __name__ == "__main__":
    main()


