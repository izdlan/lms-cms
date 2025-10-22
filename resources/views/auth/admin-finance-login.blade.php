@extends('layouts.app')

@section('title', 'Admin & Finance Login')

@section('content')
<div class="login-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6">
                <div class="login-image-section text-center">
                    <img src="store/1/default_images/front_login.jpg" 
                         alt="Admin Login Image" 
                         class="img-fluid rounded login-image">
                    <p class="mt-3 text-muted">Admin Portal</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login-form-section">
                    <div class="login-card">
                        <div class="login-header text-center mb-4">
                            <h2 class="login-title">Admin & Finance Login</h2>
                            <p class="login-subtitle">Enter your credentials to continue</p>
                        </div>

                        <form method="POST" action="{{ route('admin-finance.login') }}" class="login-form" id="loginForm">
                            @csrf
                            
                            <!-- Login Type Selection -->
                            <div class="form-group mb-4">
                                <label class="form-label">Login As</label>
                                <div class="login-type-selection">
                                    <div class="login-type-option">
                                        <input type="radio" id="admin-type" name="login_type" value="admin" {{ old('login_type', 'admin') == 'admin' ? 'checked' : '' }}>
                                        <label for="admin-type" class="login-type-label">
                                            <i data-feather="shield" width="20" height="20"></i>
                                            <span>Admin</span>
                                        </label>
                                    </div>
                                    <div class="login-type-option">
                                        <input type="radio" id="finance-admin-type" name="login_type" value="finance_admin" {{ old('login_type') == 'finance_admin' ? 'checked' : '' }}>
                                        <label for="finance-admin-type" class="login-type-label">
                                            <i data-feather="dollar-sign" width="20" height="20"></i>
                                            <span>Finance Admin</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Field -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter your email address"
                                       required 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="form-group mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="password-input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword()">
                                        <i data-feather="eye" width="20" height="20"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-login w-100" id="loginButton">
                                Login
                            </button>

                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="back-to-student-login">
                                    <i data-feather="arrow-left" width="16" height="16"></i> Student & Lecturer Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.login-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.login-image {
    max-width: 100%;
    height: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
}

.login-card {
    background: white;
    padding: 3rem;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    max-width: 450px;
    margin: 0 auto;
}

.login-title {
    color: #0056d2;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.login-subtitle {
    color: #6c757d;
    font-size: 0.95rem;
}

.login-type-selection {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.login-type-option {
    flex: 1;
}

.login-type-option input[type="radio"] {
    display: none;
}

.login-type-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.login-type-option.disabled .login-type-label {
    cursor: not-allowed;
    background-color: #f8f9fa;
    color: #6c757d;
}

.login-type-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 0.5rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    font-weight: 500;
    color: #6c757d;
    font-size: 0.9rem;
}

.login-type-label:hover {
    border-color: #0056d2;
    color: #0056d2;
}

.login-type-option input[type="radio"]:checked + .login-type-label {
    border-color: #0056d2;
    background: #e3f2fd;
    color: #0056d2;
}

.form-label {
    font-weight: 600;
    color: #333;
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
    border-color: #0056d2;
    box-shadow: 0 0 0 0.2rem rgba(0, 86, 210, 0.25);
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
    color: #0056d2;
}

.btn-login {
    background: #0056d2;
    border: none;
    padding: 0.875rem 1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-login:hover {
    background: #0041a3;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 86, 210, 0.4);
}

.back-to-student-login {
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.back-to-student-login:hover {
    color: #0056d2;
    text-decoration: underline;
}

.form-check-input:checked {
    background-color: #0056d2;
    border-color: #0056d2;
}

@media (max-width: 991.98px) {
    .login-image-section {
        margin-bottom: 2rem;
    }
    
    .login-card {
        padding: 2rem;
    }
    
    .login-type-selection {
        flex-direction: column;
    }
}
</style>
@endpush

@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.querySelector('.password-toggle i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.setAttribute('data-feather', 'eye-off');
    } else {
        passwordInput.type = 'password';
        toggleButton.setAttribute('data-feather', 'eye');
    }
    
    feather.replace();
}

function toggleLoginType() {
    const adminType = document.getElementById('admin-type');
    const financeAdminType = document.getElementById('finance-admin-type');
    const loginButton = document.getElementById('loginButton');
    
    if (adminType.checked) {
        loginButton.textContent = 'Login as Admin';
    } else if (financeAdminType.checked) {
        loginButton.textContent = 'Login as Finance Admin';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Add event listeners to radio buttons
    document.getElementById('admin-type').addEventListener('change', toggleLoginType);
    document.getElementById('finance-admin-type').addEventListener('change', toggleLoginType);
    
    // Initialize the form
    toggleLoginType();
});
</script>
@endpush
