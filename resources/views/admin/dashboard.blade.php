@extends('layouts.app')

@section('title', 'Admin Dashboard')

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
                    <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                        <i data-feather="home" width="20" height="20"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.students') }}" class="nav-link">
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
