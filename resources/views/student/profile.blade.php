@extends('layouts.app')

@section('title', 'My Profile | Student | Olympia Education')

@section('content')
<div class="student-dashboard">
    <!-- Student Navigation Bar -->
    @include('student.partials.student-navbar')
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('student.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="dashboard-header">
                    <h1>My Profile</h1>
                    <p class="text-muted">View your complete student information and manage your profile picture.</p>
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

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Profile Picture Section -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-camera me-2"></i>Profile Picture</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="profile-preview mb-3">
                                    @if(auth('student')->user()->profile_picture)
                                        <img src="{{ asset('storage/' . auth('student')->user()->profile_picture) }}" 
                                             alt="Current Profile Picture" 
                                             class="current-profile-picture">
                                    @else
                                        <div class="no-profile-picture">
                                            <i class="fas fa-user-circle"></i>
                                            <p class="text-muted">No profile picture</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <form action="{{ route('student.profile.picture') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" 
                                               accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i>Upload Picture
                                    </button>
                                </form>
                                
                                @if(auth('student')->user()->profile_picture)
                                    <form action="{{ route('student.profile.picture.delete') }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete your profile picture?')">
                                            <i class="fas fa-trash me-1"></i>Remove Picture
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Student Information Display -->
                    <div class="col-md-8">
                        @php
                            $user = auth('student')->user();
                        @endphp
                        
                        <!-- Personal Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <p class="form-control-plaintext">{{ $user->name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <p class="form-control-plaintext">{{ $user->email ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <p class="form-control-plaintext">{{ $user->phone ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">IC Number</label>
                                        <p class="form-control-plaintext">{{ $user->ic ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <p class="form-control-plaintext">{{ $user->address ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-graduation-cap me-2"></i>Academic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Student ID</label>
                                        <p class="form-control-plaintext">{{ $user->student_id ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Programme Name</label>
                                        <p class="form-control-plaintext">{{ $user->programme_name ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Category</label>
                                        <p class="form-control-plaintext">{{ $user->category ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Faculty</label>
                                        <p class="form-control-plaintext">{{ $user->faculty ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Programme Code</label>
                                        <p class="form-control-plaintext">{{ $user->programme_code ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Semester Entry</label>
                                        <p class="form-control-plaintext">{{ $user->semester_entry ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Programme Intake</label>
                                        <p class="form-control-plaintext">{{ $user->programme_intake ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Date of Commencement</label>
                                        <p class="form-control-plaintext">{{ $user->date_of_commencement ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Research Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-microscope me-2"></i>Research Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Research Title</label>
                                    <p class="form-control-plaintext">{{ $user->research_title ?? '-' }}</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Supervisor</label>
                                        <p class="form-control-plaintext">{{ $user->supervisor ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">External Examiner</label>
                                        <p class="form-control-plaintext">{{ $user->external_examiner ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Internal Examiner</label>
                                        <p class="form-control-plaintext">{{ $user->internal_examiner ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Previous University</label>
                                        <p class="form-control-plaintext">{{ $user->previous_university ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">College Reference Number</label>
                                        <p class="form-control-plaintext">{{ $user->col_ref_no ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Source Sheet</label>
                                        <p class="form-control-plaintext">{{ $user->source_sheet ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">College Date</label>
                                        <p class="form-control-plaintext">{{ $user->col_date ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Student Portal Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-key me-2"></i>Student Portal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Portal Username</label>
                                        <p class="form-control-plaintext">{{ $user->student_portal_username ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Portal Password</label>
                                        <p class="form-control-plaintext">{{ $user->student_portal_password ? '••••••••' : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Courses Information -->
                        @if($user->courses && count($user->courses) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-book me-2"></i>Enrolled Courses</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($user->courses as $index => $course)
                                        <div class="col-md-6 mb-2">
                                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                            {{ $course }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.current-profile-picture {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #20c997;
}

.no-profile-picture {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 3px solid #dee2e6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: #6c757d;
}

.no-profile-picture i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.profile-preview {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
    margin-bottom: 1.5rem;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
    display: flex;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    color: #2d3748;
    font-weight: bold;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:read-only {
    background-color: #f8f9fa;
    border-color: #e9ecef;
}

.form-control-plaintext {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0;
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

.form-label.fw-bold {
    color: #495057;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 2px solid #dee2e6;
}

.card-header h5 {
    color: #495057;
    font-weight: 600;
}

.badge.bg-primary {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.querySelector('.profile-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="current-profile-picture">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize any additional functionality
});
</script>
@endpush