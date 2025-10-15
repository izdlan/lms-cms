@extends('layouts.admin')

@section('title', 'Ex-Students Management')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Ex-Students Management</h1>
                            <p class="text-muted mb-0">Manage graduated students and their certificates</p>
                        </div>
                        <a href="{{ route('admin.ex-students.create') }}" class="btn-modern btn-modern-primary">
                            <i class="bi bi-plus"></i>
                            Add Ex-Student
                        </a>
                    </div>
                </div>

                <div class="card fade-in">
                    <div class="card-header">
                        <h5 class="mb-0">Ex-Students List</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert-modern alert-modern-success">
                                <i class="bi bi-check-circle"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($exStudents->count() > 0)
                            <div class="table-responsive">
                                <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Program</th>
                                        <th>Graduation</th>
                                        <th>CGPA</th>
                                        <th>Certificate #</th>
                                        <th>Status</th>
                                        <th>QR Code</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exStudents as $exStudent)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-mortarboard" width="16" height="16" class="text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $exStudent->student_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $exStudent->name }}</div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $exStudent->program ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $exStudent->graduation_date }}</span>
                                            </td>
                                            <td>
                                                <span class="badge-modern badge-modern-{{ $exStudent->cgpa >= 3.5 ? 'success' : ($exStudent->cgpa >= 3.0 ? 'warning' : 'secondary') }}">
                                                    {{ $exStudent->formatted_cgpa }}
                                                </span>
                                            </td>
                                            <td>
                                                <code class="text-muted">{{ $exStudent->certificate_number }}</code>
                                            </td>
                                            <td>
                                                @if($exStudent->is_verified)
                                                    <span class="badge-modern badge-modern-success">Verified</span>
                                                @else
                                                    <span class="badge-modern badge-modern-warning">Not Verified</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" 
                                                            onclick="generateQR({{ $exStudent->id }})" 
                                                            title="Generate QR Code">
                                                        <i class="bi bi-qr-code"></i>
                                                    </button>
                                                    <a href="{{ route('admin.ex-students.download-qr', $exStudent) }}" 
                                                       class="btn-modern btn-modern-success btn-modern-sm" 
                                                       title="Download QR Code">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('certificate.generate', $exStudent->id) }}" 
                                                       class="btn-modern btn-modern-success btn-modern-sm" 
                                                       title="Generate Word Certificate" target="_blank">
                                                        <i class="bi bi-file-text"></i>
                                                    </a>
                                                    <a href="{{ url('/certificates/generate/pdf/' . $exStudent->id) }}" 
                                                       class="btn-modern btn-modern-info btn-modern-sm" 
                                                       title="Generate PDF Certificate" target="_blank">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </a>
                                                    <a href="{{ route('admin.ex-students.edit', $exStudent) }}" 
                                                       class="btn-modern btn-modern-secondary btn-modern-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn-modern btn-modern-danger btn-modern-sm" 
                                                            onclick="deleteExStudent({{ $exStudent->id }})" 
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($exStudents->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $exStudents->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="mb-4">
                                <div class="bg-gray-100 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-mortarboard" width="32" height="32" class="text-muted"></i>
                                </div>
                            </div>
                            <h5 class="text-muted mb-2">No ex-students found</h5>
                            <p class="text-muted mb-4">Start by adding an ex-student record to track graduated students.</p>
                            <a href="{{ route('admin.ex-students.create') }}" class="btn-modern btn-modern-primary">
                                <i class="bi bi-plus"></i>
                                Add First Ex-Student
                            </a>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
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
                    <i class="bi bi-download" width="16" height="16"></i> Download QR Code
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
        console.log('QR Code Response:', data); // Debug log
        if (data.success) {
            // Display QR code
            document.getElementById('qrCodeContainer').innerHTML = `
                <img src="${data.qr_code_url}" alt="QR Code" class="img-fluid" style="max-width: 300px;" 
                     onerror="console.error('QR Code image failed to load:', this.src); this.style.display='none';">
                <p class="mt-2 small text-muted">Right-click to save as SVG for printing</p>
                <p class="mt-1 small text-info">URL: ${data.qr_code_url}</p>
            `;
            
            // Show info
            document.getElementById('qrStudentId').textContent = data.student_id;
            document.getElementById('qrVerificationUrl').textContent = data.verification_url;
            document.getElementById('qrInfo').style.display = 'block';
            document.getElementById('downloadQrBtn').style.display = 'inline-block';
        } else {
            console.error('QR Code generation failed:', data.message);
            document.getElementById('qrCodeContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle" width="20" height="20"></i>
                    ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('QR Code fetch error:', error);
        document.getElementById('qrCodeContainer').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle" width="20" height="20"></i>
                Failed to generate QR code. Please try again.
                <br><small>Error: ${error.message}</small>
            </div>
        `;
    });
}

function deleteExStudent(exStudentId) {
    if (confirm('Are you sure you want to delete this ex-student? This action cannot be undone.')) {
        fetch(`/admin/ex-students/${exStudentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

// Download QR code
document.getElementById('downloadQrBtn').addEventListener('click', function() {
    if (currentExStudentId) {
        window.open(`/admin/ex-students/${currentExStudentId}/download-qr`, '_blank');
    }
});

// Bootstrap Icons are already loaded via CDN
</script>
@endpush
