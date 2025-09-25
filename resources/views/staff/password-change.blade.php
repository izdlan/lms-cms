@extends('layouts.staff')

@section('title', 'Change Password')

@section('content')
<div class="dashboard-header">
    <h1>Change Password</h1>
    <p class="text-muted">Update your account password</p>
</div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Password Change</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('staff.password.update') }}">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="password-input-group">
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password" 
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password" class="form-label">New Password</label>
                                <div class="password-input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Password must be at least 8 characters long.
                                </small>
                            </div>

                            <div class="form-group mb-4">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="password-input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Password
                                </button>
                                <a href="{{ route('staff.profile') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Profile
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Tips -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Password Security Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="security-tips">
                            <li><i class="fas fa-check text-success"></i> Use a combination of uppercase and lowercase letters</li>
                            <li><i class="fas fa-check text-success"></i> Include numbers and special characters</li>
                            <li><i class="fas fa-check text-success"></i> Avoid using personal information</li>
                            <li><i class="fas fa-check text-success"></i> Don't reuse passwords from other accounts</li>
                            <li><i class="fas fa-check text-success"></i> Consider using a password manager</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.staff-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
}

.main-content {
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.page-title {
    color: #2d3748;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    margin: 0;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: #20c997;
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
    border: none;
}

.card-title {
    margin: 0;
    font-weight: 600;
    font-size: 1.2rem;
}

.card-body {
    padding: 25px;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.password-input-group {
    position: relative;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    padding-right: 50px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #20c997;
    box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25);
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
    font-size: 1rem;
}

.password-toggle:hover {
    color: #20c997;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 15px;
}

.btn-primary {
    background: #20c997;
    border-color: #20c997;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
}

.btn-primary:hover {
    background: #1a9f7a;
    border-color: #1a9f7a;
}

.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
}

.btn-secondary:hover {
    background: #5a6268;
    border-color: #5a6268;
}

.security-tips {
    list-style: none;
    padding: 0;
    margin: 0;
}

.security-tips li {
    padding: 8px 0;
    display: flex;
    align-items: center;
}

.security-tips i {
    margin-right: 12px;
    font-size: 0.9rem;
}

.alert {
    border: none;
    border-radius: 8px;
    padding: 15px 20px;
}

.alert-success {
    background: #d1edff;
    color: #0c5460;
    border-left: 4px solid #20c997;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

@media (max-width: 768px) {
    .main-content {
        padding: 15px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleButton = document.querySelector(`#${fieldId} + .password-toggle i`);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
}
</script>
@endpush
