@extends('layouts.finance-admin')

@section('title', 'Change Password | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="page-title">Change Password</h1>
                                <p class="page-subtitle">Update your account password</p>
                            </div>
                            <div>
                                <a href="{{ route('finance-admin.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-key me-2"></i>Change Password</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('finance-admin.password.update') }}">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                   id="current_password" name="current_password" required>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required minlength="8">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Password must be at least 8 characters long.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation" required>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Update Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6><i class="fas fa-info-circle me-2"></i>Password Requirements</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-check text-success me-2"></i>At least 8 characters long</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Use a combination of letters and numbers</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Avoid using common words or personal information</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Consider using special characters for added security</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
</div>
@endsection

@push('styles')
<style>
.finance-admin-dashboard {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.card-header h5, .card-header h6 {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    color: #333;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #666;
    margin: 0;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>
@endpush
