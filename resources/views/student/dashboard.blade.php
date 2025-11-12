@extends('layouts.app')

@section('title', 'Dashboard | Student | Olympia Education')

@section('content')
<div class="student-dashboard">
    <!-- Student Navigation Bar -->
    @include('student.partials.student-navbar')
    
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay d-lg-none"></div>
    
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

                <!-- System Maintenance Banner 
                <div class="maintenance-banner" role="alert">
                    <strong>Notice:</strong> Dear student, please be informed that your profile picture and password have been reset due to system maintenance. You may update them again upon your next login. Thank you for your understanding.
                </div> -->

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
                    <div class="col-md-6">
                        <div class="stats-card">
                            <div class="icon success">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h4>{{ $enrolledSubjects->pluck('lecturer_id')->unique()->count() }}</h4>
                            <p class="text-muted mb-0">MY LECTURERS</p>
                        </div>
                    </div>
                    <div class="col-md-6">
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

.maintenance-banner {
    background: #dc2626; /* solid red */
    border: 2px solid #b91c1c; /* darker red border */
    color: #ffffff; /* white text for contrast */
    padding: 1rem 1.25rem;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(185, 28, 28, 0.25);
    margin-bottom: 1rem;
    font-weight: 600;
}

.maintenance-banner a { color: #ffffff; text-decoration: underline; }

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

/* Mobile responsive styles */
@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
        padding: 1rem;
    }
    
    .stats-card .icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .course-card .course-content {
        padding: 1rem;
    }
    
    .dashboard-header h1 {
        font-size: 1.5rem;
    }
    
    .dashboard-header p {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .stats-card {
        padding: 0.75rem;
    }
    
    .stats-card h4 {
        font-size: 1.5rem;
    }
    
    .stats-card p {
        font-size: 0.8rem;
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