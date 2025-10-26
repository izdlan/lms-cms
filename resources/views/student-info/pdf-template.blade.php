<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information Card</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            color: #333;
            font-size: 12px;
        }
        
        .container {
            width: 100%;
            background: white;
            border: 2px solid #667eea;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            opacity: 0.9;
        }
        
        .content {
            padding: 20px;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-section h2 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #667eea;
        }
        
        .info-item.full-width {
            grid-column: 1 / -1;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 11px;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #212529;
            font-size: 12px;
            word-break: break-word;
        }
        
        .credentials-section {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
        
        .credentials-section h2 {
            color: #1976d2;
            margin-top: 0;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .credential-item {
            background: white;
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #bbdefb;
        }
        
        .credential-label {
            font-weight: bold;
            color: #1976d2;
            font-size: 11px;
            margin-bottom: 3px;
        }
        
        .credential-value {
            color: #333;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 4px;
            border-radius: 3px;
            word-break: break-all;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 10px;
            text-align: center;
            color: #6c757d;
            font-size: 10px;
            border-top: 1px solid #dee2e6;
        }
        
        .important-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .important-notice h3 {
            margin: 0 0 5px 0;
            color: #856404;
            font-size: 14px;
        }
        
        .important-notice ul {
            margin: 5px 0;
            padding-left: 15px;
        }
        
        .important-notice li {
            margin: 2px 0;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Student Information Card</h1>
            <p>Olympia Education - Learning Management System</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Personal Information -->
            <div class="info-section">
                <h2>📋 Personal Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Name</div>
                        <div class="info-value">{{ $student->name ?? 'Not Available' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Student ID</div>
                        <div class="info-value">{{ $student->student_id ?? 'Not Available' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Program</div>
                        <div class="info-value">{{ $student->programme_name ?? 'Not Available' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $student->email ?? $student->student_email ?? 'Not Available' }}</div>
                    </div>
                </div>
            </div>
            
            <!-- LMS Access Information -->
            <div class="credentials-section">
                <h2>🔐 LMS Access Information</h2>
                
                <div class="credential-item">
                    <div class="credential-label">LMS Link</div>
                    <div class="credential-value">{{ $lms_link }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Username</div>
                    <div class="credential-value">{{ $username }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Password</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>
            </div>
            
            <!-- Important Notice -->
            <div class="important-notice">
                <h3>⚠️ Important Instructions</h3>
                <ul>
                    <li>Keep this information card safe and confidential</li>
                    <li>Use the provided credentials to access the LMS system</li>
                    <li>Change your password after first login for security</li>
                    <li>Contact support if you have any login issues</li>
                    <li>This card contains sensitive information - do not share</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ date('F j, Y \a\t g:i A') }} | Olympia Education LMS</p>
            <p>For support, contact: support@olympia-education.com</p>
        </div>
    </div>
</body>
</html>
