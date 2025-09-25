@extends('layouts.staff')

@section('title', 'Students')

@section('content')
<div class="dashboard-header">
    <h1>Students Management</h1>
    <p class="text-muted">Manage and view student information</p>
</div>

        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('staff.students') }}" class="search-form">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search by name, email, or student ID..."
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select name="program" class="form-control">
                                        <option value="">All Programs</option>
                                        @foreach($students->pluck('programme_name')->unique()->filter() as $program)
                                            <option value="{{ $program }}" {{ request('program') == $program ? 'selected' : '' }}>
                                                {{ $program }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Students List ({{ $students->total() }} total)</h5>
                    </div>
                    <div class="card-body">
                        @if($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Student ID</th>
                                            <th>Program</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Registered</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                        <tr>
                                            <td>
                                                <div class="student-info">
                                                    <div class="student-avatar">
                                                        @if($student->profile_picture)
                                                            <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="{{ $student->name }}">
                                                        @else
                                                            <i class="fas fa-user"></i>
                                                        @endif
                                                    </div>
                                                    <div class="student-details">
                                                        <strong>{{ $student->name }}</strong>
                                                        <small class="text-muted d-block">{{ $student->ic }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                                            <td>{{ $student->programme_name ?? 'N/A' }}</td>
                                            <td>{{ $student->phone ?? 'N/A' }}</td>
                                            <td>
                                                <span class="status-badge {{ $student->must_reset_password ? 'inactive' : 'active' }}">
                                                    {{ $student->must_reset_password ? 'Inactive' : 'Active' }}
                                                </span>
                                            </td>
                                            <td>{{ $student->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="/maintenance" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="/maintenance" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
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
                                <h5 class="text-muted">No students found</h5>
                                <p class="text-muted">Try adjusting your search criteria.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.staff-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
}

.main-content {
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-title {
    color: #2d3748;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    margin: 0;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: #20c997;
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
    border: none;
}

.card-title {
    margin: 0;
    font-weight: 600;
    font-size: 1.2rem;
}

.card-body {
    padding: 25px;
}

.search-form .form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.search-form .form-control:focus {
    border-color: #20c997;
    box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25);
}

.btn-primary {
    background: #20c997;
    border-color: #20c997;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-weight: 600;
}

.btn-primary:hover {
    background: #1a9f7a;
    border-color: #1a9f7a;
}

.table th {
    border: none;
    color: #6c757d;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 15px;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

.student-info {
    display: flex;
    align-items: center;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    overflow: hidden;
}

.student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.student-avatar i {
    color: #6c757d;
    font-size: 1.2rem;
}

.student-details strong {
    color: #2d3748;
    font-size: 0.95rem;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.active {
    background: #d1edff;
    color: #0c5460;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.action-buttons .btn {
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 0.8rem;
}

.btn-outline-primary {
    color: #20c997;
    border-color: #20c997;
}

.btn-outline-primary:hover {
    background: #20c997;
    border-color: #20c997;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
}

@media (max-width: 768px) {
    .main-content {
        padding: 15px;
    }
    
    .search-form .row > div {
        margin-bottom: 15px;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .student-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .student-avatar {
        margin-right: 0;
        margin-bottom: 8px;
    }
}
</style>
@endpush
