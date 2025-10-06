@extends('layouts.finance-admin')

@section('title', 'Students Management | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                @include('finance-admin.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
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
                    <div class="card mb-4">
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
                        <div class="card-header">
                            <h5><i class="fas fa-users me-2"></i>Students List ({{ $students->total() }} total)</h5>
                        </div>
                        <div class="card-body">
                            @if($students->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>IC Number</th>
                                                <th>Email</th>
                                                <th>Student ID</th>
                                                <th>Program</th>
                                                <th>Status</th>
                                                <th>Actions</th>
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
                                                                        Blocked on {{ $student->blocked_at->format('M d, Y') }}
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

.table th {
    background: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush
