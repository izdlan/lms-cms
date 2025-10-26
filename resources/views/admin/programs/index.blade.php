@extends('layouts.admin')

@section('title', 'Program Management | Admin | Olympia Education')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            @include('admin.partials.sidebar')
            
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Program Management</h1>
                        <p class="text-muted">Manage programs, PLOs, CLOs, and subjects</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Program
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    @foreach($programs as $program)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $program->code }}</h5>
                                <span class="badge bg-{{ $program->is_active ? 'success' : 'secondary' }}">
                                    {{ $program->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">{{ $program->name }}</h6>
                                <p class="card-text">{{ Str::limit($program->description, 100) }}</p>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <small class="text-muted">Level</small>
                                        <div class="fw-bold">{{ ucfirst($program->level) }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Duration</small>
                                        <div class="fw-bold">{{ $program->duration_months }} months</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">PLOs</small>
                                        <div class="fw-bold">{{ $program->programLearningOutcomes->count() }}</div>
                                    </div>
                                </div>

                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <small class="text-muted">CLOs</small>
                                        <div class="fw-bold">{{ $program->courseLearningOutcomes->count() }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Subjects</small>
                                        <div class="fw-bold">{{ $program->programSubjects->count() }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Credits</small>
                                        <div class="fw-bold">{{ $program->programSubjects->sum('credit_hours') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.programs.plos', $program) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-list"></i> PLOs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($programs->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Programs Found</h4>
                    <p class="text-muted">Start by creating your first program.</p>
                    <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Program
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
