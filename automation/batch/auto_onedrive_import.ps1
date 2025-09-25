# Automated OneDrive Import Script
# This script runs every 5 minutes to check for new students

$LogFile = "C:\xampp\htdocs\LMS_Olympia\automation\logs\auto_import.log"
$ProjectPath = "C:\xampp\htdocs\LMS_Olympia"

# Create log directory if it doesn't exist
$LogDir = Split-Path $LogFile -Parent
if (!(Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir -Force
}

# Function to write log
function Write-Log {
    param($Message)
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] $Message"
    Write-Host $LogEntry
    Add-Content -Path $LogFile -Value $LogEntry
}

Write-Log "Starting automated OneDrive import..."

try {
    # Change to project directory
    Set-Location $ProjectPath
    
    # Run the import command
    $Result = php artisan import:onedrive-auto 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Log "✅ OneDrive import completed successfully"
        Write-Log "Output: $Result"
    } else {
        Write-Log "❌ OneDrive import failed with exit code: $LASTEXITCODE"
        Write-Log "Error: $Result"
    }
    
} catch {
    Write-Log "❌ Error during automated import: $($_.Exception.Message)"
} finally {
    Write-Log "Automated OneDrive import process completed"
    Write-Log "----------------------------------------"
}
