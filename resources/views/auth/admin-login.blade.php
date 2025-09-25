@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="login-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6">
                <div class="login-image-section text-center">
                    <img src="https://lms.olympia-education.com/store/1/default_images/front_login.jpg" 
                         alt="Admin Login Image" 
                         class="img-fluid rounded login-image">
                    <p class="mt-3 text-muted">Admin Portal</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login-form-section">
                    <div class="login-card">
                        <div class="login-header text-center mb-4">
                            <h2 class="login-title">Admin Login</h2>
                            <p class="login-subtitle">Enter your email and password to continue</p>
                        </div>

                        <form method="POST" action="{{ route('admin.login') }}" class="login-form">
                            @csrf
                            
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
                                Login as Admin
                            </button>

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

{{-- Inline styles moved to /assets/default/css/auth.css --}}

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
