@extends('layouts.admin')

@section('title', 'Lecturers Management')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>Lecturers Management</h1>
                    <div class="header-actions">
                        <a href="{{ route('admin.lecturers.create') }}" class="btn btn-primary">
                            <i data-feather="plus" width="16" height="16"></i>
                            Add Lecturer
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Lecturers Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>All Lecturers ({{ $lecturers->total() }})</h5>
                        <div class="d-flex gap-2">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Search lecturers..." id="searchInput">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="lecturersTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Profile</th>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Department</th>
                                        <th>Specialization</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lecturers as $lecturer)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input lecturer-checkbox" value="{{ $lecturer->id }}">
                                            </td>
                                            <td>
                                                @if($lecturer->profile_picture)
                                                    <img src="{{ asset($lecturer->profile_picture) }}" alt="{{ $lecturer->name }}" 
                                                         class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px; font-size: 16px;">
                                                        {{ substr($lecturer->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $lecturer->staff_id }}</td>
                                            <td>{{ $lecturer->name }}</td>
                                            <td>{{ $lecturer->email }}</td>
                                            <td>{{ $lecturer->phone ?? 'N/A' }}</td>
                                            <td>{{ $lecturer->department ?? 'N/A' }}</td>
                                            <td>{{ $lecturer->specialization ?? 'N/A' }}</td>
                                            <td>
                                                @if($lecturer->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.lecturers.edit', $lecturer) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i data-feather="edit" width="14" height="14"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteLecturer({{ $lecturer->id }})" title="Delete">
                                                        <i data-feather="trash-2" width="14" height="14"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i data-feather="users" width="48" height="48" class="mb-3"></i>
                                                    <p>No lecturers found. <a href="{{ route('admin.lecturers.create') }}">Add the first lecturer</a></p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($lecturers->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing {{ $lecturers->firstItem() }} to {{ $lecturers->lastItem() }} of {{ $lecturers->total() }} results
                                </div>
                                <div>
                                    {{ $lecturers->links() }}
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
                <p>Are you sure you want to delete this lecturer? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('lecturersTable');
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const text = row.textContent.toLowerCase();
            
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });

    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const lecturerCheckboxes = document.querySelectorAll('.lecturer-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        lecturerCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

let lecturerToDelete = null;

function deleteLecturer(lecturerId) {
    lecturerToDelete = lecturerId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (lecturerToDelete) {
        fetch(`/admin/lecturers/${lecturerToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
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
            alert('An error occurred while deleting the lecturer.');
        });
    }
});
</script>
@endpush
