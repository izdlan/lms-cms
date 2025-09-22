@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="student-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4>Student Portal</h4>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('student.dashboard') }}" class="nav-link active">
                        <i data-feather="home" width="20" height="20"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('student.courses') }}" class="nav-link">
                        <i data-feather="book-open" width="20" height="20"></i>
                        My Courses
                    </a>
                    <a href="{{ route('student.assignments') }}" class="nav-link">
                        <i data-feather="file-text" width="20" height="20"></i>
                        Assignments
                    </a>
                    <a href="{{ route('student.profile') }}" class="nav-link">
                        <i data-feather="user" width="20" height="20"></i>
                        Profile
                    </a>
                    <a href="{{ route('student.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-feather="log-out" width="20" height="20"></i>
                        Logout
                    </a>
                </nav>
                <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>Welcome, {{ auth()->user()->name }}!</h1>
                    <p>Student Dashboard</p>
                </div>

                @if(auth()->user()->must_reset_password)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i data-feather="alert-triangle" width="20" height="20"></i>
                        <strong>Password Reset Required!</strong> Please change your password for security reasons.
                        <a href="{{ route('student.password.reset') }}" class="btn btn-sm btn-warning ms-2">Change Password</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i data-feather="book-open" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ count(auth()->user()->courses ?? []) }}</h3>
                                <p>My Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i data-feather="file-text" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>0</h3>
                                <p>Assignments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i data-feather="award" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>0</h3>
                                <p>Certificates</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Courses -->
                <div class="card">
                    <div class="card-header">
                        <h5>My Courses</h5>
                        <a href="{{ route('student.courses') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->courses && count(auth()->user()->courses) > 0)
                            <div class="row">
                                @foreach(auth()->user()->courses as $course)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="course-card">
                                            <div class="course-icon">
                                                <i data-feather="book" width="32" height="32"></i>
                                            </div>
                                            <h6 class="course-title">{{ $course }}</h6>
                                            <p class="course-description">Course description will be shown here</p>
                                            <div class="course-progress">
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <small class="text-muted">0% Complete</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i data-feather="book-open" width="48" height="48" class="text-muted mb-3"></i>
                                <h5 class="text-muted">No courses assigned</h5>
                                <p class="text-muted">Contact your administrator to get enrolled in courses.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i data-feather="activity" width="48" height="48" class="text-muted mb-3"></i>
                            <h5 class="text-muted">No recent activity</h5>
                            <p class="text-muted">Your recent activities will appear here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.student-dashboard {
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
    background: #0056d2;
    color: white;
    border-left-color: #0041a3;
}

.nav-link i {
    margin-right: 0.75rem;
}

.main-content {
    padding: 2rem;
}

.dashboard-header h1 {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.dashboard-header p {
    color: #718096;
    margin-bottom: 2rem;
}

.stats-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.stats-icon {
    background: #0056d2;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stats-content h3 {
    font-size: 2rem;
    font-weight: bold;
    color: #2d3748;
    margin: 0;
}

.stats-content p {
    color: #718096;
    margin: 0;
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

.course-card {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border-color: #0056d2;
}

.course-icon {
    color: #0056d2;
    margin-bottom: 1rem;
}

.course-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.course-description {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.course-progress {
    margin-top: 1rem;
}

.progress {
    height: 6px;
    border-radius: 3px;
    background-color: #e9ecef;
}

.progress-bar {
    background-color: #0056d2;
    border-radius: 3px;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
