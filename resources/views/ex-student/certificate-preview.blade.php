@extends('layouts.app')

@section('title', 'Certificate Preview | Ex-Student | Olympia Education')

@section('content')
<div class="certificate-preview-page">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="certificate-preview-card">
                    <div class="certificate-header">
                        <h2><i class="fas fa-certificate"></i> Certificate Preview</h2>
                        <p class="text-muted">Preview your official certificate before downloading</p>
                    </div>

                    <div class="certificate-actions mb-4">
                        <button class="btn btn-success" onclick="generatePdfCertificate()">
                            <i class="fas fa-file-pdf"></i> Download PDF Certificate
                        </button>
                        <button class="btn btn-info" onclick="printCertificate()">
                            <i class="fas fa-print"></i> Print Certificate
                        </button>
                    </div>

                    <!-- Certificate Preview -->
                    <div class="certificate-preview" id="certificatePreview">
                        <div class="certificate-preview-container">
                            <div class="preview-loading text-center py-5" id="previewLoading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-3">Generating certificate preview...</p>
                            </div>
                            
                            <div class="preview-content" id="previewContent" style="display: none;">
                                <div class="preview-actions mb-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="refreshPreview()">
                                        <i class="fas fa-sync-alt"></i> Refresh Preview
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreen()">
                                        <i class="fas fa-expand"></i> Fullscreen
                                    </button>
                                </div>
                                
                <div class="pdf-viewer-container">
                    <h3 class="mb-3">Certificate Preview</h3>
                    
                    <iframe
                        src="{{ route('certificates.preview', $exStudent->student_id) }}"
                        width="100%"
                        height="600"
                        style="border: 1px solid #ccc; border-radius: 10px;"
                    ></iframe>
                    
                    <!-- Optional: Download button -->
                    <div class="mt-3">
                        <a href="{{ route('certificates.preview', $exStudent->student_id) }}" download class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Certificate
                        </a>
                    </div>
                </div>
                            </div>
                            
                            <div class="preview-error text-center py-5" id="previewError" style="display: none;">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h5>Preview Generation Failed</h5>
                                    <p id="errorMessage">Unable to generate certificate preview. Please try again.</p>
                                    <button class="btn btn-primary" onclick="refreshPreview()">
                                        <i class="fas fa-retry"></i> Try Again
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Generating certificate...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.certificate-preview-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 2rem 0;
}

.certificate-preview-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.certificate-header h2 {
    color: #0056d2;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.certificate-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.certificate-preview {
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: 2rem;
    margin-top: 2rem;
}

.certificate-document {
    max-width: 800px;
    margin: 0 auto;
    font-family: 'Times New Roman', serif;
}

.university-name {
    font-size: 2rem;
    font-weight: bold;
    color: #000;
    margin-bottom: 0.5rem;
}

.education-name {
    font-size: 1.5rem;
    font-weight: bold;
    color: #000;
    margin-bottom: 2rem;
}

.declaration-malay {
    font-size: 1.1rem;
    font-weight: bold;
    color: #000;
    margin-bottom: 1rem;
}

.declaration-english {
    font-size: 1.1rem;
    color: #000;
    margin-bottom: 0;
}

.student-name {
    font-size: 1.8rem;
    font-weight: bold;
    color: #000;
    margin-bottom: 0.5rem;
}

.student-id {
    font-size: 1.2rem;
    color: #000;
    margin-bottom: 0;
}

.degree-malay {
    font-size: 1.4rem;
    font-weight: bold;
    color: #000;
    margin-bottom: 0.5rem;
}

.degree-english {
    font-size: 1.2rem;
    color: #000;
    margin-bottom: 0;
}

.graduation-date {
    font-size: 1.4rem;
    font-weight: bold;
    color: #000;
    margin-bottom: 0;
}

.signature-name {
    font-size: 1rem;
    font-weight: bold;
    color: #000;
    margin-bottom: 0.25rem;
}

