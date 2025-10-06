@extends('layouts.admin')

@section('title', 'Ex-Students Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Ex-Students Management</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.ex-students.create') }}" class="btn btn-primary">
                        <i data-feather="plus" width="16" height="16"></i>
                        Add Ex-Student
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Ex-Students List</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($exStudents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                                <strong>{{ $exStudent->student_id }}</strong>
                                            </td>
                                            <td>{{ $exStudent->name }}</td>
                                            <td>{{ $exStudent->program ?? 'N/A' }}</td>
                                            <td>{{ $exStudent->graduation_date }}</td>
                                            <td>
                                                <span class="badge bg-{{ $exStudent->cgpa >= 3.5 ? 'success' : ($exStudent->cgpa >= 3.0 ? 'warning' : 'secondary') }}">
                                                    {{ $exStudent->formatted_cgpa }}
                                                </span>
                                            </td>
                                            <td>
                                                <code>{{ $exStudent->certificate_number }}</code>
                                            </td>
                                            <td>
                                                @if($exStudent->is_verified)
                                                    <span class="badge bg-success">Verified</span>
                                                @else
                                                    <span class="badge bg-warning">Not Verified</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="generateQR({{ $exStudent->id }})" 
                                                            title="Generate QR Code">
                                                        <i data-feather="qrcode" width="14" height="14"></i>
                                                    </button>
                                                    <a href="{{ route('admin.ex-students.download-qr', $exStudent) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Download QR Code">
                                                        <i data-feather="download" width="14" height="14"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.ex-students.edit', $exStudent) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i data-feather="edit" width="14" height="14"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteExStudent({{ $exStudent->id }})" 
                                                            title="Delete">
                                                        <i data-feather="trash-2" width="14" height="14"></i>
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
                            <div class="d-flex justify-content-center mt-3">
                                {{ $exStudents->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i data-feather="graduation-cap" width="48" height="48" class="text-muted mb-3"></i>
                            <h5 class="text-muted">No ex-students found</h5>
                            <p class="text-muted">Start by adding an ex-student record.</p>
                            <a href="{{ route('admin.ex-students.create') }}" class="btn btn-primary">
                                Add First Ex-Student
                            </a>
                        </div>
                    @endif
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
            // Display QR code
            document.getElementById('qrCodeContainer').innerHTML = `
                <img src="${data.qr_code_url}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
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

// Initialize feather icons
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
