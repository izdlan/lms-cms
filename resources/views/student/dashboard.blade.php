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
                                    <span class="badge">{{ auth()->user()->category ?? 'Not specified' }}</span>
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
                                    <span class="badge">{{ auth()->user()->student_id ?? 'Not assigned' }}</span>
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
                                    <span class="badge">{{ auth()->user()->student_portal_username }}</span>
                                </div>
                                @endif
                                <div class="info-item">
                                    <label>Account Status:</label>
                                    <span class="badge">Active</span>
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
                                    <span class="badge">IC Number</span>
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

.stats-icon.bg-primary { background: #6c757d; }
.stats-icon.bg-success { background: #6c757d; }
.stats-icon.bg-info { background: #6c757d; }
.stats-icon.bg-warning { background: #6c757d; color: #fff; }

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


/* Student Info */
.student-info {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 4px solid #6c757d;
}

    /* Badge Styles - No background */
    .badge {
        font-size: 0.75rem;
        padding: 0;
        background-color: transparent !important;
        color: #6c757d !important;
        font-weight: 500;
    }

/* Card Header Icons - Neutral color */
.card-header h5 i {
    color: #6c757d;
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
