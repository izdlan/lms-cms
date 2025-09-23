@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="password-reset-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6">
                <div class="password-reset-image-section text-center">
                    <img src="https://lms.olympia-education.com/store/1/default_images/front_login.jpg" 
                         alt="Password Reset Image" 
                         class="img-fluid rounded password-reset-image">
                    <p class="mt-3 text-muted">Student Portal - Password Reset</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="password-reset-form-section">
                    <div class="password-reset-card">
                        <div class="password-reset-header text-center mb-4">
                            <h2 class="password-reset-title">Reset Your Password</h2>
                            <p class="password-reset-subtitle">Enter your email address to receive a password reset link</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i data-feather="check-circle" width="20" height="20"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('student.password.email') }}" class="password-reset-form">
                            @csrf
                            
                            <div class="form-group mb-4">
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

                            <button type="submit" class="btn btn-primary btn-password-reset w-100">
                                <i data-feather="mail" width="20" height="20"></i>
                                Send Reset Link
                            </button>

                            <div class="password-reset-divider">
                                <span>or</span>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('student.login') }}" class="back-to-login">
                                    ← Back to Student Login
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
.password-reset-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.password-reset-image {
    max-width: 100%;
    height: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
}

.password-reset-card {
    background: white;
    padding: 3rem;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    max-width: 450px;
    margin: 0 auto;
}

.password-reset-title {
    color: #0056d2;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.password-reset-subtitle {
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

.btn-password-reset {
    background: #0056d2;
    border: none;
    padding: 0.875rem 1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-password-reset:hover {
    background: #0041a3;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 86, 210, 0.4);
}

.password-reset-divider {
    text-align: center;
    margin: 1.5rem 0;
    position: relative;
}

.password-reset-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e9ecef;
}

.password-reset-divider span {
    background: white;
    padding: 0 1rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.back-to-login {
    color: #0056d2;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.back-to-login:hover {
    color: #0041a3;
    text-decoration: underline;
}

.alert {
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

@media (max-width: 991.98px) {
    .password-reset-image-section {
        margin-bottom: 2rem;
    }
    
    .password-reset-card {
        padding: 2rem;
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

