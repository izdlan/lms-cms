@extends('layouts.admin')

@section('title', 'Edit Ex-Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Ex-Student</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.ex-students') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left" width="16" height="16"></i>
                        Back to Ex-Students
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Ex-Student Information</h5>
                </div>
                <div class="card-body">
                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.ex-students.update', $exStudent) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" 
                                           value="{{ old('student_id', $exStudent->student_id) }}" required 
                                           placeholder="e.g., 670219-08-6113">
                                    <div class="form-text">Format: YYMMDD-GG-XXXX</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', $exStudent->name) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email', $exStudent->email) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="{{ old('phone', $exStudent->phone) }}" placeholder="+60123456789">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program" class="form-label">Program</label>
                                    <input type="text" class="form-control" id="program" name="program" 
                                           value="{{ old('program', $exStudent->program) }}" placeholder="e.g., Bachelor of Computer Science">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cgpa" class="form-label">CGPA</label>
                                    <input type="number" class="form-control" id="cgpa" name="cgpa" 
                                           value="{{ old('cgpa', $exStudent->cgpa) }}" step="0.01" min="0" max="4" 
                                           placeholder="3.75">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="graduation_year" class="form-label">Graduation Year <span class="text-danger">*</span></label>
                                    <select class="form-select" id="graduation_year" name="graduation_year" required>
                                        <option value="">Select Year</option>
                                        @for($year = date('Y'); $year >= 2010; $year--)
                                            <option value="{{ $year }}" {{ old('graduation_year', $exStudent->graduation_year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="graduation_month" class="form-label">Graduation Month</label>
                                    <select class="form-select" id="graduation_month" name="graduation_month">
                                        <option value="">Select Month</option>
                                        @for($month = 1; $month <= 12; $month++)
                                            <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" 
                                                    {{ old('graduation_month', $exStudent->graduation_month) == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Information -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6>QR Code Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Certificate Number</label>
                                            <input type="text" class="form-control" value="{{ $exStudent->certificate_number }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Verification URL</label>
                                            <input type="text" class="form-control" value="{{ $exStudent->getVerificationUrl() }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary" onclick="generateQR({{ $exStudent->id }})">
                                                <i data-feather="qrcode" width="16" height="16"></i>
                                                Generate QR Code
                                            </button>
                                            <a href="{{ route('admin.ex-students.download-qr', $exStudent) }}" class="btn btn-outline-success">
                                                <i data-feather="download" width="16" height="16"></i>
                                                Download QR Code
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Records Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6>Academic Records</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="academic_records_json" class="form-label">Academic Records (JSON)</label>
                                    <textarea class="form-control" id="academic_records_json" name="academic_records_json" 
                                              rows="6">{{ old('academic_records_json', json_encode($exStudent->academic_records, JSON_PRETTY_PRINT)) }}</textarea>
                                    <div class="form-text">Enter academic records in JSON format.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Certificate Data Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6>Certificate Data</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="certificate_data_json" class="form-label">Certificate Data (JSON)</label>
                                    <textarea class="form-control" id="certificate_data_json" name="certificate_data_json" 
                                              rows="4">{{ old('certificate_data_json', json_encode($exStudent->certificate_data, JSON_PRETTY_PRINT)) }}</textarea>
                                    <div class="form-text">Enter certificate data in JSON format.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save" width="16" height="16"></i>
                                    Update Ex-Student
                                </button>
                                <a href="{{ route('admin.ex-students') }}" class="btn btn-secondary">
                                    <i data-feather="x" width="16" height="16"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code for Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Generating QR Code...</span>
                    </div>
                </div>
                <div id="qrInfo" class="mt-3" style="display: none;">
                    <h6>Student Information</h6>
                    <p><strong>Student ID:</strong> <span id="qrStudentId"></span></p>
                    <p><strong>Verification URL:</strong> <span id="qrVerificationUrl"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadQrBtn" style="display: none;">
                    <i data-feather="download" width="16" height="16"></i> Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentExStudentId = null;

function generateQR(exStudentId) {
    currentExStudentId = exStudentId;
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
    
    // Show loading
    document.getElementById('qrCodeContainer').innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Generating QR Code...</span>
        </div>
    `;
    document.getElementById('qrInfo').style.display = 'none';
    document.getElementById('downloadQrBtn').style.display = 'none';
    
    // Generate QR code
    fetch(`/admin/ex-students/${exStudentId}/generate-qr`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Display QR code at full resolution
            document.getElementById('qrCodeContainer').innerHTML = `
                <img src="${data.qr_code_url}" alt="QR Code" class="img-fluid" style="max-width: 500px; width: 100%; height: auto;">
                <p class="mt-2 small text-muted">Right-click to save as PNG for printing</p>
            `;
            
            // Show info
            document.getElementById('qrStudentId').textContent = data.student_id;
            document.getElementById('qrVerificationUrl').textContent = data.verification_url;
            document.getElementById('qrInfo').style.display = 'block';
            document.getElementById('downloadQrBtn').style.display = 'inline-block';
        } else {
            document.getElementById('qrCodeContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i data-feather="alert-circle" width="20" height="20"></i>
                    ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('qrCodeContainer').innerHTML = `
            <div class="alert alert-danger">
                <i data-feather="alert-circle" width="20" height="20"></i>
                Failed to generate QR code. Please try again.
            </div>
        `;
    });
}

// Download QR code
document.getElementById('downloadQrBtn').addEventListener('click', function() {
    if (currentExStudentId) {
        window.open(`/admin/ex-students/${currentExStudentId}/download-qr`, '_blank');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Auto-format student ID
    document.getElementById('student_id').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        
        if (value.length >= 6) {
            // Format as YYMMDD-GG-XXXX
            let formatted = value.substring(0, 6);
            if (value.length > 6) {
                formatted += '-' + value.substring(6, 8);
            }
            if (value.length > 8) {
                formatted += '-' + value.substring(8, 12);
            }
            e.target.value = formatted;
        }
    });
    
    // Parse JSON inputs
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Parse academic records JSON
        const academicRecordsText = document.getElementById('academic_records_json').value;
        if (academicRecordsText.trim()) {
            try {
                const academicRecords = JSON.parse(academicRecordsText);
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'academic_records';
                hiddenInput.value = JSON.stringify(academicRecords);
                form.appendChild(hiddenInput);
            } catch (error) {
                alert('Invalid JSON format in Academic Records field');
                e.preventDefault();
                return;
            }
        }
        
        // Parse certificate data JSON
        const certificateDataText = document.getElementById('certificate_data_json').value;
        if (certificateDataText.trim()) {
            try {
                const certificateData = JSON.parse(certificateDataText);
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'certificate_data';
                hiddenInput.value = JSON.stringify(certificateData);
                form.appendChild(hiddenInput);
            } catch (error) {
                alert('Invalid JSON format in Certificate Data field');
                e.preventDefault();
                return;
            }
        }
    });
});
</script>
@endpush
