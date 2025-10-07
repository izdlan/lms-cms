@extends('layouts.app')

@section('title', 'Student & Lecturer Login')

@section('content')
<div class="login-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6">
                <div class="login-image-section text-center">
                    <img src="https://lms.olympia-education.com/store/1/default_images/front_login.jpg" 
                         alt="Login Image" 
                         class="img-fluid rounded login-image">
                    <p class="mt-3 text-muted">LMS Olympia Portal</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login-form-section">
                    <div class="login-card">
                        <div class="login-header text-center mb-4">
                            <h2 class="login-title">Student & Lecturer Login</h2>
                            <p class="login-subtitle">Enter your credentials to continue</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="login-form" id="loginForm">
                            @csrf
                            
                            <!-- Login Type Selection -->
                            <div class="form-group mb-4">
                                <label class="form-label">Login As</label>
                                <div class="login-type-selection">
                                    <div class="login-type-option">
                                        <input type="radio" id="student-type" name="login_type" value="student" {{ old('login_type', 'student') == 'student' ? 'checked' : '' }}>
                                        <label for="student-type" class="login-type-label">
                                            <i data-feather="user" width="20" height="20"></i>
                                            <span>Student</span>
                                        </label>
                                    </div>
                                    <div class="login-type-option">
                                        <input type="radio" id="lecturer-type" name="login_type" value="lecturer" {{ old('login_type') == 'lecturer' ? 'checked' : '' }}>
                                        <label for="lecturer-type" class="login-type-label">
                                            <i data-feather="users" width="20" height="20"></i>
                                            <span>Lecturer</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Login Fields -->
                            <div id="student-fields" class="login-fields">
                                <div class="form-group mb-3">
                                    <label for="ic" class="form-label">IC Number</label>
                                    <input type="text" 
                                           class="form-control @error('ic') is-invalid @enderror" 
                                           id="ic" 
                                           name="ic" 
                                           value="{{ old('ic') }}" 
                                           placeholder="Enter your IC number"
                                           required>
                                    @error('ic')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Staff/Admin Login Fields -->
                            <div id="staff-fields" class="login-fields" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="staff-email" class="form-label">Email Address</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="staff-email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Enter your email address">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Field (Common) -->
                            <div class="form-group mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="password-input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           autocomplete="current-password"
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

                            <div class="login-divider">
                                <span>or</span>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('student.password.reset') }}" class="forgot-password-link" id="forgotPasswordLink">
                                    Forgot your password?
                                </a>
                            </div>
                            
                            <div class="login-divider">
                                <span>or</span>
                            </div>
                            
                            <div class="text-center">
                                <a href="{{ route('ex-student.login') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-graduation-cap"></i> Login as Ex-Student
                                </a>
                                <p class="mt-2 text-muted small">Verify your certificate with QR code</p>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('admin-finance.login') }}" class="admin-login-link">
                                    <i data-feather="shield" width="16" height="16"></i> Admin Login
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

.login-fields {
    transition: all 0.3s ease;
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

.forgot-password-link {
    color: #0056d2;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.forgot-password-link:hover {
    color: #0041a3;
    text-decoration: underline;
}

.admin-login-link {
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.admin-login-link:hover {
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
    const studentType = document.getElementById('student-type');
    const lecturerType = document.getElementById('lecturer-type');
    const studentFields = document.getElementById('student-fields');
    const staffFields = document.getElementById('staff-fields');
    const loginButton = document.getElementById('loginButton');
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    const icField = document.getElementById('ic');
    const staffEmailField = document.getElementById('staff-email');
    
    if (studentType.checked) {
        studentFields.style.display = 'block';
        staffFields.style.display = 'none';
        loginButton.textContent = 'Login as Student';
        forgotPasswordLink.href = '{{ route("student.password.reset") }}';
        icField.required = true;
        staffEmailField.required = false;
        // Disable hidden email fields
        staffEmailField.disabled = true;
        icField.disabled = false;
    } else if (lecturerType.checked) {
        studentFields.style.display = 'none';
        staffFields.style.display = 'block';
        loginButton.textContent = 'Login as Lecturer';
        forgotPasswordLink.href = '#';
        icField.required = false;
        staffEmailField.required = true;
        // Disable hidden fields
        icField.disabled = true;
        staffEmailField.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Add event listeners to radio buttons
    document.getElementById('student-type').addEventListener('change', toggleLoginType);
    document.getElementById('lecturer-type').addEventListener('change', toggleLoginType);
    
    // Initialize the form
    toggleLoginType();
});
</script>
@endpush
