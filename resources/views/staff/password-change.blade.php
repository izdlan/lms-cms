@extends('layouts.staff-course')

@section('title', 'Change Password | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>Change Password</h1>
    <p>Update your account password for security</p>
</div>

<div class="content-card">
    <h5><i class="fas fa-shield-alt"></i> Password Security</h5>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('staff.password.update') }}" class="password-change-form">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
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
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="password" class="form-label">New Password</label>
                    <div class="password-input-group">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your new password"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
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
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <div class="password-input-group">
                        <input type="password" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Confirm your new password"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button type="submit" class="action-btn primary">
                <i class="fas fa-save"></i>
                Update Password
            </button>
            <a href="{{ route('staff.profile') }}" class="action-btn secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Profile
            </a>
        </div>
    </form>
</div>

<!-- Security Tips -->
<div class="content-card mt-4">
    <h5><i class="fas fa-info-circle"></i> Password Security Tips</h5>
    <div class="security-tips">
        <ul class="security-tips-list">
            <li><i class="fas fa-check text-success"></i> Use a combination of letters, numbers, and symbols</li>
            <li><i class="fas fa-check text-success"></i> Make it at least 8 characters long (12+ recommended)</li>
            <li><i class="fas fa-check text-success"></i> Avoid using personal information (name, birthdate, etc.)</li>
            <li><i class="fas fa-check text-success"></i> Don't reuse passwords from other accounts</li>
            <li><i class="fas fa-check text-success"></i> Consider using a password manager</li>
            <li><i class="fas fa-check text-success"></i> Change your password regularly</li>
        </ul>
    </div>
</div>

@endsection

@push('styles')
<style>
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

/* Password Input Group */
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

/* Security Tips */
.security-tips-list {
    margin: 0;
    padding-left: 0;
    color: #6c757d;
    list-style: none;
}

.security-tips-list li {
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.security-tips-list li i {
    margin-right: 0.75rem;
    width: 16px;
}

/* Form Enhancements */
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

.form-control.is-invalid {
    border-color: #dc3545;
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
    .password-requirements {
        padding: 0.75rem;
    }
    
    .requirement {
        font-size: 0.85rem;
    }
    
    .security-tips-list li {
        font-size: 0.85rem;
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
        toggleButton.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleButton.className = 'fas fa-eye';
    }
}

document.addEventListener('DOMContentLoaded', function() {
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