$content = Get-Content .env
$newContent = @()
foreach ($line in $content) {
    if ($line -match '^MAIL_FROM_NAME=') {
        $newContent += 'MAIL_FROM_NAME="LMS Olympia"'
    } else {
        $newContent += $line
    }
}
$newContent | Set-Content .env
Write-Host "Fixed .env file"
