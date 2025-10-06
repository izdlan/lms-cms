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
                <p>Verify your identity using QR code from your certificate</p>
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
                        <div class="step-text">Scan the QR code on your certificate using your phone camera</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-text">Or upload a photo of the QR code</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-text">Or enter your student ID manually</div>
                    </div>
                </div>
                
                <!-- QR Code Scanner Section -->
                <div class="qr-scanner-section">
                    <div class="qr-scanner" id="qrScanner">
                        <i class="fas fa-qrcode qr-icon"></i>
                        <div class="qr-text">Scan QR code from your certificate</div>
                        <button type="button" class="btn btn-scan" onclick="startCamera()">
                            <i class="fas fa-camera"></i> Start Camera
                        </button>
                        <input type="file" id="qrFileInput" class="file-input" accept="image/*" onchange="handleFileUpload(event)">
                        <button type="button" class="btn btn-outline-secondary mt-2" onclick="document.getElementById('qrFileInput').click()">
                            <i class="fas fa-upload"></i> Upload Image
                        </button>
                    </div>
                    
                    <div class="loading" id="loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <p class="mt-2">Processing QR code...</p>
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

    <!-- Camera Modal -->
    <div class="modal fade" id="cameraModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QR Code Scanner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="cameraVideo" width="100%" height="300" style="display: none;"></video>
                    <canvas id="qrCanvas" width="400" height="300" style="display: none;"></canvas>
                    <div id="cameraPlaceholder">
                        <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                        <p>Camera will start here</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="captureBtn" style="display: none;">Capture</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        let qrScanner = null;
        let stream = null;

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

        // Start camera
        function startCamera() {
            const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
            modal.show();
            
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    const video = document.getElementById('cameraVideo');
                    video.srcObject = mediaStream;
                    video.style.display = 'block';
                    document.getElementById('cameraPlaceholder').style.display = 'none';
                    document.getElementById('captureBtn').style.display = 'inline-block';
                    
                    // Start QR scanning with jsQR
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    
                    function scanQR() {
                        if (video.readyState === video.HAVE_ENOUGH_DATA) {
                            canvas.height = video.videoHeight;
                            canvas.width = video.videoWidth;
                            context.drawImage(video, 0, 0, canvas.width, canvas.height);
                            
                            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                            const code = jsQR(imageData.data, imageData.width, imageData.height);
                            
                            if (code) {
                                console.log('QR Code found:', code.data);
                                handleQrResult(code.data);
                                modal.hide();
                                return;
                            }
                        }
                        requestAnimationFrame(scanQR);
                    }
                    
                    video.addEventListener('loadedmetadata', scanQR);
                })
                .catch(function(err) {
                    console.error('Camera access denied:', err);
                    alert('Camera access is required for QR scanning');
                });
        }

        // Handle QR result
        function handleQrResult(result) {
            showLoading();
            
            console.log('QR Code scanned:', result);
            
            // Extract student ID from the QR code result
            let studentId = null;
            
            // Check if the result contains a student ID pattern
            const studentIdMatch = result.match(/student_id=([^&]+)/);
            if (studentIdMatch) {
                studentId = studentIdMatch[1];
            } else if (result.includes('670219-08-6113')) {
                studentId = '670219-08-6113';
            } else if (result.includes('670220-09-7224')) {
                studentId = '670220-09-7224';
            } else if (result.includes('670221-10-8335')) {
                studentId = '670221-10-8335';
            } else if (result.includes('670222-11-9446')) {
                studentId = '670222-11-9446';
            }
            
            setTimeout(() => {
                if (studentId) {
                    window.location.href = `{{ route('ex-student.verify') }}?student_id=${encodeURIComponent(studentId)}`;
                } else {
                    alert('Invalid QR code. Please try again or enter your student ID manually.');
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('qrScanner').style.display = 'block';
                }
            }, 1000);
        }

        // Handle file upload
        function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            showLoading();
            
            // Create a canvas to process the image
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = function() {
                // Set canvas size to match image
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                
                // Use jsQR to decode the QR code
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    console.log('QR Code from image:', code.data);
                    handleQrResult(code.data);
                } else {
                    console.log('No QR code found in image');
                    
                    // Fallback: Show manual input option
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('qrScanner').style.display = 'block';
                    
                    // Show a more helpful error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-warning mt-3';
                    errorDiv.innerHTML = `
                        <i class="fas fa-exclamation-triangle"></i> 
                        Could not read QR code from image. 
                        <br><strong>Please try:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Ensure the QR code is clear and not blurry</li>
                            <li>Make sure the entire QR code is visible in the image</li>
                            <li>Try taking a new photo with better lighting</li>
                            <li>Or enter your student ID manually below</li>
                        </ul>
                    `;
                    
                    // Remove any existing error messages
                    const existingError = document.querySelector('.alert-warning');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    document.getElementById('qrScanner').appendChild(errorDiv);
                }
            };
            
            img.onerror = function() {
                console.error('Failed to load image');
                document.getElementById('loading').style.display = 'none';
                document.getElementById('qrScanner').style.display = 'block';
                alert('Failed to load image. Please try a different image format (JPG, PNG, etc.)');
            };
            
            img.src = URL.createObjectURL(file);
        }

        // Show loading state
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('qrScanner').style.display = 'none';
        }

        // Clean up camera when modal is closed
        document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        });
    </script>
</body>
</html>
