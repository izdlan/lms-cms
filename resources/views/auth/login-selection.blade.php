@extends('layouts.app')

@section('title', 'Login Selection')

@section('content')
<div class="login-selection-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-8 mx-auto">
                <div class="login-selection-card">
                    <div class="text-center mb-5">
                        <h1 class="login-selection-title">Welcome to LMS Olympia</h1>
                        <p class="login-selection-subtitle">Please select your login type</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="login-option-card">
                                <div class="login-option-icon">
                                    <i data-feather="user" width="48" height="48"></i>
                                </div>
                                <h3 class="login-option-title">Student Login</h3>
                                <p class="login-option-description">
                                    Access your courses, assignments, and academic resources
                                </p>
                                <a href="{{ route('student.login') }}" class="btn btn-primary btn-login-option">
                                    Login as Student
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="login-option-card">
                                <div class="login-option-icon">
                                    <i data-feather="shield" width="48" height="48"></i>
                                </div>
                                <h3 class="login-option-title">Admin Login</h3>
                                <p class="login-option-description">
                                    Manage students, courses, and system administration
                                </p>
                                <a href="{{ route('admin.login') }}" class="btn btn-outline-primary btn-login-option">
                                    Login as Admin
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
.login-selection-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.login-selection-card {
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.login-selection-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.5rem;
    font-size: 2.5rem;
}

.login-selection-subtitle {
    color: #718096;
    font-size: 1.1rem;
}

.login-option-card {
    background: #f8fafc;
    padding: 2.5rem;
    border-radius: 15px;
    text-align: center;
    height: 100%;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.login-option-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.login-option-icon {
    color: #667eea;
    margin-bottom: 1.5rem;
}

.login-option-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.login-option-description {
    color: #718096;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.btn-login-option {
    padding: 0.875rem 2rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-login-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .login-selection-card {
        padding: 2rem;
    }
    
    .login-selection-title {
        font-size: 2rem;
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
