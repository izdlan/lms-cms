@extends('layouts.finance-admin')

@section('title', 'Student Details | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="page-title">Student Details</h1>
                                <p class="page-subtitle">{{ $student->name }}</p>
                            </div>
                            <div>
                                <a href="{{ route('finance-admin.students') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Students
                                </a>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Student Information -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Full Name</label>
                                            <p class="form-control-plaintext">{{ $student->name ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Email</label>
                                            <p class="form-control-plaintext">{{ $student->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">IC Number</label>
                                            <p class="form-control-plaintext">{{ $student->ic ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Phone Number</label>
                                            <p class="form-control-plaintext">{{ $student->phone ?? '-' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Address</label>
                                        <p class="form-control-plaintext">{{ $student->address ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-graduation-cap me-2"></i>Academic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Student ID</label>
                                            <p class="form-control-plaintext">{{ $student->student_id ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Programme Name</label>
                                            <p class="form-control-plaintext">{{ $student->programme_name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Category</label>
                                            <p class="form-control-plaintext">{{ $student->category ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Faculty</label>
                                            <p class="form-control-plaintext">{{ $student->faculty ?? '-' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Programme Code</label>
                                            <p class="form-control-plaintext">{{ $student->programme_code ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Semester Entry</label>
                                            <p class="form-control-plaintext">{{ $student->semester_entry ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enrollment History -->
                            @if($enrollments->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-book me-2"></i>Enrollment History</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Subject Code</th>
                                                    <th>Subject Name</th>
                                                    <th>Lecturer</th>
                                                    <th>Enrollment Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($enrollments as $enrollment)
                                                    <tr>
                                                        <td>{{ $enrollment->subject_code }}</td>
                                                        <td>{{ $enrollment->subject->name ?? '-' }}</td>
                                                        <td>{{ $enrollment->lecturer->name ?? '-' }}</td>
                                                        <td>{{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('M d, Y') : '-' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : 'secondary' }}">
                                                                {{ ucfirst($enrollment->status ?? 'Unknown') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Actions and Status -->
                        <div class="col-md-4">
                            <!-- Student Status -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle me-2"></i>Account Status</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($student->is_blocked)
                                        <div class="status-blocked">
                                            <i class="fas fa-ban fa-3x text-danger mb-3"></i>
                                            <h5 class="text-danger">Account Blocked</h5>
                                            <p class="text-muted">Blocked on {{ $student->blocked_at->format('M d, Y \a\t g:i A') }}</p>
                                            @if($student->block_reason)
                                                <div class="alert alert-warning">
                                                    <strong>Reason:</strong><br>
                                                    {{ $student->block_reason }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="status-active">
                                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                            <h5 class="text-success">Account Active</h5>
                                            <p class="text-muted">Student can access all services</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('finance-admin.create-invoice', $student->id) }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Create Invoice
                                        </a>
                                        
                                        <a href="{{ route('finance-admin.payment-history', $student->id) }}" class="btn btn-info">
                                            <i class="fas fa-receipt me-2"></i>View Payment History
                                        </a>
                                        
                                        @if($student->is_blocked)
                                            <button type="button" class="btn btn-success" onclick="unblockStudent({{ $student->id }})">
                                                <i class="fas fa-unlock me-2"></i>Unblock Student
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-warning" onclick="blockStudent({{ $student->id }})">
                                                <i class="fas fa-ban me-2"></i>Block Student
                                            </button>
                                        @endif
                                        
                                        <a href="mailto:{{ $student->email }}" class="btn btn-outline-primary">
                                            <i class="fas fa-envelope me-2"></i>Send Email
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Picture -->
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-image me-2"></i>Profile Picture</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($student->profile_picture)
                                        <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                                             alt="Profile Picture" class="img-fluid rounded-circle" style="max-width: 150px;">
                                    @else
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             style="width: 150px; height: 150px;">
                                            <i class="fas fa-user fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Block Student Modal -->
<div class="modal fade" id="blockStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blockStudentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to block <strong>{{ $student->name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="block_reason" class="form-label">Reason for blocking:</label>
                        <textarea class="form-control" id="block_reason" name="block_reason" rows="3" 
                                  placeholder="Enter the reason for blocking this student..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unblock Student Modal -->
<div class="modal fade" id="unblockStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unblock Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="unblockStudentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to unblock <strong>{{ $student->name }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Unblock Student</button>
                </div>
            </form>
@endsection

@push('scripts')
<script>
function blockStudent(studentId) {
    document.getElementById('blockStudentForm').action = `/finance-admin/students/${studentId}/block`;
    new bootstrap.Modal(document.getElementById('blockStudentModal')).show();
}

function unblockStudent(studentId) {
    document.getElementById('unblockStudentForm').action = `/finance-admin/students/${studentId}/unblock`;
    new bootstrap.Modal(document.getElementById('unblockStudentModal')).show();
}
</script>
@endpush

@push('styles')
<style>
.finance-admin-dashboard {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.card-header h5 {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    color: #333;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #666;
    margin: 0;
}

.form-label.fw-bold {
    color: #495057;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.form-control-plaintext {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0;
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

.status-blocked, .status-active {
    padding: 1rem;
}

.table th {
    background: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}
</style>
@endpush
