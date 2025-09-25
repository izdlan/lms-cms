@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>Dashboard</h1>
                    <p>Welcome back, {{ auth()->user()->name }}!</p>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i data-feather="users" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ $totalStudents }}</h3>
                                <p>Total Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i data-feather="book-open" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>0</h3>
                                <p>Total Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i data-feather="activity" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>0</h3>
                                <p>Active Sessions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Students -->
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Students</h5>
                        <a href="{{ route('admin.students') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>IC</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Courses</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->ic }}</td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->phone ?? 'N/A' }}</td>
                                                <td>
                                                    @if($student->courses)
                                                        @foreach($student->courses as $course)
                                                            <span class="badge bg-primary me-1">{{ $course }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">No courses</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Enhanced Pagination for Dashboard -->
                            @if($students->hasPages())
                                <div class="pagination-container">
                                    <div class="pagination-info">
                                        <div class="pagination-text">
                                            Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} recent students
                                        </div>
                                        <div class="pagination-controls">
                                            <span class="text-muted">Page {{ $students->currentPage() }} of {{ $students->lastPage() }}</span>
                                        </div>
                                    </div>
                                    {{ $students->links('pagination.admin-pagination') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No students found. <a href="{{ route('admin.import') }}">Import students</a> to get started.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Inline styles moved to /assets/default/css/admin.css --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
