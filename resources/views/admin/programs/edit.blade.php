@extends('layouts.admin')

@section('title', 'Edit Program | Admin | Olympia Education')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            @include('admin.partials.sidebar')
            
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Edit Program</h1>
                        <p class="text-muted">{{ $program->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Program
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5>Program Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.programs.update', $program) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Program Code</label>
                                        <input type="text" class="form-control" id="code" name="code" 
                                               value="{{ old('code', $program->code) }}" required>
                                        <small class="form-text text-muted">Unique identifier for the program</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="level" class="form-label">Program Level</label>
                                        <select class="form-control" id="level" name="level" required>
                                            <option value="">Select Level</option>
                                            <option value="certificate" {{ old('level', $program->level) == 'certificate' ? 'selected' : '' }}>Certificate</option>
                                            <option value="diploma" {{ old('level', $program->level) == 'diploma' ? 'selected' : '' }}>Diploma</option>
                                            <option value="bachelor" {{ old('level', $program->level) == 'bachelor' ? 'selected' : '' }}>Bachelor</option>
                                            <option value="master" {{ old('level', $program->level) == 'master' ? 'selected' : '' }}>Master</option>
                                            <option value="phd" {{ old('level', $program->level) == 'phd' ? 'selected' : '' }}>PhD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Program Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $program->name) }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Program Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $program->description) }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="duration_months" class="form-label">Duration (Months)</label>
                                        <input type="number" class="form-control" id="duration_months" name="duration_months" 
                                               min="1" value="{{ old('duration_months', $program->duration_months) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                                   {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active Program
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Program
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
