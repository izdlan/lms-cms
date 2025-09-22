@extends('layouts.app')

@section('title', 'Student Login')

@section('content')
<div class="login-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6">
                <div class="login-image-section text-center">
                    <img src="https://lms.olympia-education.com/store/1/default_images/front_login.jpg" 
                         alt="Student Login Image" 
                         class="img-fluid rounded login-image">
                    <p class="mt-3 text-muted">Student Portal</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login-form-section">
                    <div class="login-card">
                        <div class="login-header text-center mb-4">
                            <h2 class="login-title">Student Login</h2>
                            <p class="login-subtitle">Enter your IC/Passport or Email and password to continue</p>
                        </div>

                        <form method="POST" action="{{ route('student.login') }}" class="login-form">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label for="login" class="form-label">IC/Passport or Email</label>
                                <input type="text" 
                                       class="form-control @error('login') is-invalid @enderror" 
                                       id="login" 
                                       name="login" 
                                       value="{{ old('login') }}" 
                                       placeholder="Enter your IC/Passport or Email"
                                       required 
                                       autofocus>
                                @error('login')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

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

                            <button type="submit" class="btn btn-primary btn-login w-100">
                                Login as Student
                            </button>

                            <div class="login-divider">
                                <span>or</span>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('student.password.reset') }}" class="forgot-password-link">Forgot your password?</a>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('login.selection') }}" class="back-to-selection">
                                    ← Back to Login Selection
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

.login-divider {
    text-align: center;
    margin: 1.5rem 0;
    position: relative;
}

.login-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e9ecef;
}

.login-divider span {
    background: white;
    padding: 0 1rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.forgot-password-link, .back-to-selection {
    color: #0056d2;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.forgot-password-link:hover, .back-to-selection:hover {
    color: #0041a3;
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

document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
