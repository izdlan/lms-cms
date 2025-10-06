@extends('layouts.admin')

@section('title', 'Edit Lecturer')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>Edit Lecturer</h1>
                    <div class="header-actions">
                        <a href="{{ route('admin.lecturers') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" width="16" height="16"></i>
                            Back to Lecturers
                        </a>
                    </div>
                </div>

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Edit Lecturer Form -->
                <div class="card">
                    <div class="card-header">
                        <h5>Lecturer Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.lecturers.update', $lecturer) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Current Profile Picture Display -->
                            @if($lecturer->profile_picture)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label">Current Profile Picture</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset($lecturer->profile_picture) }}" alt="{{ $lecturer->name }}" 
                                                 class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                            <div>
                                                <p class="mb-1 text-muted">Current profile picture</p>
                                                <small class="text-muted">Upload a new image to replace this one</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="staff_id" class="form-label">Staff ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('staff_id') is-invalid @enderror" 
                                               id="staff_id" name="staff_id" value="{{ old('staff_id', $lecturer->staff_id) }}" required>
                                        @error('staff_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $lecturer->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $lecturer->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $lecturer->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                               id="department" name="department" value="{{ old('department', $lecturer->department) }}">
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="specialization" class="form-label">Specialization</label>
                                        <input type="text" class="form-control @error('specialization') is-invalid @enderror" 
                                               id="specialization" name="specialization" value="{{ old('specialization', $lecturer->specialization) }}">
                                        @error('specialization')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Biography</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                  id="bio" name="bio" rows="4" placeholder="Enter lecturer's biography...">{{ old('bio', $lecturer->bio) }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label">Profile Picture</label>
                                        <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                                               id="profile_picture" name="profile_picture" accept="image/*">
                                        <div class="form-text">Upload a new profile picture (JPEG, PNG, JPG, GIF - Max 2MB)</div>
                                        @error('profile_picture')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="is_active" class="form-label">Status</label>
                                        <select class="form-select @error('is_active') is-invalid @enderror" 
                                                id="is_active" name="is_active">
                                            <option value="1" {{ old('is_active', $lecturer->is_active) ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ !old('is_active', $lecturer->is_active) ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.lecturers') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i data-feather="save" width="16" height="16"></i>
                                            Update Lecturer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
