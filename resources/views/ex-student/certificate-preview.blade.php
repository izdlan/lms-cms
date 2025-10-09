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
                        <button class="btn btn-primary" onclick="generateWordCertificate()">
                            <i class="fas fa-file-word"></i> Download Word Certificate
                        </button>
                        <button class="btn btn-success" onclick="generatePdfCertificate()">
                            <i class="fas fa-file-pdf"></i> Download PDF Certificate
                        </button>
                        <button class="btn btn-info" onclick="printCertificate()">
                            <i class="fas fa-print"></i> Print Certificate
                        </button>
                    </div>

                    <!-- Certificate Preview -->
                    <div class="certificate-preview" id="certificatePreview">
                        <div class="certificate-document">
                            <!-- University Header -->
                            <div class="certificate-header-section text-center mb-4">
                                <div class="university-logo mb-3">
                                    <img src="/store/1/logo/OLYMPIA.png" alt="Olympia University" class="logo-img" style="height: 80px;">
                                </div>
                                <h1 class="university-name">OLYMPIA UNIVERSITY</h1>
                                <h2 class="education-name">OLYMPIA EDUCATION</h2>
                            </div>

                            <!-- Certificate Body -->
                            <div class="certificate-body">
                                <!-- Declaration Text -->
                                <div class="declaration-text text-center mb-4">
                                    <p class="declaration-malay">DENGAN KUASA YANG DIBERIKAN OLEH LEMBAGA AKADEMIK, ADALAH DIPERAKUI BAHAWA</p>
                                    <p class="declaration-english">By the authority of Academic Board it is certify that</p>
                                </div>

                                <!-- Student Information -->
                                <div class="student-info text-center mb-4">
                                    <h3 class="student-name">{{ $exStudent->name }}</h3>
                                    <p class="student-id">{{ $exStudent->student_id }}</p>
                                </div>

                                <!-- Award Text -->
                                <div class="award-text text-center mb-4">
                                    <p>TELAH DIANUGERAHKAN / has been awarded the</p>
                                    <h4 class="degree-malay">{{ $exStudent->program ?? 'SARJANA MUDA EKSEKUTIF PENTADBIRAN PERNIAGAAN' }}</h4>
                                    <p class="degree-english">Bachelor Executive in Business Administration</p>
                                </div>

                                <!-- Completion Text -->
                                <div class="completion-text text-center mb-4">
                                    <p>SETELAH MEMENUHI SEMUA SYARAT YANG DITETAPKAN DAN DIKURNIAKAN IJAZAH PADA / having fulfilled all the requirements and has been conferred the degree at</p>
                                    <h4 class="graduation-date">{{ $exStudent->getFormattedGraduationDate() }}</h4>
                                </div>

                                <!-- Signatures and QR Codes -->
                                <div class="certificate-footer">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="signatures">
                                                <div class="signature-item mb-3">
                                                    <p class="signature-name">Assoc Prof Dr Mohd Kamil Yusoff</p>
                                                    <p class="signature-title">Director, Olympia Education</p>
                                                </div>
                                                <div class="signature-item">
                                                    <p class="signature-name">Brigadier General (R) Professor Dato Ts Dr. Hj. Shohaimi Abdullah</p>
                                                    <p class="signature-title">Chairman, Olympia Academic Board</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="qr-codes text-center">
                                                <div class="qr-code-item mb-3">
                                                    <img src="{{ asset('storage/' . $qrCodePath1) }}" alt="QR Code 1" class="qr-img">
                                                </div>
                                                <div class="qr-code-item">
                                                    <img src="{{ asset('storage/' . $qrCodePath2) }}" alt="QR Code 2" class="qr-img">
                                                </div>
                                                <p class="certificate-number">{{ $exStudent->certificate_number ?? 'CERT-' . time() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accreditation Logos -->
                                <div class="accreditation-logos text-center mt-4">
                                    <div class="row justify-content-center">
                                        <div class="col-auto">
                                            <img src="/store/1/logo/MQA.png" alt="MQA" class="accreditation-logo">
                                        </div>
                                        <div class="col-auto">
                                            <img src="/store/1/logo/CMI.png" alt="CMI" class="accreditation-logo">
                                        </div>
                                        <div class="col-auto">
                                            <img src="/store/1/logo/CTH.png" alt="CTH" class="accreditation-logo">
                                        </div>
                                        <div class="col-auto">
                                            <img src="/store/1/logo/Ministry.png" alt="Ministry" class="accreditation-logo">
                                        </div>
                                    </div>
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
</style>
@endpush

@push('scripts')
<script>
function generateWordCertificate() {
    showLoading();
    
    fetch('{{ route("certificate.generate.word") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            student_id: '{{ $exStudent->student_id }}'
        })
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Certificate generation failed');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Certificate_{{ $exStudent->name }}.docx';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        hideLoading();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to generate certificate: ' + error.message);
        hideLoading();
    });
}

function generatePdfCertificate() {
    showLoading();
    
    fetch('{{ route("certificate.generate.pdf") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            student_id: '{{ $exStudent->student_id }}'
        })
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Certificate generation failed');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Certificate_{{ $exStudent->name }}.pdf';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
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
    window.print();
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
