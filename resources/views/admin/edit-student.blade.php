@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4>Admin Panel</h4>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i data-feather="home" width="20" height="20"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.students') }}" class="nav-link active">
                        <i data-feather="users" width="20" height="20"></i>
                        Students
                    </a>
                    <a href="{{ route('admin.import') }}" class="nav-link">
                        <i data-feather="upload" width="20" height="20"></i>
                        Import Students
                    </a>
                    <a href="{{ route('admin.sync') }}" class="nav-link">
                        <i data-feather="refresh-cw" width="20" height="20"></i>
                        Sync from Excel
                    </a>
                    <a href="{{ route('admin.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-feather="log-out" width="20" height="20"></i>
                        Logout
                    </a>
                </nav>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>Edit Student</h1>
                    <div class="header-actions">
                        <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" width="16" height="16"></i>
                            Back to Students
                        </a>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Edit Student Form -->
                <div class="card">
                    <div class="card-header">
                        <h5>Student Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.students.update', $student) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $student->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ic" class="form-label">IC Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('ic') is-invalid @enderror" 
                                               id="ic" name="ic" value="{{ old('ic', $student->ic) }}" required>
                                        @error('ic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $student->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" 
                                          placeholder="Enter student's address">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="previous_university" class="form-label">Previous University</label>
                                        <input type="text" class="form-control @error('previous_university') is-invalid @enderror" 
                                               id="previous_university" name="previous_university" value="{{ old('previous_university', $student->previous_university) }}" 
                                               placeholder="Enter previous university">
                                        @error('previous_university')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">Student ID</label>
                                        <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                               id="student_id" name="student_id" value="{{ old('student_id', $student->student_id) }}" 
                                               placeholder="Enter student ID">
                                        @error('student_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="col_ref_no" class="form-label">College Reference Number</label>
                                <input type="text" class="form-control @error('col_ref_no') is-invalid @enderror" 
                                       id="col_ref_no" name="col_ref_no" value="{{ old('col_ref_no', $student->col_ref_no) }}" 
                                       placeholder="Enter college reference number">
                                @error('col_ref_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="courses" class="form-label">Courses</label>
                                <input type="text" class="form-control @error('courses') is-invalid @enderror" 
                                       id="courses" name="courses" value="{{ old('courses', $student->courses ? implode(', ', $student->courses) : '') }}" 
                                       placeholder="Enter courses separated by commas (e.g., Math, Science, English)">
                                <div class="form-text">Separate multiple courses with commas</div>
                                @error('courses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Created At</label>
                                        <input type="text" class="form-control" value="{{ $student->created_at->format('M d, Y H:i') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Updated</label>
                                        <input type="text" class="form-control" value="{{ $student->updated_at->format('M d, Y H:i') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i data-feather="info" width="16" height="16"></i>
                                <strong>Note:</strong> The student's password is set to their IC number. They can change it after logging in.
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-danger" onclick="deleteStudent({{ $student->id }})">
                                        <i data-feather="trash-2" width="16" height="16"></i>
                                        Delete Student
                                    </button>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.students') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="save" width="16" height="16"></i>
                                        Update Student
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this student? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.admin-dashboard {
    min-height: 100vh;
    background-color: #f8f9fa;
}

.sidebar {
    background: #2d3748;
    min-height: 100vh;
    padding: 0;
}

.sidebar-header {
    background: #1a202c;
    padding: 1.5rem;
    color: white;
    border-bottom: 1px solid #4a5568;
}

.sidebar-header h4 {
    margin: 0;
    font-weight: bold;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #a0aec0;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: #4a5568;
    color: white;
}

.nav-link.active {
    background: #667eea;
    color: white;
    border-left-color: #5a67d8;
}

.nav-link i {
    margin-right: 0.75rem;
}

.main-content {
    padding: 2rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    color: #2d3748;
    font-weight: bold;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.card-header h5 {
    margin: 0;
    color: #2d3748;
    font-weight: bold;
}

.card-body {
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-control[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
}

.alert-info {
    background-color: #e6f3ff;
    border-color: #b3d9ff;
    color: #0066cc;
}

.alert-info i {
    margin-right: 0.5rem;
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #667eea;
    border-color: #667eea;
}

.btn-primary:hover {
    background-color: #5a67d8;
    border-color: #5a67d8;
}

.btn-outline-secondary {
    color: #718096;
    border-color: #e2e8f0;
}

.btn-outline-secondary:hover {
    background-color: #f7fafc;
    border-color: #cbd5e0;
}

.btn-danger {
    background-color: #e53e3e;
    border-color: #e53e3e;
}

.btn-danger:hover {
    background-color: #c53030;
    border-color: #c53030;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});

function deleteStudent(studentId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/students/${studentId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
