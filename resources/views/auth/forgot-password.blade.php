@extends('layouts.app')

@section('title', 'Forgot Password | Olympia Education')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="card-title text-primary">Forgot Password?</h2>
                        <p class="text-muted">Enter your email address to receive a reset code</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('forgot-password.send') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   placeholder="Enter your email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <strong>Email Reset</strong><br>
                                <small>We'll send the reset code to your registered email address.</small>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send Reset Code
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Remember your password? 
                            <a href="{{ route('login') }}" class="text-primary text-decoration-none">
                                <i class="fas fa-sign-in-alt me-1"></i>Back to Login
                            </a>
                        </p>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-shield-alt me-2"></i>Security Information
                        </h6>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li><i class="fas fa-check text-success me-2"></i>Reset code expires in 15 minutes</li>
                            <li><i class="fas fa-check text-success me-2"></i>Code will be sent to your registered email address</li>
                            <li><i class="fas fa-check text-success me-2"></i>Only the account owner can receive the code</li>
                            <li><i class="fas fa-check text-success me-2"></i>Check your spam folder if you don't receive the email</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert {
    border-radius: 10px;
}

.bg-light {
    background-color: #f8f9fa !important;
    border-radius: 10px;
}
</style>
@endsection
