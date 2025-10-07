@extends('layouts.admin')

@section('title', 'Lecturers Management')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Lecturers Management</h1>
                            <p class="text-muted mb-0">Manage lecturer accounts and information</p>
                        </div>
                        <a href="{{ route('admin.lecturers.create') }}" class="btn-modern btn-modern-primary">
                            <i data-feather="plus"></i>
                            Add Lecturer
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert-modern alert-modern-success">
                        <i data-feather="check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Lecturers Table -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">All Lecturers ({{ $lecturers->total() }})</h5>
                            <div class="search-box">
                                <input type="text" class="form-control-modern" placeholder="Search lecturers..." id="searchInput">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-modern" id="lecturersTable">
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
                                                <div class="d-flex align-items-center">
                                                    @if($lecturer->profile_picture)
                                                        <img src="{{ asset($lecturer->profile_picture) }}" alt="{{ $lecturer->name }}" 
                                                             class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 40px; height: 40px; font-size: 16px;">
                                                            {{ substr($lecturer->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $lecturer->staff_id }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $lecturer->name }}</div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $lecturer->email }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $lecturer->phone ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $lecturer->department ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $lecturer->specialization ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($lecturer->is_active)
                                                    <span class="badge-modern badge-modern-success">Active</span>
                                                @else
                                                    <span class="badge-modern badge-modern-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.lecturers.edit', $lecturer) }}" 
                                                       class="btn-modern btn-modern-secondary btn-modern-sm" title="Edit">
                                                        <i data-feather="edit-2"></i>
                                                    </a>
                                                    <button type="button" class="btn-modern btn-modern-danger btn-modern-sm" 
                                                            onclick="deleteLecturer({{ $lecturer->id }})" title="Delete">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-8">
                                                <div class="mb-4">
                                                    <div class="bg-gray-100 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                        <i data-feather="users" width="32" height="32" class="text-muted"></i>
                                                    </div>
                                                </div>
                                                <h5 class="text-muted mb-2">No lecturers found</h5>
                                                <p class="text-muted mb-4">Start by adding the first lecturer to your system.</p>
                                                <a href="{{ route('admin.lecturers.create') }}" class="btn-modern btn-modern-primary">
                                                    <i data-feather="plus"></i>
                                                    Add First Lecturer
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($lecturers->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $lecturers->links() }}
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