.signature-title {
    font-size: 0.9rem;
    color: #000;
    margin-bottom: 0;
}

.qr-img {
    width: 80px;
    height: 80px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.certificate-number {
    font-size: 0.9rem;
    font-weight: bold;
    color: #000;
    margin-top: 0.5rem;
    margin-bottom: 0;
}

.accreditation-logo {
    width: 60px;
    height: 60px;
    margin: 0 10px;
    object-fit: contain;
}

@media print {
    .certificate-actions,
    .certificate-header {
        display: none !important;
    }
    
    .certificate-preview {
        border: none !important;
        padding: 0 !important;
    }
    
    .certificate-preview-page {
        background: white !important;
        padding: 0 !important;
    }
}

@media (max-width: 768px) {
    .certificate-actions {
        flex-direction: column;
    }
    
    .certificate-actions .btn {
        width: 100%;
    }
    
    .university-name {
        font-size: 1.5rem;
    }
    
    .education-name {
        font-size: 1.2rem;
    }
    
    .student-name {
        font-size: 1.4rem;
    }
    
    .degree-malay {
        font-size: 1.2rem;
    }
}

/* PDF Viewer Styles */
.certificate-preview-container {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.preview-loading {
    padding: 3rem 0;
}

.preview-content {
    position: relative;
}

.preview-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    margin-bottom: 1rem;
}

.pdf-viewer-container {
    position: relative;
    width: 100%;
    min-height: 800px;
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

#pdfViewer {
    width: 100%;
    height: 800px;
    border: none;
    border-radius: 8px;
    background: white;
}

.preview-error {
    padding: 3rem 0;
}

.preview-error .alert {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.preview-error .alert h5 {
    margin-bottom: 1rem;
    color: #721c24;
}

.preview-error .alert i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #dc3545;
}

@media (max-width: 768px) {
    .pdf-viewer-container {
        min-height: 600px;
    }
    
    #pdfViewer {
        height: 600px;
    }
    
    .preview-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .preview-actions .btn {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .pdf-viewer-container {
        min-height: 500px;
    }
    
    #pdfViewer {
        height: 500px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Load PDF preview when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Show content immediately since iframe loads directly
    const loadingDiv = document.getElementById('previewLoading');
    const contentDiv = document.getElementById('previewContent');
    
    loadingDiv.style.display = 'none';
    contentDiv.style.display = 'block';
});

function refreshPreview() {
    // Reload the iframe
    const pdfViewer = document.getElementById('pdfViewer');
    pdfViewer.src = pdfViewer.src;
}

function toggleFullscreen() {
    const pdfViewer = document.getElementById('pdfViewer');
    if (pdfViewer.requestFullscreen) {
        pdfViewer.requestFullscreen();
    } else if (pdfViewer.webkitRequestFullscreen) {
        pdfViewer.webkitRequestFullscreen();
    } else if (pdfViewer.msRequestFullscreen) {
        pdfViewer.msRequestFullscreen();
    }
}

function generatePdfCertificate() {
    showLoading();
    
    // Use the GET route with studentId in URL
    const url = '{{ route("certificate.generate.pdf", ["studentId" => $exStudent->id]) }}';
    
    fetch(url, {
        method: 'GET'
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Certificate generation failed');
    })
    .then(blob => {
        const downloadUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = 'Certificate_{{ $exStudent->name }}.pdf';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(downloadUrl);
        document.body.removeChild(a);
        hideLoading();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to generate certificate: ' + error.message);
        hideLoading();
    });
}

function printCertificate() {
    // Print the PDF preview
    const pdfViewer = document.getElementById('pdfViewer');
    if (pdfViewer.contentWindow) {
        pdfViewer.contentWindow.print();
    } else {
        window.print();
    }
}

function showLoading() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoading() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) {
        modal.hide();
    }
}
</script>
@endpush
