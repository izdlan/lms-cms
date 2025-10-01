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
                            <button type="button" class="btn btn-outline-info" onclick="testEditFunction()">
                                <i data-feather="edit" width="16" height="16"></i>
                                Test Edit
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
                                                        <a href="{{ route('admin.students.edit', $student) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Edit Student"
                                                           onclick="console.log('Edit button clicked for student:', {{ $student->id }})">
                                                            <i data-feather="edit" width="14" height="14"></i>
                                                            Edit
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-student-btn" 
                                                                data-student-id="{{ $student->id }}" 
                                                                data-student-name="{{ $student->name }}" 
                                                                data-student-email="{{ $student->email }}"
                                                                title="Delete Student">
                                                            <i data-feather="trash-2" width="14" height="14"></i>
                                                            Delete
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
    // Safely replace feather icons
    if (typeof safeFeatherReplace === 'function') {
        safeFeatherReplace();
    } else {
        try {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.warn('Feather icons error:', error);
        }
    }
    
    // Debug: Check if edit buttons are present
    const editButtons = document.querySelectorAll('a[href*="edit"]');
    console.log('Edit buttons found:', editButtons.length);
    editButtons.forEach((btn, index) => {
        console.log(`Edit button ${index + 1}:`, btn.href, btn.textContent.trim());
    });
    
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
    
    // Initialize bulk delete button state
    updateBulkDeleteButton();
    
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
        
        if (checkedBoxes.length > 0) {
            bulkDeleteBtn.disabled = false;
            bulkDeleteBtn.textContent = `Delete Selected (${checkedBoxes.length})`;
            bulkDeleteBtn.style.backgroundColor = '#dc3545';
            bulkDeleteBtn.style.color = 'white';
        } else {
            bulkDeleteBtn.disabled = true;
            bulkDeleteBtn.textContent = 'Delete Selected';
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
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the student.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Use fetch API for single delete
                fetch(`/admin/students/${studentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'Student deleted successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Delete failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete student: ' + error.message,
                        icon: 'error'
                    });
                });
            }
        });
    }
});

function bulkDeleteStudents() {
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
    
    // Use fetch API for bulk delete
    fetch('/admin/students/bulk-delete', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            student_ids: studentIds
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message || 'Students deleted successfully.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Bulk delete failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete students: ' + error.message,
            icon: 'error'
        });
    });
}

// Google Sheets Import Function
function runGoogleSheetsImport() {
    const importBtn = document.getElementById('googleSheetsBtn');
    const originalText = importBtn.innerHTML;
    
    // Show loading state
    importBtn.disabled = true;
    importBtn.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Importing...';
    
    // Safely replace feather icons
    if (typeof safeFeatherReplace === 'function') {
        safeFeatherReplace();
    } else {
        try {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.warn('Feather icons error in Google Sheets import:', error);
        }
    }
    
    // Make the import request
    fetch('/admin/students/google-sheets-import', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
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
        
        // Safely replace feather icons
        if (typeof safeFeatherReplace === 'function') {
            safeFeatherReplace();
        } else {
            try {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            } catch (error) {
                console.warn('Feather icons error in Google Sheets import finally:', error);
            }
        }
    });
}

// OneDrive Import Function
function runOneDriveImport() {
    const importBtn = document.getElementById('oneDriveBtn');
    const originalText = importBtn.innerHTML;
    
    // Show loading state
    importBtn.disabled = true;
    importBtn.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Importing... (This may take up to 5 minutes)';
    
    // Safely replace feather icons
    setTimeout(() => {
        if (typeof safeFeatherReplace === 'function') {
            safeFeatherReplace();
        } else {
            try {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            } catch (error) {
                console.warn('Feather icons error in OneDrive import:', error);
            }
        }
    }, 200);
    
    // Show progress message
    showAlert('info', 'OneDrive import started. This may take up to 5 minutes. Please wait...');
    
    // Make the import request with extended timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => {
        console.log('Request timeout reached, but import may still be running in background...');
        showAlert('warning', 'Request timed out, but the import may still be running in the background. Please refresh the page in a few minutes to check for new students.');
        controller.abort();
    }, 60000); // 1 minute timeout for request, but import continues in background
    
    console.log('Starting OneDrive import request...');
    
    fetch('/admin/students/onedrive-import', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        signal: controller.signal
    })
    .then(response => {
        console.log('Response received:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Data received:', data);
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
        if (error.name === 'AbortError') {
            showAlert('danger', 'OneDrive import was aborted due to timeout. The import may still be processing in the background. Please refresh the page in a few minutes to check for new students.');
        } else {
            showAlert('danger', 'Error occurred during OneDrive import: ' + error.message);
        }
    })
    .finally(() => {
        // Clear timeout
        clearTimeout(timeoutId);
        
        // Reset button state
        importBtn.disabled = false;
        importBtn.innerHTML = originalText;
        
        // Safely replace feather icons
        setTimeout(() => {
            if (typeof safeFeatherReplace === 'function') {
                safeFeatherReplace();
            } else {
                try {
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                } catch (error) {
                    console.warn('Feather icons error in OneDrive import finally:', error);
                }
            }
        }, 100);
    });
}

// Test edit function
function testEditFunction() {
    const firstStudentRow = document.querySelector('tbody tr');
    if (firstStudentRow) {
        const editButton = firstStudentRow.querySelector('a[href*="edit"]');
        if (editButton) {
            console.log('Edit button found:', editButton.href);
            showAlert('success', 'Edit button is visible and working!');
            // Test the edit URL
            fetch(editButton.href)
                .then(response => {
                    if (response.ok) {
                        showAlert('success', 'Edit page is accessible!');
                    } else {
                        showAlert('danger', 'Edit page returned status: ' + response.status);
                    }
                })
                .catch(error => {
                    showAlert('danger', 'Error testing edit page: ' + error.message);
                });
        } else {
            showAlert('danger', 'No edit button found in the first row!');
        }
    } else {
        showAlert('danger', 'No student rows found!');
    }
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