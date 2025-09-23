@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="student-courses">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('student.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="courses-header">
                    <h1>My Courses</h1>
                    <p>Manage and track your course progress</p>
                </div>

                @if(auth()->user()->courses && count(auth()->user()->courses) > 0)
                    <div class="row">
                        @foreach(auth()->user()->courses as $index => $course)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="course-card">
                                    <div class="course-header">
                                        <div class="course-icon">
                                            <i data-feather="book" width="32" height="32"></i>
                                        </div>
                                        <div class="course-status">
                                            <span class="badge badge-enrolled">Enrolled</span>
                                        </div>
                                    </div>
                                    <div class="course-body">
                                        <h5 class="course-title">{{ $course }}</h5>
                                        <p class="course-description">
                                            @if($index % 3 == 0)
                                                Advanced management concepts and strategic planning
                                            @elseif($index % 3 == 1)
                                                Research methodology and academic writing
                                            @else
                                                Professional development and leadership skills
                                            @endif
                                        </p>
                                        <div class="course-meta">
                                            <div class="course-duration">
                                                <i data-feather="clock" width="16" height="16"></i>
                                                <span>12 weeks</span>
                                            </div>
                                            <div class="course-level">
                                                <i data-feather="trending-up" width="16" height="16"></i>
                                                <span>Advanced</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="course-footer">
                                        <div class="course-progress">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: {{ rand(10, 90) }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ rand(10, 90) }}% Complete</small>
                                        </div>
                                        <div class="course-actions">
                                            <button class="btn btn-sm btn-primary">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="book-open" width="64" height="64"></i>
                        </div>
                        <h3>No Courses Assigned</h3>
                        <p>You haven't been enrolled in any courses yet. Contact your administrator to get started.</p>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
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
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.course-header {
    background: linear-gradient(135deg, #0056d2, #0041a3);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.course-icon {
    color: white;
}

.course-status .badge-enrolled {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.course-body {
    padding: 1.5rem;
    flex-grow: 1;
}

.course-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.course-description {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.course-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.course-duration, .course-level {
    display: flex;
    align-items: center;
    color: #718096;
    font-size: 0.85rem;
}

.course-duration i, .course-level i {
    margin-right: 0.25rem;
}

.course-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.course-progress {
    margin-bottom: 1rem;
}

.progress {
    height: 6px;
    border-radius: 3px;
    background-color: #e9ecef;
    margin-bottom: 0.5rem;
}

.progress-bar {
    background: linear-gradient(90deg, #0056d2, #0041a3);
    border-radius: 3px;
}

.course-actions {
    text-align: center;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.empty-state-icon {
    color: #cbd5e0;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: #2d3748;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #718096;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .course-meta {
        flex-direction: column;
        gap: 0.5rem;
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

