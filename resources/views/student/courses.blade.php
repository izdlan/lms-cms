@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="student-courses">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4>Student Portal</h4>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('student.dashboard') }}" class="nav-link">
                        <i data-feather="home" width="20" height="20"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('student.courses') }}" class="nav-link active">
                        <i data-feather="book-open" width="20" height="20"></i>
                        My Courses
                    </a>
                    <a href="{{ route('student.password.change') }}" class="nav-link">
                        <i data-feather="key" width="20" height="20"></i>
                        Change Password
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
                <div class="courses-header">
                    <h1>My Courses</h1>
                    <p>View all your enrolled courses and academic progress.</p>
                </div>

                @if(auth()->user()->courses && count(auth()->user()->courses) > 0)
                    <div class="row">
                        @foreach(auth()->user()->courses as $course)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="course-card">
                                    <div class="course-header">
                                        <div class="course-icon">
                                            <i data-feather="book" width="32" height="32"></i>
                                        </div>
                                        <div class="course-code">{{ $course }}</div>
                                    </div>
                                    <div class="course-body">
                                        <h5 class="course-title">{{ $course }}</h5>
                                        <p class="course-description">
                                            Course description and details will be shown here. 
                                            This is where you can find information about the course content, 
                                            learning objectives, and requirements.
                                        </p>
                                        <div class="course-meta">
                                            <div class="meta-item">
                                                <i data-feather="calendar" width="16" height="16"></i>
                                                <span>Current Semester</span>
                                            </div>
                                            <div class="meta-item">
                                                <i data-feather="user" width="16" height="16"></i>
                                                <span>Instructor TBD</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="course-footer">
                                        <div class="course-progress">
                                            <div class="progress-label">
                                                <span>Progress</span>
                                                <span>0%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <div class="course-actions">
                                            <button class="btn btn-outline-primary btn-sm" onclick="alert('Course materials coming soon!')">
                                                <i data-feather="download" width="16" height="16"></i>
                                                Materials
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" onclick="alert('Assignments coming soon!')">
                                                <i data-feather="file-text" width="16" height="16"></i>
                                                Assignments
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-courses">
                        <div class="no-courses-icon">
                            <i data-feather="book-open" width="64" height="64"></i>
                        </div>
                        <h3>No Courses Assigned</h3>
                        <p>You haven't been enrolled in any courses yet. Please contact your administrator to get enrolled.</p>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                            <i data-feather="arrow-left" width="16" height="16"></i>
                            Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.student-courses {
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
    background: #6c757d;
    color: white;
    border-left-color: #495057;
}

.nav-link i {
    margin-right: 0.75rem;
}

.main-content {
    padding: 2rem;
}

.courses-header h1 {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.courses-header p {
    color: #718096;
    margin-bottom: 2rem;
}

.course-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.course-header {
    background: #f8f9fa;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #e9ecef;
}

.course-icon {
    color: #6c757d;
    margin-right: 1rem;
}

.course-code {
    background: #6c757d;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.course-body {
    padding: 1.5rem;
    flex: 1;
}

.course-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.75rem;
}

.course-description {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.course-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    color: #6c757d;
    font-size: 0.85rem;
}

.meta-item i {
    margin-right: 0.5rem;
    width: 16px;
    height: 16px;
}

.course-footer {
    padding: 1.5rem;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
}

.course-progress {
    margin-bottom: 1rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    background: #6c757d;
    height: 100%;
    transition: width 0.3s ease;
}

.course-actions {
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.btn-outline-primary {
    color: #6c757d;
    border-color: #6c757d;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #6c757d;
    color: white;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

.btn-primary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-primary:hover {
    background: #5a6268;
    border-color: #5a6268;
    color: white;
}

.no-courses {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.no-courses-icon {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.no-courses h3 {
    color: #2d3748;
    margin-bottom: 1rem;
}

.no-courses p {
    color: #6c757d;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .course-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
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
