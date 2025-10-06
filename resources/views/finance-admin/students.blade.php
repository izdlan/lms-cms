@extends('layouts.finance-admin')

@section('title', 'Students Management | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
                    <div class="page-header">
                        <h1 class="page-title">Students Management</h1>
                        <p class="page-subtitle">View and manage all student accounts</p>
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

                    <!-- Filters and Search -->
                    <div class="card mb-2">
                        <div class="card-body">
                            <form method="GET" action="{{ route('finance-admin.students') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Name, Email, IC, or Student ID">
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Students</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>Search
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <a href="{{ route('finance-admin.students') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-users me-2"></i>Students List</h5>
                            <span class="badge bg-primary fs-6">{{ $students->total() }} Total Students</span>
                        </div>
                        <div class="card-body">
                            @if($students->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th><i class="fas fa-user me-1"></i> Name</th>
                                                <th><i class="fas fa-id-card me-1"></i> IC Number</th>
                                                <th><i class="fas fa-envelope me-1"></i> Email</th>
                                                <th><i class="fas fa-graduation-cap me-1"></i> Student ID</th>
                                                <th><i class="fas fa-book me-1"></i> Program</th>
                                                <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                                <th><i class="fas fa-cogs me-1"></i> Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($student->profile_picture)
                                                                <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                                                                     alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                                            @else
                                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                                     style="width: 32px; height: 32px;">
                                                                    <i class="fas fa-user text-white"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div class="fw-bold">{{ $student->name }}</div>
                                                                @if($student->is_blocked)
                                                                    <small class="text-danger">
                                                                        <i class="fas fa-ban me-1"></i>
                                                                        Blocked on {{ $student->blocked_at ? $student->blocked_at->format('M d, Y') : 'Unknown date' }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $student->ic }}</td>
                                                    <td>{{ $student->email }}</td>
                                                    <td>{{ $student->student_id ?? '-' }}</td>
                                                    <td>{{ $student->programme_name ?? '-' }}</td>
                                                    <td>
                                                        @if($student->is_blocked)
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-ban me-1"></i>Blocked
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>Active
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('finance-admin.student.show', $student->id) }}" 
                                                               class="btn btn-sm btn-primary" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('finance-admin.payment-history', $student->id) }}" 
                                                               class="btn btn-sm btn-info" title="Payment History">
                                                                <i class="fas fa-receipt"></i>
                                                            </a>
                                                            @if($student->is_blocked)
                                                                <button type="button" class="btn btn-sm btn-success" 
                                                                        onclick="unblockStudent({{ $student->id }})" title="Unblock Student">
                                                                    <i class="fas fa-unlock"></i>
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-warning" 
                                                                        onclick="blockStudent({{ $student->id }})" title="Block Student">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $students->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>No Students Found</h5>
                                    <p class="text-muted">
                                        @if(request('search') || request('status'))
                                            No students match your search criteria.
                                        @else
                                            No students are registered in the system.
                                        @endif
                                    </p>
                                </div>
                            @endif
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
                    <p>Are you sure you want to block this student?</p>
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
                    <p>Are you sure you want to unblock this student?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Unblock Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
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

/* Fix excessive spacing issues */
.main-content {
    padding: 1rem !important; /* Reduced from 2rem */
}

.page-header {
    margin-bottom: 1rem !important; /* Reduced from 2rem */
}

.page-title {
    margin-bottom: 0.25rem !important; /* Reduced spacing */
}

.page-subtitle {
    margin-bottom: 0 !important; /* Remove bottom margin */
}

/* Reduce card spacing */
.card {
    margin-bottom: 1rem !important; /* Reduced from default */
}

.card-header {
    padding: 1rem !important; /* Reduced from 1.5rem */
}

.card-body {
    padding: 1rem !important; /* Ensure consistent padding */
}

/* Reduce form spacing */
.row.g-3 {
    --bs-gutter-y: 0.75rem; /* Reduced from 1rem */
    --bs-gutter-x: 0.75rem; /* Reduced from 1rem */
}

/* Reduce table spacing */
.table th,
.table td {
    padding: 0.5rem 0.75rem !important; /* Reduced from default */
}

/* Reduce button group spacing */
.btn-group .btn {
    margin-right: 1px !important; /* Reduced from 2px */
}

/* Reduce pagination spacing */
.d-flex.justify-content-center.mt-4 {
    margin-top: 1rem !important; /* Reduced from 1.5rem */
}

/* Fix large pagination arrows */
.pagination .page-link {
    min-width: 32px !important;
    height: 32px !important;
    padding: 0.25rem 0.5rem !important;
    font-size: 0.875rem !important;
}

.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    min-width: 32px !important;
    height: 32px !important;
    padding: 0.25rem !important;
    font-size: 1rem !important;
}

/* Fix table column widths to prevent truncation */
.table th:nth-child(1),
.table td:nth-child(1) {
    width: 25% !important; /* Name column */
    min-width: 200px !important;
}

.table th:nth-child(2),
.table td:nth-child(2) {
    width: 15% !important; /* IC Number column */
    min-width: 120px !important;
}

.table th:nth-child(3),
.table td:nth-child(3) {
    width: 25% !important; /* Email column */
    min-width: 200px !important;
    word-break: break-all !important;
}

.table th:nth-child(4),
.table td:nth-child(4) {
    width: 12% !important; /* Student ID column */
    min-width: 100px !important;
}

.table th:nth-child(5),
.table td:nth-child(5) {
    width: 15% !important; /* Program column */
    min-width: 120px !important;
}

.table th:nth-child(6),
.table td:nth-child(6) {
    width: 8% !important; /* Status column */
    min-width: 80px !important;
}

.table th:nth-child(7),
.table td:nth-child(7) {
    width: 10% !important; /* Actions column */
    min-width: 100px !important;
}

/* Enhanced table styling */
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,0.02);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #343a40;
    border-color: #495057;
}

.table td {
    vertical-align: middle;
    border-color: #e9ecef;
}

/* Enhanced button styling */
.btn-group .btn {
    margin-right: 2px;
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Profile picture styling */
.profile-picture, .bg-secondary {
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.profile-picture:hover, .bg-secondary:hover {
    border-color: #007bff;
}

/* Status badge enhancements */
.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
    border-radius: 0.5rem;
}

/* Card enhancements */
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
}

.card-header h5 {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

/* Search form enhancements */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .table-responsive {
        border-radius: 0.5rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 2px;
        margin-right: 0;
    }
}
</style>
@endpush
