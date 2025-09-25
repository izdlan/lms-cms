@extends('layouts.app')

@section('title', 'Change Password')

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
                <div class="password-change-header">
                    <h1>Change Password</h1>
                    <p>Update your account password for security.</p>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="password-change-card">
                            <div class="card-header">
                                <h5><i data-feather="shield" width="20" height="20" class="me-2"></i>Password Security</h5>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i data-feather="check-circle" width="20" height="20"></i>
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('student.password.change.post') }}" class="password-change-form">
                                    @csrf
                                    
                                    <div class="form-group mb-4">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <div class="password-input-group">
                                            <input type="password" 
                                                   class="form-control @error('current_password') is-invalid @enderror" 
                                                   id="current_password" 
                                                   name="current_password" 
                                                   placeholder="Enter your current password"
                                                   required 
                                                   autofocus>
                                            <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                                <i data-feather="eye" width="20" height="20"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="password-input-group">
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Enter your new password"
                                                   required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                                <i data-feather="eye" width="20" height="20"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="form-text">
                                            Password must be at least 6 characters long.
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <div class="password-input-group">
                                            <input type="password" 
                                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   placeholder="Confirm your new password"
                                                   required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                                <i data-feather="eye" width="20" height="20"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i data-feather="save" width="16" height="16"></i>
                                            Change Password
                                        </button>
                                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                                            <i data-feather="arrow-left" width="16" height="16"></i>
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Security Tips -->
                        <div class="security-tips">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i data-feather="info" width="16" height="16" class="me-2"></i>Password Security Tips</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="security-tips-list">
                                        <li>Use a combination of letters, numbers, and symbols</li>
                                        <li>Make it at least 8 characters long</li>
                                        <li>Avoid using personal information</li>
                                        <li>Don't reuse passwords from other accounts</li>
                                        <li>Consider using a password manager</li>
                                    </ul>
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

.password-change-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.card-header h5 {
    margin: 0;
    color: #2d3748;
    font-weight: bold;
    display: flex;
    align-items: center;
}

.card-body {
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #6c757d;
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
}

.password-input-group {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
}

.password-toggle:hover {
    color: #495057;
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
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

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

.security-tips .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
}

.security-tips .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
}

.security-tips .card-header h6 {
    margin: 0;
    color: #2d3748;
    font-weight: bold;
    display: flex;
    align-items: center;
}

.security-tips .card-body {
    padding: 1.5rem;
}

.security-tips-list {
    margin: 0;
    padding-left: 1.5rem;
    color: #6c757d;
}

.security-tips-list li {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.alert {
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: #d1edff;
    color: #0c5460;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .form-actions {
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
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleButton = passwordInput.parentNode.querySelector('.password-toggle i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.setAttribute('data-feather', 'eye-off');
    } else {
        passwordInput.type = 'password';
        toggleButton.setAttribute('data-feather', 'eye');
    }
    
    feather.replace();
}

document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
