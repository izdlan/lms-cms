@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="student-dashboard">
    <!-- Student Navigation Bar -->
    @include('student.partials.student-navbar')
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('student.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="dashboard-header">
                    <h1>Dashboard</h1>
                    <p class="text-muted">Here's your academic overview and course information.</p>
                </div>

                @if(auth('student')->user()->must_reset_password)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i data-feather="alert-triangle" width="20" height="20"></i>
                        <strong>Password Reset Required!</strong> Please change your password for security reasons.
                        <a href="{{ route('student.password.reset') }}" class="btn btn-sm btn-warning ms-2">Change Password</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Overview Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="icon primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4>0</h4>
                            <p class="text-muted mb-0">TOTAL OLYMPIA STUDENTS</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="icon success">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h4>0</h4>
                            <p class="text-muted mb-0">TOTAL INSTRUCTORS</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="icon info">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h4>0</h4>
                            <p class="text-muted mb-0">TOTAL COURSES</p>
                        </div>
                    </div>
                </div>

                <!-- My Courses Section -->
                <div class="card">
                    <div class="card-header">
                        <h5><i data-feather="book-open" width="20" height="20" class="me-2"></i>My Courses</h5>
                    </div>
                    <div class="card-body">
                        @if(auth('student')->user()->courses && count(auth('student')->user()->courses) > 0)
                            <div class="row">
                                @foreach(auth('student')->user()->courses as $course)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="course-card">
                                            <div class="course-image">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div class="course-content">
                                                <h6 class="course-title">{{ $course }}</h6>
                                                <p class="course-code">Course Code: {{ $course }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i data-feather="book-open" width="48" height="48" class="text-muted mb-3"></i>
                                <h5 class="text-muted">No courses assigned</h5>
                                <p class="text-muted">Your courses will appear here once they are assigned by your administrator.</p>
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

.stats-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    margin-bottom: 1.5rem;
}

.stats-card .icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
}

.stats-card .icon.primary { background: #e3f2fd; color: #1976d2; }
.stats-card .icon.success { background: #e8f5e8; color: #388e3c; }
.stats-card .icon.info { background: #e0f2f1; color: #00796b; }

.course-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    margin-bottom: 1.5rem;
}

.course-card:hover {
    transform: translateY(-5px);
}

.course-card .course-image {
    height: 120px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.course-card .course-content {
    padding: 1.5rem;
}

.course-card .course-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.course-card .course-code {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
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