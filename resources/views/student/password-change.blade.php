@extends('layouts.app')

@section('title', 'Change Password | Student | Olympia Education')

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
                                            <strong>Password Requirements:</strong>
                                        </div>
                                        <div class="password-requirements">
                                            <div class="requirement" id="req-length">
                                                <i class="fas fa-times text-danger"></i>
                                                <span>At least 8 characters long</span>
                                            </div>
                                            <div class="requirement" id="req-uppercase">
                                                <i class="fas fa-times text-danger"></i>
                                                <span>One uppercase letter (A-Z)</span>
                                            </div>
                                            <div class="requirement" id="req-lowercase">
                                                <i class="fas fa-times text-danger"></i>
                                                <span>One lowercase letter (a-z)</span>
                                            </div>
                                            <div class="requirement" id="req-number">
                                                <i class="fas fa-times text-danger"></i>
                                                <span>One number (0-9)</span>
                                            </div>
                                            <div class="requirement" id="req-special">
                                                <i class="fas fa-times text-danger"></i>
                                                <span>One special character (!@#$%^&*)</span>
                                            </div>
                                        </div>
                                        <div class="password-strength mt-3">
                                            <div class="strength-label">Password Strength:</div>
                                            <div class="strength-bar">
                                                <div class="strength-fill" id="strength-fill"></div>
                                            </div>
                                            <div class="strength-text" id="strength-text">Very Weak</div>
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

/* Password Requirements Styles */
.password-requirements {
    margin-top: 0.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.requirement {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #6c757d;
}

.requirement i {
    margin-right: 0.5rem;
    width: 16px;
    text-align: center;
}

.requirement.valid i {
    color: #28a745;
}

.requirement.valid span {
    color: #28a745;
    font-weight: 500;
}

/* Password Strength Indicator */
.password-strength {
    margin-top: 1rem;
}

.strength-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.strength-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.strength-fill.very-weak {
    background: #dc3545;
    width: 20%;
}

.strength-fill.weak {
    background: #fd7e14;
    width: 40%;
}

.strength-fill.fair {
    background: #ffc107;
    width: 60%;
}

.strength-fill.good {
    background: #20c997;
    width: 80%;
}

.strength-fill.strong {
    background: #28a745;
    width: 100%;
}

.strength-text {
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
}

.strength-text.very-weak {
    color: #dc3545;
}

.strength-text.weak {
    color: #fd7e14;
}

.strength-text.fair {
    color: #ffc107;
}

.strength-text.good {
    color: #20c997;
}

.strength-text.strong {
    color: #28a745;
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
    
    // Password validation
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    
    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
    });
    
    confirmInput.addEventListener('input', function() {
        validatePasswordConfirmation();
    });
});

function validatePassword(password) {
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(req => {
        const element = document.getElementById(`req-${req}`);
        const icon = element.querySelector('i');
        
        if (requirements[req]) {
            element.classList.add('valid');
            icon.className = 'fas fa-check text-success';
        } else {
            element.classList.remove('valid');
            icon.className = 'fas fa-times text-danger';
        }
    });
    
    // Calculate password strength
    let score = 0;
    Object.values(requirements).forEach(valid => {
        if (valid) score++;
    });
    
    // Additional scoring based on length
    if (password.length >= 12) score += 0.5;
    if (password.length >= 16) score += 0.5;
    
    // Check for common patterns (penalize)
    if (/(.)\1{2,}/.test(password)) score -= 1; // Repeated characters
    if (/123|abc|qwe|asd|zxc/i.test(password)) score -= 1; // Sequential patterns
    
    updatePasswordStrength(score, password.length);
}

function updatePasswordStrength(score, length) {
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    
    // Remove all strength classes
    strengthFill.className = 'strength-fill';
    strengthText.className = 'strength-text';
    
    if (score < 2 || length < 8) {
        strengthFill.classList.add('very-weak');
        strengthText.classList.add('very-weak');
        strengthText.textContent = 'Very Weak';
    } else if (score < 3) {
        strengthFill.classList.add('weak');
        strengthText.classList.add('weak');
        strengthText.textContent = 'Weak';
    } else if (score < 4) {
        strengthFill.classList.add('fair');
        strengthText.classList.add('fair');
        strengthText.textContent = 'Fair';
    } else if (score < 5) {
        strengthFill.classList.add('good');
        strengthText.classList.add('good');
        strengthText.textContent = 'Good';
    } else {
        strengthFill.classList.add('strong');
        strengthText.classList.add('strong');
        strengthText.textContent = 'Strong';
    }
}

function validatePasswordConfirmation() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const confirmInput = document.getElementById('password_confirmation');
    
    if (confirm && password !== confirm) {
        confirmInput.classList.add('is-invalid');
        if (!confirmInput.nextElementSibling || !confirmInput.nextElementSibling.classList.contains('invalid-feedback')) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = 'Passwords do not match.';
            confirmInput.parentNode.appendChild(errorDiv);
        }
    } else {
        confirmInput.classList.remove('is-invalid');
        const errorDiv = confirmInput.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
}
</script>
@endpush
