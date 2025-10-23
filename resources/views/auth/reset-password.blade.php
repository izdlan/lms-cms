@extends('layouts.app')

@section('title', 'Reset Password | Olympia Education')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fab fa-whatsapp text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="card-title text-primary">Reset Your Password</h2>
                        <p class="text-muted">Enter the 6-digit code sent to your WhatsApp and your new password</p>
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

                    <form method="POST" action="{{ route('reset-password.post') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="mb-4">
                            <label for="reset_code" class="form-label">
                                <i class="fas fa-key me-2"></i>Reset Code
                            </label>
                            <input type="text" 
                                   class="form-control @error('reset_code') is-invalid @enderror" 
                                   id="reset_code" 
                                   name="reset_code" 
                                   value="{{ old('reset_code') }}" 
                                   required 
                                   autofocus
                                   maxlength="6"
                                   placeholder="Enter 6-digit code"
                                   style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Check your WhatsApp for the 6-digit code
                            </div>
                            @error('reset_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>New Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter your new password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-2"></i>Confirm New Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   placeholder="Confirm your new password">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>
                                Reset Password
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Didn't receive the code? 
                            <a href="{{ route('forgot-password') }}" class="text-primary text-decoration-none">
                                <i class="fas fa-redo me-1"></i>Try Again
                            </a>
                        </p>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-info-circle me-2"></i>Password Requirements
                        </h6>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li><i class="fas fa-check text-success me-2"></i>At least 8 characters long</li>
                            <li><i class="fas fa-check text-success me-2"></i>Include uppercase and lowercase letters</li>
                            <li><i class="fas fa-check text-success me-2"></i>Include at least one number</li>
                            <li><i class="fas fa-check text-success me-2"></i>Include at least one special character</li>
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

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.alert {
    border-radius: 10px;
}

.bg-light {
    background-color: #f8f9fa !important;
    border-radius: 10px;
}

#reset_code {
    font-family: 'Courier New', monospace;
}
</style>

<script>
// Auto-format reset code input
document.getElementById('reset_code').addEventListener('input', function(e) {
    // Remove any non-digit characters
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Limit to 6 digits
    if (this.value.length > 6) {
        this.value = this.value.slice(0, 6);
    }
});

// Auto-submit when 6 digits are entered
document.getElementById('reset_code').addEventListener('input', function(e) {
    if (this.value.length === 6) {
        // Focus on password field
        document.getElementById('password').focus();
    }
});
</script>
@endsection
