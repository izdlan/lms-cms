<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information Card</title>
    <style>
        @page {
            margin: 8mm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            color: #333;
            font-size: 16px;
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
            font-size: 28px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 15px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section h2 {
            color: #667eea;
            font-size: 22px;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            border-left: 3px solid #667eea;
        }
        
        .info-item.full-width {
            grid-column: 1 / -1;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #212529;
            font-size: 16px;
            word-break: break-word;
        }
        
        .credentials-section {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 6px;
            margin-top: 15px;
        }
        
        .credentials-section h2 {
            color: #1976d2;
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 15px;
        }
        
        .credential-item {
            background: white;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #bbdefb;
        }
        
        .credential-label {
            font-weight: bold;
            color: #1976d2;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .credential-value {
            color: #333;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 8px;
            border-radius: 3px;
            word-break: break-all;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        
        .important-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        
        .important-notice h3 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 20px;
        }
        
        .important-notice ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .webmail-section {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
            border: 2px solid #4a90e2;
        }
        
        .webmail-section h2 {
            color: #2c5aa0;
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 15px;
        }
        
        .webmail-item {
            background: white;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #b3d9ff;
        }
        
        .webmail-label {
            font-weight: bold;
            color: #2c5aa0;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .webmail-value {
            color: #333;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 8px;
            border-radius: 3px;
            word-break: break-all;
        }
        
        .instructions-section {
            background: #fff8e1;
            border: 1px solid #ffcc02;
            color: #e65100;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .instructions-section h3 {
            margin: 0 0 10px 0;
            color: #e65100;
            font-size: 20px;
        }
        
        .instructions-section ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .instructions-section li {
            margin: 4px 0;
            font-size: 16px;
        }
        
        .roundcube-info {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            text-align: center;
        }
        
        .roundcube-info h4 {
            margin: 0 0 8px 0;
            color: #2e7d32;
            font-size: 20px;
        }
        
        .roundcube-info p {
            margin: 0;
            font-size: 16px;
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
                <h2>Personal Information</h2>
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
                        <div class="info-value">{{ $student->email ?? 'Not Available' }}</div>
                    </div>
                </div>
            </div>
            
            <!-- LMS Access Information -->
            <div class="credentials-section">
                <h2>LMS Access Information</h2>
                
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
                <h3>Important Instructions</h3>
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
    
    <!-- Page Break for Second Page -->
    <div class="page-break"></div>
    
    <!-- Second Page: Webmail Information -->
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Webmail Access Information</h1>
            <p>Olympia Education - Email System</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Webmail Access Information -->
            <div class="webmail-section">
                <h2>Webmail Access Information</h2>
                
                <div class="webmail-item">
                    <div class="webmail-label">Webmail Link</div>
                    <div class="webmail-value">https://lms.olympia-education.com/webmail</div>
                </div>
                
                <div class="webmail-item">
                    <div class="webmail-label">Email Address</div>
                    <div class="webmail-value">{{ $webmail_email }}</div>
                </div>
                
                <div class="webmail-item">
                    <div class="webmail-label">Password</div>
                    <div class="webmail-value">{{ $username }}</div>
                </div>
            </div>
            
            <!-- Login Instructions -->
            <div class="instructions-section">
                <h3>Login Instructions</h3>
                <ol>
                    <li>Open your web browser and go to the webmail link above</li>
                    <li>Enter your email address and password as shown above</li>
                    <li>Click "Login" to access your email account</li>
                    <li>Look for the <strong>"Open" button below the Roundcube picture</strong></li>
                    <li>You will see the Roundcube webmail interface</li>
                    <li>Click the "Open" button to access the mail main page</li>
                </ol>
            </div>
            
            <!-- Roundcube Information -->
            <div class="roundcube-info">
                <h4>Roundcube Webmail</h4>
                <p>After logging in, you will see the Roundcube interface. Look for the "Open" button below the Roundcube picture to access your main email page.</p>
            </div>
            
            <!-- Password Change Instructions -->
            <div class="important-notice">
                <h3>Password Security</h3>
                <ul>
                    <li>You can change your webmail password after logging in</li>
                    <li>Go to Settings → Password to change your password</li>
                    <li>Use a strong password for better security</li>
                    <li>Keep your login credentials secure and private</li>
                    <li>Contact IT support if you have login issues</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ date('F j, Y \a\t g:i A') }} | Olympia Education Webmail</p>
            <p>For webmail support, contact:support@olympia-education.com</p>
        </div>
    </div>
</body>
</html>
