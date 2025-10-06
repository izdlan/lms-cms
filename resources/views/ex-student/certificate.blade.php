<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate | {{ $exStudent->name }} | Olympia Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Times New Roman', serif;
        }
        
        .certificate-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .certificate {
            background: white;
            border: 5px solid #2c3e50;
            border-radius: 10px;
            padding: 60px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            min-height: 600px;
        }
        
        .certificate::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            pointer-events: none;
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .university-logo {
            width: 80px;
            height: 80px;
            background: #2c3e50;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .university-name {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .university-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .certificate-title {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .certificate-body {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .certificate-text {
            font-size: 1.3rem;
            line-height: 1.8;
            color: #495057;
            margin-bottom: 30px;
        }
        
        .student-name {
            font-size: 2.2rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 30px 0;
            text-decoration: underline;
            text-decoration-color: #667eea;
            text-decoration-thickness: 3px;
        }
        
        .certificate-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 40px 0;
        }
        
        .detail-item {
            text-align: left;
        }
        
        .detail-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 1.1rem;
        }
        
        .detail-value {
            color: #495057;
            font-size: 1.1rem;
            margin-top: 5px;
        }
        
        .certificate-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }
        
        .signature-section {
            text-align: center;
            flex: 1;
        }
        
        .signature-line {
            border-bottom: 2px solid #2c3e50;
            width: 200px;
            margin: 0 auto 10px;
            height: 50px;
        }
        
        .signature-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 1rem;
        }
        
        .qr-section {
            text-align: center;
            flex: 1;
        }
        
        .qr-code {
            width: 120px;
            height: 120px;
            margin: 0 auto 10px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            background: white;
        }
        
        .qr-text {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .verification-info {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }
        
        .verification-info h6 {
            color: #0c5460;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .verification-info p {
            color: #0c5460;
            margin: 5px 0;
            font-size: 0.95rem;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        @media print {
            .print-button,
            .back-button {
                display: none;
            }
            
            .certificate-container {
                padding: 0;
            }
            
            .certificate {
                box-shadow: none;
                border: 5px solid #000;
            }
        }
        
        @media (max-width: 768px) {
            .certificate {
                padding: 30px;
            }
            
            .university-name {
                font-size: 1.8rem;
            }
            
            .certificate-title {
                font-size: 1.5rem;
            }
            
            .student-name {
                font-size: 1.8rem;
            }
            
            .certificate-details {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .certificate-footer {
                flex-direction: column;
                gap: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Action Buttons -->
        <div class="print-button">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
        
        <div class="back-button">
            <a href="{{ route('ex-student.dashboard', ['student_id' => $exStudent->student_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('ex-student.transcript1', ['student_id' => $exStudent->student_id]) }}" class="btn btn-primary">
                <i class="fas fa-file-alt"></i> View Transcript
            </a>
        </div>
        
        <!-- Certificate -->
        <div class="certificate">
            <div class="certificate-header">
                <div class="university-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="university-name">Olympia Education</h1>
                <p class="university-subtitle">Excellence in Education Since 1995</p>
            </div>
            
            <div class="certificate-body">
                <h2 class="certificate-title">Certificate of Graduation</h2>
                
                <p class="certificate-text">
                    This is to certify that
                </p>
                
                <div class="student-name">
                    {{ $exStudent->name }}
                </div>
                
                <p class="certificate-text">
                    has successfully completed the requirements for the degree of
                </p>
                
                <div class="certificate-details">
                    <div class="detail-item">
                        <div class="detail-label">Program:</div>
                        <div class="detail-value">{{ $exStudent->program ?? 'Bachelor of Science' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Student ID:</div>
                        <div class="detail-value">{{ $exStudent->student_id }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Graduation Date:</div>
                        <div class="detail-value">{{ $exStudent->graduation_date }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">CGPA:</div>
                        <div class="detail-value">{{ $exStudent->formatted_cgpa }}</div>
                    </div>
                </div>
                
                <p class="certificate-text">
                    and is hereby awarded this certificate on this {{ now()->format('jS \of F, Y') }}.
                </p>
            </div>
            
            <div class="certificate-footer">
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-label">Registrar</div>
                </div>
                
                <div class="qr-section">
                    <div class="qr-code">
                        <img src="{{ asset('storage/' . $qrCodePath) }}" alt="QR Code" style="width: 100px; height: 100px;">
                    </div>
                    <div class="qr-text">Verification QR Code</div>
                </div>
                
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-label">Vice Chancellor</div>
                </div>
            </div>
        </div>
        
        <div class="verification-info">
            <h6><i class="fas fa-shield-check"></i> Certificate Verification</h6>
            <p><strong>Certificate Number:</strong> {{ $exStudent->certificate_number }}</p>
            <p><strong>Verification URL:</strong> {{ $exStudent->getVerificationUrl() }}</p>
            <p><strong>Issued Date:</strong> {{ now()->format('F j, Y') }}</p>
            <p class="mb-0"><strong>Status:</strong> <span class="text-success">Verified and Authentic</span></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
