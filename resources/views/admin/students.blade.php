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
                        Sync from Excel/CSV
                    </a>
                    <a href="{{ route('admin.automation') }}" class="nav-link">
                        <i data-feather="settings" width="20" height="20"></i>
                        Automation
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
                        <div class="d-flex gap-2">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Search students..." id="searchInput">
                            </div>
                            <button type="button" class="btn btn-outline-danger" id="bulkDeleteBtn" style="cursor: pointer;">
                                <i data-feather="trash-2" width="16" height="16"></i>
                                Delete Selected
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" id="studentsTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Name</th>
                                            <th>IC/Passport</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Student ID</th>
                                            <th>Source Sheet</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input student-checkbox" value="{{ $student->id }}">
                                                </td>
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
                                                    @if($student->source_sheet)
                                                        <span class="badge bg-info">{{ $student->source_sheet }}</span>
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
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-student-btn" 
                                                                data-student-id="{{ $student->id }}" 
                                                                data-student-name="{{ $student->name }}" 
                                                                data-student-email="{{ $student->email }}">
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

<!-- Hidden form for delete action -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

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

.student-checkbox {
    transform: scale(1.2);
}

#bulkDeleteBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.list-group-item {
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
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

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
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
    
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }
    
    // Individual checkbox functionality
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkDeleteButton();
            updateSelectAllCheckbox();
        });
    });
    
    // Add event listener for bulk delete button
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Bulk delete button clicked');
            bulkDeleteStudents();
        });
    }
    
    function updateBulkDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        console.log('Updating bulk delete button. Checked boxes:', checkedBoxes.length);
        // Always enable the button for testing
        bulkDeleteBtn.disabled = false;
        bulkDeleteBtn.textContent = `Delete Selected (${checkedBoxes.length})`;
        
        // Add visual feedback
        if (checkedBoxes.length > 0) {
            bulkDeleteBtn.style.backgroundColor = '#dc3545';
            bulkDeleteBtn.style.color = 'white';
        } else {
            bulkDeleteBtn.style.backgroundColor = '';
            bulkDeleteBtn.style.color = '';
        }
    }
    
    function updateSelectAllCheckbox() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        const totalBoxes = document.querySelectorAll('.student-checkbox');
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === totalBoxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
});

// Event delegation for delete buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-student-btn')) {
        const button = e.target.closest('.delete-student-btn');
        const studentId = button.getAttribute('data-student-id');
        const studentName = button.getAttribute('data-student-name');
        const studentEmail = button.getAttribute('data-student-email');
        
        console.log('Delete function called with:', studentId, studentName, studentEmail);
        
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <div class="text-start">
                    <p>You are about to delete the following student:</p>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">${studentName}</h6>
                            <p class="card-text">
                                <strong>Email:</strong> ${studentEmail}<br>
                                <strong>Student ID:</strong> ${studentId}
                            </p>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning!</strong> This action cannot be undone.
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit the delete form
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = `/admin/students/${studentId}`;
                
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the student.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the form
                deleteForm.submit();
            }
        });
    }
});

function bulkDeleteStudents() {
    alert('Bulk delete function called!');
    console.log('bulkDeleteStudents function called');
    const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
    const studentIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    console.log('Checked boxes:', checkedBoxes.length);
    console.log('Student IDs:', studentIds);
    
    if (studentIds.length === 0) {
        Swal.fire({
            title: 'No Selection',
            text: 'Please select at least one student to delete.',
            icon: 'warning'
        });
        return;
    }
    
    // Build student list for display
    let studentListHtml = '<ul class="list-group">';
    checkedBoxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const name = row.querySelector('td:nth-child(2) strong').textContent;
        const email = row.querySelector('td:nth-child(4)').textContent;
        studentListHtml += `<li class="list-group-item">${name} - ${email}</li>`;
    });
    studentListHtml += '</ul>';
    
    Swal.fire({
        title: 'Delete Multiple Students',
        html: `
            <div class="text-start">
                <p>You are about to delete <strong>${studentIds.length}</strong> students:</p>
                <div class="mt-3" style="max-height: 200px; overflow-y: auto;">
                    ${studentListHtml}
                </div>
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: `Yes, delete ${studentIds.length} students!`,
        cancelButtonText: 'Cancel',
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: `Please wait while we delete ${studentIds.length} students.`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit bulk delete form
            confirmBulkDelete();
        }
    });
}

function confirmBulkDelete() {
    console.log('confirmBulkDelete function called');
    const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
    const studentIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    console.log('Student IDs to delete:', studentIds);
    
    // Create a form to submit multiple delete requests
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/students/bulk-delete';
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Add student IDs
    studentIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    console.log('Form created, submitting...');
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
