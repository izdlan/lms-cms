@extends('layouts.admin')

@section('title', 'Diploma Program Management | Admin | Olympia Education')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            @include('admin.partials.sidebar')
            
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Diploma Program Management</h1>
                        <p class="text-muted">Manage Diploma level programs and their learning outcomes</p>
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

                <div class="row">
                    @forelse($programs as $program)
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
                                        <a href="{{ route('admin.diploma.plos', $program) }}" class="btn btn-outline-primary btn-sm">
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
        </div>
    </div>
</div>
@endsection
