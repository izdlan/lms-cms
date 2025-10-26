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
                        <p class="text-muted">Manage programs and learning outcomes by academic level</p>
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

                <!-- Academic Level Tabs -->
                <ul class="nav nav-tabs mb-4" id="academicLevelTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="diploma-tab" data-bs-toggle="tab" data-bs-target="#diploma" type="button" role="tab">
                            <i class="bi bi-mortarboard"></i> Diploma Programs ({{ $diplomaPrograms->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="degree-tab" data-bs-toggle="tab" data-bs-target="#degree" type="button" role="tab">
                            <i class="bi bi-graduation-cap"></i> Degree Programs ({{ $degreePrograms->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="master-tab" data-bs-toggle="tab" data-bs-target="#master" type="button" role="tab">
                            <i class="bi bi-award"></i> Master Programs ({{ $masterPrograms->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="phd-tab" data-bs-toggle="tab" data-bs-target="#phd" type="button" role="tab">
                            <i class="bi bi-trophy"></i> PhD Programs ({{ $phdPrograms->count() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="academicLevelTabsContent">
                    <!-- Diploma Programs Tab -->
                    <div class="tab-pane fade show active" id="diploma" role="tabpanel">
                        <div class="row">
                            @forelse($diplomaPrograms as $program)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title">{{ $program->code }}</h5>
                                                <span class="badge bg-primary">{{ $program->level }}</span>
                                            </div>
                                            <h6 class="card-subtitle mb-2 text-muted">{{ $program->name }}</h6>
                                            <p class="card-text">{{ Str::limit($program->description, 100) }}</p>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-primary">{{ $program->plo_count }}</h6>
                                                        <small class="text-muted">PLOs</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-success">{{ $program->duration_months }}</h6>
                                                        <small class="text-muted">Months</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="mb-0 text-info">{{ $program->is_active ? 'Active' : 'Inactive' }}</h6>
                                                    <small class="text-muted">Status</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('admin.programs.diploma-plos', $program) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-graduation-cap"></i> Manage PLOs
                                                </a>
                                                <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-mortarboard fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">No Diploma Programs Found</h4>
                                        <p class="text-muted">Start by creating your first diploma program.</p>
                                        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Create Diploma Program
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Degree Programs Tab -->
                    <div class="tab-pane fade" id="degree" role="tabpanel">
                        <div class="row">
                            @forelse($degreePrograms as $program)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title">{{ $program->code }}</h5>
                                                <span class="badge bg-success">{{ $program->level }}</span>
                                            </div>
                                            <h6 class="card-subtitle mb-2 text-muted">{{ $program->name }}</h6>
                                            <p class="card-text">{{ Str::limit($program->description, 100) }}</p>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-primary">{{ $program->plo_count }}</h6>
                                                        <small class="text-muted">PLOs</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-success">{{ $program->duration_months }}</h6>
                                                        <small class="text-muted">Months</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="mb-0 text-info">{{ $program->is_active ? 'Active' : 'Inactive' }}</h6>
                                                    <small class="text-muted">Status</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('admin.programs.degree-plos', $program) }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-graduation-cap"></i> Manage PLOs
                                                </a>
                                                <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">No Degree Programs Found</h4>
                                        <p class="text-muted">Start by creating your first degree program.</p>
                                        <a href="{{ route('admin.programs.create') }}" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Create Degree Program
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Master Programs Tab -->
                    <div class="tab-pane fade" id="master" role="tabpanel">
                        <div class="row">
                            @forelse($masterPrograms as $program)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title">{{ $program->code }}</h5>
                                                <span class="badge bg-warning">{{ $program->level }}</span>
                                            </div>
                                            <h6 class="card-subtitle mb-2 text-muted">{{ $program->name }}</h6>
                                            <p class="card-text">{{ Str::limit($program->description, 100) }}</p>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-primary">{{ $program->plo_count }}</h6>
                                                        <small class="text-muted">PLOs</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-success">{{ $program->duration_months }}</h6>
                                                        <small class="text-muted">Months</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="mb-0 text-info">{{ $program->is_active ? 'Active' : 'Inactive' }}</h6>
                                                    <small class="text-muted">Status</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('admin.programs.master-plos', $program) }}" class="btn btn-outline-warning btn-sm">
                                                    <i class="fas fa-award"></i> Manage PLOs
                                                </a>
                                                <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-award fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">No Master Programs Found</h4>
                                        <p class="text-muted">Start by creating your first master program.</p>
                                        <a href="{{ route('admin.programs.create') }}" class="btn btn-warning">
                                            <i class="fas fa-plus"></i> Create Master Program
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- PhD Programs Tab -->
                    <div class="tab-pane fade" id="phd" role="tabpanel">
                        <div class="row">
                            @forelse($phdPrograms as $program)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title">{{ $program->code }}</h5>
                                                <span class="badge bg-danger">{{ $program->level }}</span>
                                            </div>
                                            <h6 class="card-subtitle mb-2 text-muted">{{ $program->name }}</h6>
                                            <p class="card-text">{{ Str::limit($program->description, 100) }}</p>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-primary">{{ $program->plo_count }}</h6>
                                                        <small class="text-muted">PLOs</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0 text-success">{{ $program->duration_months }}</h6>
                                                        <small class="text-muted">Months</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="mb-0 text-info">{{ $program->is_active ? 'Active' : 'Inactive' }}</h6>
                                                    <small class="text-muted">Status</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('admin.programs.phd-plos', $program) }}" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trophy"></i> Manage PLOs
                                                </a>
                                                <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">No PhD Programs Found</h4>
                                        <p class="text-muted">Start by creating your first PhD program.</p>
                                        <a href="{{ route('admin.programs.create') }}" class="btn btn-danger">
                                            <i class="fas fa-plus"></i> Create PhD Program
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection