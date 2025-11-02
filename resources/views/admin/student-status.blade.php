@extends('layouts.admin')

@section('title', 'Student Status Overview')

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
                            <h1>Student Status Overview</h1>
                            <p class="text-muted mb-0">View all students with their current status and contact information</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.students') }}" class="btn-modern btn-modern-secondary">
                                <i data-feather="arrow-left"></i>
                                Back to Students
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert-modern alert-modern-success">
                        <i data-feather="check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Students Status Table -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">All Students ({{ $students->total() }})</h5>
                            <div class="d-flex gap-2">
                                <div class="search-box">
                                    <input type="text" class="form-control-modern" placeholder="Search students..." id="searchInput">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table-modern" id="studentsTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Student ID</th>
                                            <th>Email</th>
                                            <th>Program</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            @if($student->profile_picture)
                                                                <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                                                                     alt="{{ $student->name }}" 
                                                                     class="rounded-circle"
                                                                     style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e0e0e0;">
                                                            @else
                                                                <div class="bg-primary-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                    <i data-feather="user" width="16" height="16" class="text-primary"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">{{ $student->name }}</div>
                                                            <small class="text-muted">{{ $student->ic }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($student->contact_no)
                                                        <span class="text-muted">{{ $student->contact_no }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $student->student_id ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-semibold">{{ $student->email }}</div>
                                                        @if($student->student_email)
                                                            <small class="text-muted">{{ $student->student_email }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $student->programme_name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    @if($student->status)
                                                        @switch($student->status)
                                                            @case('In progress')
                                                                <span class="badge-modern badge-modern-warning">In Progress</span>
                                                                @break
                                                            @case('Withdrawn')
                                                                <span class="badge-modern badge-modern-danger">Withdrawn</span>
                                                                @break
                                                            @case('Complete Viva')
                                                                <span class="badge-modern badge-modern-success">Complete Viva</span>
                                                                @break
                                                            @default
                                                                <span class="badge-modern badge-modern-secondary">{{ $student->status }}</span>
                                                        @endswitch
                                                    @else
                                                        <span class="badge-modern badge-modern-secondary">No Status</span>
                                                    @endif
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
                            <div class="text-center py-8">
                                <div class="mb-4">
                                    <div class="bg-gray-100 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i data-feather="users" width="32" height="32" class="text-muted"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted mb-2">No students found</h5>
                                <p class="text-muted mb-4">No students are currently enrolled in the system.</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('admin.students') }}" class="btn-modern btn-modern-primary">
                                        <i data-feather="users"></i>
                                        View All Students
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
@endsection

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
</script>
@endpush
