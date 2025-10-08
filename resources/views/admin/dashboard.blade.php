@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="dashboard-header">
                    <h1>Dashboard</h1>
                    <p>Welcome back, {{ auth()->user()->name }}!</p>
                </div>

                <!-- Modern Stats Cards -->
                <div class="row mb-8">
                    <div class="col-md-4 mb-4">
                        <div class="stats-card fade-in">
                            <div class="stats-icon">
                                <i data-feather="users"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ $totalStudents }}</h3>
                                <p>Total Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card fade-in">
                            <div class="stats-icon">
                                <i data-feather="book-open"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ $totalCourses ?? 0 }}</h3>
                                <p>Total Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card fade-in">
                            <div class="stats-icon">
                                <i data-feather="activity"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ $activeEnrollments ?? 0 }}</h3>
                                <p>Active Enrollments</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Students -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Students</h5>
                            <a href="{{ route('admin.students') }}" class="btn-modern btn-modern-secondary btn-modern-sm">
                                <i data-feather="arrow-right"></i>
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table-modern">
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
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="bg-primary-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                <i data-feather="user" width="16" height="16" class="text-primary"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">{{ $student->name }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $student->ic }}</span>
                                                </td>
                                                <td>
                                                    <a href="mailto:{{ $student->email }}" class="text-decoration-none">{{ $student->email }}</a>
                                                </td>
                                                <td>
                                                    @if($student->phone)
                                                        <a href="tel:{{ $student->phone }}" class="text-decoration-none">{{ $student->phone }}</a>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $enrollments = \App\Models\StudentEnrollment::where('user_id', $student->id)
                                                            ->where('status', 'enrolled')
                                                            ->with('subject')
                                                            ->get();
                                                    @endphp
                                                    @if($enrollments->count() > 0)
                                                        @foreach($enrollments as $enrollment)
                                                            <span class="badge-modern badge-modern-primary me-1">{{ $enrollment->subject->name ?? $enrollment->subject_code }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">No courses</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.students.edit', $student) }}" class="btn-modern btn-modern-secondary btn-modern-sm">
                                                        <i data-feather="edit-2"></i>
                                                        Edit
                                                    </a>
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
                            <div class="text-center py-8">
                                <div class="mb-4">
                                    <div class="bg-gray-100 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i data-feather="users" width="32" height="32" class="text-muted"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted mb-2">No students found</h5>
                                <p class="text-muted mb-4">Get started by importing students to your system.</p>
                                <a href="{{ route('admin.import') }}" class="btn-modern btn-modern-primary">
                                    <i data-feather="upload"></i>
                                    Import Students
                                </a>
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
