@extends('layouts.app')

@section('title', 'Students Management')

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
                    <h1>Students Management</h1>
                    <div class="header-actions">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                            <i data-feather="plus" width="16" height="16"></i>
                            Add Student
                        </a>
                        <a href="{{ route('admin.import') }}" class="btn btn-outline-primary">
                            <i data-feather="upload" width="16" height="16"></i>
                            Import from Excel
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Students Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>All Students ({{ $students->total() }})</h5>
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Search students..." id="searchInput">
                        </div>
                    </div>
                    <div class="card-body">
                        @if($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" id="studentsTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>IC/Passport</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Student ID</th>
                                            <th>Previous University</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>
                                                    <div class="student-info">
                                                        <strong>{{ $student->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ $student->ic }}</td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->phone ?? 'N/A' }}</td>
                                                <td>{{ $student->student_id ?? 'N/A' }}</td>
                                                <td>
                                                    @if($student->previous_university)
                                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $student->previous_university }}">
                                                            {{ $student->previous_university }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $student->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary">
                                                            <i data-feather="edit" width="14" height="14"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteStudent({{ $student->id }})">
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
                            <div class="d-flex justify-content-center">
                                {{ $students->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i data-feather="users" width="48" height="48" class="text-muted mb-3"></i>
                                <h5 class="text-muted">No students found</h5>
                                <p class="text-muted">Get started by importing students from Excel or adding them manually.</p>
                                <div class="mt-3">
                                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary me-2">
                                        <i data-feather="plus" width="16" height="16"></i>
                                        Add Student
                                    </a>
                                    <a href="{{ route('admin.import') }}" class="btn btn-outline-primary">
                                        <i data-feather="upload" width="16" height="16"></i>
                                        Import from Excel
                                    </a>
                                </div>
                            </div>
                        @endif
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
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    color: #2d3748;
    font-weight: bold;
}

.search-box {
    width: 300px;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #2d3748;
}

.student-info strong {
    color: #2d3748;
}

.badge {
    font-size: 0.75rem;
}

.btn-group .btn {
    margin-right: 0.25rem;
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
    
    .search-box {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('studentsTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }
});

function deleteStudent(studentId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/students/${studentId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
