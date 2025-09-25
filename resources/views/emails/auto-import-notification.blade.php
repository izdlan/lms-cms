<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LMS Auto Import Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; }
        .stats { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { color: #28a745; font-weight: bold; }
        .info { color: #17a2b8; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎓 LMS Auto Import Notification</h2>
            <p>New students have been automatically imported from OneDrive</p>
        </div>
        
        <div class="content">
            <h3>📊 Import Summary</h3>
            <div class="stats">
                <p><span class="success">✅ Created:</span> {{ $created }} new students</p>
                <p><span class="info">🔄 Updated:</span> {{ $updated }} existing students</p>
                <p><span class="info">⚠️ Errors:</span> {{ $errors }} errors</p>
                <p><strong>🕐 Import Time:</strong> {{ $timestamp }}</p>
            </div>
            
            @if(!empty($processed_sheets))
            <h3>📋 Sheet-by-Sheet Results</h3>
            <div class="stats">
                @foreach($processed_sheets as $sheet)
                <p><strong>{{ $sheet['sheet'] }}:</strong> 
                   Created={{ $sheet['created'] }}, 
                   Updated={{ $sheet['updated'] }}, 
                   Errors={{ $sheet['errors'] }}
                </p>
                @endforeach
            </div>
            @endif
            
            <div class="stats">
                <p><strong>🔗 Action Required:</strong></p>
                <p>Please log in to the LMS admin panel to review the imported students and verify their information.</p>
                <p><a href="{{ url('/admin/students') }}" style="color: #007bff;">View Students →</a></p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from the LMS system.</p>
            <p>If you no longer wish to receive these notifications, please contact the system administrator.</p>
        </div>
    </div>
</body>
</html>
