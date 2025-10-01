@extends('layouts.staff-course')

@section('title', 'Change Password | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>Change Password</h1>
    <p>Update your account password for security</p>
</div>

<div class="content-card">
    <h5><i class="fas fa-key"></i> Password Change</h5>
    <form method="POST" action="{{ route('staff.password.update') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>
        </div>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Password Requirements:</strong>
            <ul class="mb-0 mt-2">
                <li>At least 8 characters long</li>
                <li>Must contain at least one uppercase letter</li>
                <li>Must contain at least one lowercase letter</li>
                <li>Must contain at least one number</li>
            </ul>
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


@endsection
