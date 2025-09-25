@extends('layouts.staff')

@section('title', 'Staff Profile')

@section('content')
<div class="dashboard-header">
    <h1>My Profile</h1>
    <p class="text-muted">Manage your personal information</p>
</div>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Personal Information</h5>
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

                        <form method="POST" action="{{ route('staff.profile.update') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ ucfirst($user->role) }}" 
                                           readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="created_at" class="form-label">Member Since</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $user->created_at->format('M d, Y') }}" 
                                           readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="updated_at" class="form-label">Last Updated</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $user->updated_at->format('M d, Y H:i') }}" 
                                           readonly>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Profile Picture and Quick Actions -->
            <div class="col-md-4">
                <!-- Profile Picture -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Profile Picture</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="profile-picture-container">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                     alt="{{ $user->name }}" 
                                     class="profile-picture">
                            @else
                                <div class="profile-picture-placeholder">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                            @endif
                        </div>
                        <p class="profile-name">{{ $user->name }}</p>
                        <p class="profile-role">{{ ucfirst($user->role) }}</p>
                        <button class="btn btn-outline-primary btn-sm" disabled>
                            <i class="fas fa-camera"></i> Change Picture
                        </button>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="{{ route('staff.password.change') }}" class="quick-action-btn">
                                <i class="fas fa-key"></i>
                                <span>Change Password</span>
                            </a>
                            <a href="{{ route('staff.students') }}" class="quick-action-btn">
                                <i class="fas fa-users"></i>
                                <span>View Students</span>
                            </a>
                            <a href="{{ route('staff.dashboard') }}" class="quick-action-btn">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
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
.staff-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
}

.main-content {
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
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

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #20c997;
    box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25);
}

.form-control[readonly] {
    background: #f8f9fa;
    color: #6c757d;
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

.profile-picture-container {
    margin-bottom: 20px;
}

.profile-picture {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #20c997;
}

.profile-picture-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: #6c757d;
    border: 4px solid #20c997;
}

.profile-name {
    font-size: 1.2rem;
    font-weight: bold;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
}

.profile-role {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0 0 20px 0;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #2d3748;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-action-btn:hover {
    background: #20c997;
    color: white;
    text-decoration: none;
    transform: translateX(5px);
}

.quick-action-btn i {
    margin-right: 12px;
    font-size: 1.1rem;
    width: 20px;
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
