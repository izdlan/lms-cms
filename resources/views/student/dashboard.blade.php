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
                            <h4>{{ $enrolledSubjects->count() }}</h4>
                            <p class="text-muted mb-0">MY SUBJECTS</p>
                        </div>
                    </div>
                </div>

                <!-- My Subjects Section -->
                <div class="card">
                    <div class="card-header">
                        <h5><i data-feather="book-open" width="20" height="20" class="me-2"></i>My Subjects</h5>
                    </div>
                    <div class="card-body">
                        @if($enrolledSubjects->count() > 0)
                            <div class="row">
                                @foreach($enrolledSubjects as $enrollment)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="course-card">
                                            <div class="course-image" style="background-image: url('{{ $enrollment->subject && $enrollment->subject->image ? asset('storage/' . $enrollment->subject->image) : '' }}');">
                                                @if(!$enrollment->subject || !$enrollment->subject->image)
                                                    <i class="fas fa-book"></i>
                                                @endif
                                            </div>
                                            <div class="course-content">
                                                <h6 class="course-title">{{ $enrollment->subject ? $enrollment->subject->name : '-' }}</h6>
                                                <p class="course-code">{{ $enrollment->subject_code }} - {{ $enrollment->class_code ?? '-' }}</p>
                                                <small class="text-muted">Lecturer: {{ $enrollment->lecturer ? $enrollment->lecturer->name : '-' }}</small>
                                                <div class="mt-2">
                                                    <a href="{{ route('student.course.class', $enrollment->subject_code) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-arrow-right me-1"></i>Enter Class
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i data-feather="book-open" width="48" height="48" class="text-muted mb-3"></i>
                                <h5 class="text-muted">No subjects enrolled</h5>
                                <p class="text-muted">Your enrolled subjects will appear here once you are registered for courses.</p>
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
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    position: relative;
}

.course-card .course-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
    z-index: 1;
}

.course-card .course-image i {
    position: relative;
    z-index: 2;
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