@extends('layouts.staff-course')

@section('title', 'My Profile | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>My Profile</h1>
    <p>Manage your personal information and account settings</p>
</div>

<div class="content-card">
    <h5><i class="fas fa-user"></i> Profile Information</h5>
    
    <!-- Profile Picture Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="profile-picture-section">
                <div class="current-picture">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Current Profile Picture" class="profile-picture-large">
                    @else
                        <div class="profile-picture-placeholder-large">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                <div class="picture-actions">
                    <button type="button" class="action-btn primary" onclick="document.getElementById('profile_picture').click()">
                        <i class="fas fa-camera"></i>
                        Change Picture
                    </button>
                    @if(auth()->user()->profile_picture)
                        <button type="button" class="action-btn danger" onclick="removeProfilePicture()">
                            <i class="fas fa-trash"></i>
                            Remove Picture
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <form method="POST" action="{{ route('staff.profile.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;" onchange="previewProfilePicture(this)">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ic" class="form-label">IC Number</label>
                    <input type="text" class="form-control" id="ic" name="ic" value="{{ $user->ic }}" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <input type="text" class="form-control" id="role" value="{{ ucfirst($user->role) }}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="created_at" class="form-label">Member Since</label>
                    <input type="text" class="form-control" id="created_at" value="{{ $user->created_at->format('M d, Y') }}" readonly>
                </div>
            </div>
        </div>
        <div class="action-buttons">
            <button type="submit" class="action-btn primary">
                <i class="fas fa-save"></i>
                Update Profile
            </button>
            <a href="{{ route('staff.password.change') }}" class="action-btn warning">
                <i class="fas fa-key"></i>
                Change Password
            </a>
        </div>
    </form>
</div>


@push('styles')
<style>
.profile-picture-section {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 15px;
    margin-bottom: 2rem;
}

.current-picture {
    margin-bottom: 1.5rem;
}

.profile-picture-large {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #0056d2;
    box-shadow: 0 4px 15px rgba(0, 86, 210, 0.2);
}

.profile-picture-placeholder-large {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0056d2, #0041a3);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    border: 4px solid #0056d2;
    box-shadow: 0 4px 15px rgba(0, 86, 210, 0.2);
}

.profile-picture-placeholder-large i {
    font-size: 4rem;
    color: white;
}

.picture-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.picture-actions .action-btn {
    min-width: 150px;
}

.preview-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #28a745;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
}

.upload-info {
    background: #e9f5ff;
    border-left: 4px solid #0056d2;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #495057;
}

.upload-info i {
    color: #0056d2;
    margin-right: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function previewProfilePicture(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const currentPicture = document.querySelector('.current-picture');
            currentPicture.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="preview-image">
                <div class="upload-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Preview:</strong> Click "Update Profile" to save this picture.
                </div>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeProfilePicture() {
    if (confirm('Are you sure you want to remove your profile picture?')) {
        // Create a hidden input to indicate removal
        const form = document.querySelector('form');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'remove_profile_picture';
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);
        
        // Submit the form
        form.submit();
    }
}
</script>
@endpush

@endsection
