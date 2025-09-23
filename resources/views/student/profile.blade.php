@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div class="student-profile">
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
                    <a href="{{ route('student.courses') }}" class="nav-link">
                        <i data-feather="book-open" width="20" height="20"></i>
                        My Courses
                    </a>
                    <a href="{{ route('student.assignments') }}" class="nav-link">
                        <i data-feather="file-text" width="20" height="20"></i>
                        Assignments
                    </a>
                    <a href="{{ route('student.profile') }}" class="nav-link active">
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
                <div class="profile-header">
                    <h1>My Profile</h1>
                    <p>Manage your personal information and account settings</p>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i data-feather="user" width="20" height="20" class="me-2"></i>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" value="{{ auth()->user()->name }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="ic" class="form-label">IC Number</label>
                                            <input type="text" class="form-control" id="ic" value="{{ auth()->user()->ic }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone" value="{{ auth()->user()->phone ?? 'Not provided' }}" readonly>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" id="address" rows="3" readonly>{{ auth()->user()->address ?? 'Not provided' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <i data-feather="info" width="16" height="16" class="me-2"></i>
                                        <strong>Note:</strong> To update your personal information, please contact the administration office.
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i data-feather="key" width="20" height="20" class="me-2"></i>Account Security</h5>
                            </div>
                            <div class="card-body">
                                <div class="security-item">
                                    <label>Password:</label>
                                    <span class="text-muted">Last changed: Never</span>
                                    <button class="btn btn-sm btn-outline-primary" onclick="alert('Password change feature coming soon!')">Change</button>
                                </div>
                                <div class="security-item">
                                    <label>Two-Factor Auth:</label>
                                    <span class="text-muted">Not enabled</span>
                                    <button class="btn btn-sm btn-outline-secondary" disabled>Enable</button>
                                </div>
                                <div class="security-item">
                                    <label>Login Activity:</label>
                                    <span class="text-muted">Last login: {{ auth()->user()->updated_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5><i data-feather="settings" width="20" height="20" class="me-2"></i>Account Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="emailNotifications" checked disabled>
                                    <label class="form-check-label" for="emailNotifications">
                                        Email Notifications
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="smsNotifications" disabled>
                                    <label class="form-check-label" for="smsNotifications">
                                        SMS Notifications
                                    </label>
                                </div>
                                <div class="alert alert-warning">
                                    <small>Settings are managed by the system administrator.</small>
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
.student-profile {
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

.profile-header h1 {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.profile-header p {
    color: #718096;
    margin-bottom: 2rem;
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

.card-header h5 i {
    color: #0056d2;
}

.security-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.security-item:last-child {
    border-bottom: none;
}

.security-item label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    min-width: 120px;
}

.security-item span {
    color: #6c757d;
    flex: 1;
    margin: 0 1rem;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .security-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .security-item label {
        min-width: auto;
        margin-bottom: 0.25rem;
    }
    
    .security-item span {
        margin: 0 0 0.5rem 0;
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