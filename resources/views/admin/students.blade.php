@extends('layouts.admin')

@section('title', 'Students Management')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

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
                        <button type="button" class="btn btn-warning" onclick="runGoogleSheetsImport()" id="googleSheetsBtn">
                            <i data-feather="download" width="16" height="16"></i>
                            Google Sheets Import
                        </button>
                        <button type="button" class="btn btn-info" onclick="runOneDriveImport()" id="oneDriveBtn">
                            <i data-feather="cloud" width="16" height="16"></i>
                            OneDrive Import
                        </button>
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

                            <!-- Enhanced Pagination -->
                            <div class="pagination-container">
                                <div class="pagination-info">
                                    <div class="pagination-text">
                                        Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students
                                    </div>
                                    <div class="pagination-controls">
                                        <span class="text-muted">Page {{ $students->currentPage() }} of {{ $students->lastPage() }}</span>
                                    </div>
                                </div>
                                {{ $students->links('pagination.admin-pagination') }}
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

{{-- Inline styles moved to /assets/default/css/admin.css --}}

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

// Google Sheets Import Function
function runGoogleSheetsImport() {
    const importBtn = document.getElementById('googleSheetsBtn');
    const originalText = importBtn.innerHTML;
    
    // Show loading state
    importBtn.disabled = true;
    importBtn.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Importing...';
    feather.replace();
    
    // Make the import request
    fetch('/admin/students/google-sheets-import', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', `Google Sheets import completed! Created: ${data.created}, Updated: ${data.updated}, Errors: ${data.errors}`);
            
            // Refresh the page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('danger', 'Google Sheets import failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Error occurred during Google Sheets import: ' + error.message);
    })
    .finally(() => {
        // Reset button state
        importBtn.disabled = false;
        importBtn.innerHTML = originalText;
        feather.replace();
    });
}

// OneDrive Import Function
function runOneDriveImport() {
    const importBtn = document.getElementById('oneDriveBtn');
    const originalText = importBtn.innerHTML;
    
    // Show loading state
    importBtn.disabled = true;
    importBtn.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Importing...';
    feather.replace();
    
    // Make the import request
    fetch('/admin/students/onedrive-import', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message with sheet details
            let message = `OneDrive import completed! Created: ${data.created}, Updated: ${data.updated}, Errors: ${data.errors}`;
            
            if (data.processed_sheets && data.processed_sheets.length > 0) {
                message += '\n\nProcessed sheets:';
                data.processed_sheets.forEach(sheet => {
                    message += `\n- ${sheet.sheet}: Created=${sheet.created}, Updated=${sheet.updated}, Errors=${sheet.errors}`;
                });
            }
            
            showAlert('success', message);
            
            // Refresh the page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showAlert('danger', 'OneDrive import failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Error occurred during OneDrive import: ' + error.message);
    })
    .finally(() => {
        // Reset button state
        importBtn.disabled = false;
        importBtn.innerHTML = originalText;
        feather.replace();
    });
}

// Show alert function
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the main content
    const mainContent = document.querySelector('.main-content');
    mainContent.insertBefore(alertDiv, mainContent.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush