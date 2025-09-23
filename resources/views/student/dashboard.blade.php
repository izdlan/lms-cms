@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="student-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('student.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1>Welcome back, {{ auth()->user()->name }}!</h1>
                            <p class="mb-0">
                                <span class="badge bg-primary me-2">{{ auth()->user()->programme_name ?? 'Student' }}</span>
                                <span class="text-muted">{{ auth()->user()->faculty ?? 'Faculty' }}</span>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="student-info">
                                <small class="text-muted">Student ID: {{ auth()->user()->student_id ?? 'N/A' }}</small><br>
                                <small class="text-muted">IC: {{ auth()->user()->ic ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
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
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon bg-primary">
                                <i data-feather="book-open" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ count(auth()->user()->courses ?? []) }}</h3>
                                <p>My Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon bg-success">
                                <i data-feather="calendar" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ auth()->user()->semester_entry ?? 'N/A' }}</h3>
                                <p>Current Semester</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon bg-info">
                                <i data-feather="id-card" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ auth()->user()->student_id ?? 'N/A' }}</h3>
                                <p>Student ID</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon bg-warning">
                                <i data-feather="award" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ auth()->user()->category ?? 'Local' }}</h3>
                                <p>Category</p>
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

                <!-- Academic Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i data-feather="graduation-cap" width="20" height="20" class="me-2"></i>Academic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item">
                                    <label>Programme:</label>
                                    <span>{{ auth()->user()->programme_name ?? 'Not specified' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Faculty:</label>
                                    <span>{{ auth()->user()->faculty ?? 'Not specified' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Programme Code:</label>
                                    <span>{{ auth()->user()->programme_code ?? 'Not specified' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Category:</label>
                                    <span class="badge bg-secondary">{{ auth()->user()->category ?? 'Not specified' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Intake:</label>
                                    <span>{{ auth()->user()->programme_intake ?? 'Not specified' }}</span>
                                </div>
                                @if(auth()->user()->date_of_commencement)
                                <div class="info-item">
                                    <label>Commencement Date:</label>
                                    <span>{{ \Carbon\Carbon::parse(auth()->user()->date_of_commencement)->format('d M Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i data-feather="user" width="20" height="20" class="me-2"></i>Student Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item">
                                    <label>Student ID:</label>
                                    <span class="badge bg-primary">{{ auth()->user()->student_id ?? 'Not assigned' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>IC Number:</label>
                                    <span>{{ auth()->user()->ic ?? 'Not specified' }}</span>
                                </div>
                                @if(auth()->user()->col_ref_no)
                                <div class="info-item">
                                    <label>College Ref No:</label>
                                    <span>{{ auth()->user()->col_ref_no }}</span>
                                </div>
                                @endif
                                @if(auth()->user()->previous_university)
                                <div class="info-item">
                                    <label>Previous University:</label>
                                    <span>{{ auth()->user()->previous_university }}</span>
                                </div>
                                @endif
                                @if(auth()->user()->student_portal_username)
                                <div class="info-item">
                                    <label>Portal Username:</label>
                                    <span class="badge bg-info">{{ auth()->user()->student_portal_username }}</span>
                                </div>
                                @endif
                                <div class="info-item">
                                    <label>Account Status:</label>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information & Important Dates -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i data-feather="phone" width="20" height="20" class="me-2"></i>Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span>{{ auth()->user()->email }}</span>
                                </div>
                                @if(auth()->user()->phone)
                                <div class="info-item">
                                    <label>Phone:</label>
                                    <span>{{ auth()->user()->phone }}</span>
                                </div>
                                @endif
                                @if(auth()->user()->address)
                                <div class="info-item">
                                    <label>Address:</label>
                                    <span>{{ auth()->user()->address }}</span>
                                </div>
                                @endif
                                <div class="info-item">
                                    <label>Login Method:</label>
                                    <span class="badge bg-success">IC Number</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i data-feather="calendar" width="20" height="20" class="me-2"></i>Important Dates</h5>
                            </div>
                            <div class="card-body">
                                @if(auth()->user()->col_date)
                                <div class="info-item">
                                    <label>College Date:</label>
                                    <span>{{ \Carbon\Carbon::parse(auth()->user()->col_date)->format('d M Y') }}</span>
                                </div>
                                @endif
                                @if(auth()->user()->date_of_commencement)
                                <div class="info-item">
                                    <label>Programme Start:</label>
                                    <span>{{ \Carbon\Carbon::parse(auth()->user()->date_of_commencement)->format('d M Y') }}</span>
                                </div>
                                @endif
                                <div class="info-item">
                                    <label>Last Login:</label>
                                    <span>{{ auth()->user()->updated_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Account Created:</label>
                                    <span>{{ auth()->user()->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5><i data-feather="zap" width="20" height="20" class="me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('student.profile') }}" class="quick-action-btn">
                                    <i data-feather="user" width="24" height="24"></i>
                                    <span>Update Profile</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="#" class="quick-action-btn" onclick="alert('Feature coming soon!')">
                                    <i data-feather="file-text" width="24" height="24"></i>
                                    <span>View Assignments</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="#" class="quick-action-btn" onclick="alert('Feature coming soon!')">
                                    <i data-feather="download" width="24" height="24"></i>
                                    <span>Download Materials</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="#" class="quick-action-btn" onclick="alert('Feature coming soon!')">
                                    <i data-feather="message-circle" width="24" height="24"></i>
                                    <span>Contact Support</span>
                                </a>
                            </div>
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
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stats-icon.bg-primary { background: #0056d2; }
.stats-icon.bg-success { background: #28a745; }
.stats-icon.bg-info { background: #17a2b8; }
.stats-icon.bg-warning { background: #ffc107; color: #212529; }

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

/* Info Items */
.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    min-width: 120px;
}

.info-item span {
    color: #6c757d;
    text-align: right;
    flex: 1;
}

.research-title {
    font-style: italic;
    color: #0056d2 !important;
    font-weight: 500;
}

/* Quick Action Buttons */
.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
    text-align: center;
    min-height: 120px;
    justify-content: center;
}

.quick-action-btn:hover {
    background: #0056d2;
    color: white;
    border-color: #0056d2;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 86, 210, 0.3);
}

.quick-action-btn i {
    margin-bottom: 0.5rem;
}

.quick-action-btn span {
    font-weight: 500;
    font-size: 0.9rem;
}

/* Student Info */
.student-info {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 4px solid #0056d2;
}

/* Badge Styles */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Card Header Icons */
.card-header h5 i {
    color: #0056d2;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .info-item label {
        min-width: auto;
        margin-bottom: 0.25rem;
    }
    
    .info-item span {
        text-align: left;
    }
    
    .quick-action-btn {
        min-height: 100px;
        padding: 1rem 0.5rem;
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