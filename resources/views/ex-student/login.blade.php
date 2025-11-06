<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ex-Student Verification | Olympia Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .verification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .card-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .card-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .qr-scanner-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .qr-scanner {
            border: 3px dashed #e9ecef;
            border-radius: 15px;
            padding: 2rem;
            margin: 1rem 0;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .qr-scanner:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }
        
        .qr-scanner.dragover {
            border-color: #667eea;
            background: #e8f0fe;
        }
        
        .qr-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .qr-text {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        .file-input {
            display: none;
        }
        
        .btn-scan {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-scan:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .manual-input {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-verify {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .loading {
            display: none;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        .verification-steps {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .step:last-child {
            margin-bottom: 0;
        }
        
        .step-number {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
            font-size: 0.9rem;
        }
        
        .step-text {
            color: #495057;
            font-size: 0.95rem;
        }
        
        @media (max-width: 768px) {
            .verification-container {
                padding: 10px;
            }
            
            .card-header h1 {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="card-header">
                <h1><i class="fas fa-graduation-cap"></i> Ex-Student Verification</h1>
                <p>Enter your student ID to verify your record</p>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    </div>
                @endif
                
                <div class="verification-steps">
                    <h6 class="mb-3"><i class="fas fa-info-circle"></i> How to verify:</h6>
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-text">Enter your student ID below and click Verify</div>
                    </div>
                </div>
                
                
                
                <!-- Manual Input Section -->
                <div class="manual-input">
                    <h6 class="mb-3"><i class="fas fa-keyboard"></i> Manual Verification</h6>
                    <form id="manualForm">
                        @csrf
                        <div class="mb-3">
                            <label for="studentId" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="studentId" name="student_id" 
                                   placeholder="Enter your student ID (e.g., 670219-08-6113)" required>
                            <div class="form-text">Available student IDs: 670219-08-6113, 670220-09-7224, 670221-10-8335, 670222-11-9446</div>
                        </div>
                        <button type="submit" class="btn btn-verify">
                            <i class="fas fa-check"></i> Verify Identity
                        </button>
                    </form>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt"></i> Your information is secure and encrypted
                    </small>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Manual form submission
        document.getElementById('manualForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const studentId = document.getElementById('studentId').value;
            if (!studentId) {
                alert('Please enter your student ID');
                return;
            }
            
            // For manual verification, we'll redirect to verify page
            window.location.href = `{{ route('ex-student.verify') }}?student_id=${encodeURIComponent(studentId)}`;
        });
    </script>
</body>
</html>
