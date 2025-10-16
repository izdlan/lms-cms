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
            color: #dc3545;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .university-subtitle {
            font-size: 1.2rem;
            color: #007bff;
            margin-bottom: 20px;
            text-transform: uppercase;
            text-decoration: underline;
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
            font-style: italic;
        }
        
        .course-name {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 30px 0;
            text-decoration: underline;
            text-decoration-color: #667eea;
            text-decoration-thickness: 3px;
            font-style: italic;
        }
        
        .graduation-date {
            font-size: 1.3rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
            font-style: italic;
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
        
        .signature-subtitle {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
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
            <button class="btn btn-primary me-2" onclick="downloadPdf()">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
            <button class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Print Preview
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
        
        <!-- Preview Notice -->
        <div class="alert alert-info mb-4">
            <h5><i class="fas fa-info-circle"></i> Certificate Preview</h5>
            <p class="mb-0">This is a preview of your certificate. Click the button above to download the official PDF version with the latest template design and QR code.</p>
        </div>
        
        <!-- PDF Preview -->
        <div class="pdf-preview-section mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-pdf"></i> Certificate Preview</h5>
                    <p class="mb-0 text-muted">View the official certificate</p>
                </div>
                <div class="card-body p-0">
                    <div class="pdf-preview-container" style="height: 600px; border: 1px solid #ddd;">
                        <object id="interactivePdfViewer" 
                                data="{{ route('certificates.preview', $exStudent->student_id) }}#toolbar=0&navpanes=0&scrollbar=1" 
                                type="application/pdf"
                                width="100%" 
                                height="100%"
                                style="border: none;">
                            <iframe id="pdfIframe" 
                                    src="{{ route('certificates.preview', $exStudent->student_id) }}#toolbar=0&navpanes=0&scrollbar=1" 
                                    width="100%" 
                                    height="100%" 
                                    frameborder="0"
                                    style="border: none;">
                            </iframe>
                        </object>
                        <div id="pdfFallback" style="display: none; padding: 20px; text-align: center;">
                            <p>PDF preview not available. <a href="{{ route('certificates.preview', $exStudent->student_id) }}" target="_blank" class="btn btn-primary">View Certificate in New Tab</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple PDF loading - let iframe handle it directly
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Certificate page loaded');
            
            // Set a simple timeout to show fallback if needed
            setTimeout(() => {
                const pdfViewer = document.getElementById('interactivePdfViewer');
                const pdfIframe = document.getElementById('pdfIframe');
                const pdfFallback = document.getElementById('pdfFallback');
                
                // Check if iframe has loaded content
                if (pdfIframe && pdfIframe.contentDocument && pdfIframe.contentDocument.body && pdfIframe.contentDocument.body.innerHTML.trim() === '') {
                    console.log('PDF not loaded, showing fallback');
                    if (pdfViewer) pdfViewer.style.display = 'none';
                    if (pdfIframe) pdfIframe.style.display = 'none';
                    pdfFallback.style.display = 'block';
                }
            }, 10000);
        });
        
        function downloadPdf() {
            // Show loading state
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            btn.disabled = true;
            
            // Download PDF certificate
            window.open('{{ route("certificate.generate.pdf", $exStudent->id) }}', '_blank');
            
            // Reset button after a delay
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 5000);
        }
    </script>
</body>
</html>
