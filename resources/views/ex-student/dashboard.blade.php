<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ex-Student Dashboard | Olympia Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-container {
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .dashboard-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .dashboard-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .student-info {
            background: #f8f9fa;
            padding: 2rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
            font-weight: 500;
        }
        
        .documents-section {
            padding: 2rem;
        }
        
        .document-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .document-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.1);
        }
        
        .document-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .document-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .document-description {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .verification-status {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .logout-section {
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-logout {
            background: #dc3545;
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
            color: white;
        }
        
        .qr-display {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        .qr-code {
            max-width: 200px;
            height: auto;
            margin: 0 auto;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }
            
            .dashboard-header h1 {
                font-size: 1.5rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h1><i class="fas fa-graduation-cap"></i> Ex-Student Dashboard</h1>
                <p>Welcome back, {{ $exStudent->name }}</p>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success m-3">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            <div class="verification-status">
                <i class="fas fa-shield-check"></i>
                <span>Identity verified successfully</span>
            </div>
            
            <div class="student-info">
                <h5 class="mb-3"><i class="fas fa-user"></i> Student Information</h5>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value">{{ $exStudent->student_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $exStudent->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Program:</span>
                        <span class="info-value">{{ $exStudent->program ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Graduation Year:</span>
                        <span class="info-value">{{ $exStudent->graduation_date }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">CGPA:</span>
                        <span class="info-value">{{ $exStudent->formatted_cgpa }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Certificate Number:</span>
                        <span class="info-value">{{ $exStudent->certificate_number }}</span>
                    </div>
                </div>
            </div>
            
            <div class="documents-section">
                <h5 class="mb-3"><i class="fas fa-file-alt"></i> Your Documents</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="document-card" onclick="viewCertificate()">
                            <div class="text-center">
                                <i class="fas fa-certificate document-icon"></i>
                                <div class="document-title">Certificate</div>
                                <div class="document-description">View your graduation certificate</div>
                                <button class="btn btn-view">
                                    <i class="fas fa-eye"></i> View Certificate
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="document-card" onclick="viewTranscript()">
                            <div class="text-center">
                                <i class="fas fa-scroll document-icon"></i>
                                <div class="document-title">Academic Transcript</div>
                                <div class="document-description">View your academic records</div>
                                <button class="btn btn-view">
                                    <i class="fas fa-eye"></i> View Transcript
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="qr-display">
                    <h6><i class="fas fa-qrcode"></i> Your QR Code</h6>
                    <p class="text-muted">This QR code is printed on your certificate for verification</p>
                    <div id="qrCodeContainer">
                        <!-- QR code will be generated here -->
                    </div>
                </div>
            </div>
            
            <div class="logout-section">
                <form action="{{ route('ex-student.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js"></script>
    <script>
        // Generate QR code for display
        function generateQRCode() {
            const qrData = {
                student_id: '{{ $exStudent->student_id }}',
                certificate_number: '{{ $exStudent->certificate_number }}',
                verification_url: '{{ $exStudent->getVerificationUrl() }}',
                timestamp: Date.now()
            };
            
            const qrString = JSON.stringify(qrData);
            
            QRCode.toCanvas(document.getElementById('qrCodeContainer'), qrString, {
                width: 200,
                height: 200,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            }, function (error) {
                if (error) console.error('QR Code generation error:', error);
            });
        }
        
        // View certificate
        function viewCertificate() {
            window.open('{{ route("ex-student.certificate", ["student_id" => $exStudent->student_id]) }}', '_blank');
        }
        
        // View transcript
        function viewTranscript() {
            window.open('{{ route("ex-student.transcript1", ["student_id" => $exStudent->student_id]) }}', '_blank');
        }
        
        // Generate QR code on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateQRCode();
        });
    </script>
</body>
</html>
