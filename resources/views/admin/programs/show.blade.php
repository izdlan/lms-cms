@extends('layouts.admin')

@section('title', $program->name . ' | Admin | Olympia Education')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            @include('admin.partials.sidebar')
            
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>{{ $program->name }}</h1>
                        <p class="text-muted">{{ $program->code }} - {{ ucfirst($program->level) }} Program</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Program
                        </a>
                        <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Programs
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Program Overview -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Program Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Program Code:</strong> {{ $program->code }}</p>
                                        <p><strong>Level:</strong> {{ ucfirst($program->level) }}</p>
                                        <p><strong>Duration:</strong> {{ $program->duration_months }} months</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Status:</strong> 
                                            <span class="badge bg-{{ $program->is_active ? 'success' : 'secondary' }}">
                                                {{ $program->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                        <p><strong>Created:</strong> {{ $program->created_at->format('M d, Y') }}</p>
                                        <p><strong>Updated:</strong> {{ $program->updated_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                
                                @if($program->description)
                                <div class="mt-3">
                                    <h6>Description:</h6>
                                    <p>{{ $program->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Statistics</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="row">
                                    <div class="col-6">
                                        <h3 class="text-primary">{{ $program->programLearningOutcomes->count() }}</h3>
                                        <small class="text-muted">PLOs</small>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="text-info">{{ $program->courseLearningOutcomes->count() }}</h3>
                                        <small class="text-muted">CLOs</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <h3 class="text-success">{{ $program->programSubjects->count() }}</h3>
                                        <small class="text-muted">Subjects</small>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="text-warning">{{ $program->programSubjects->sum('credit_hours') }}</h3>
                                        <small class="text-muted">Credits</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Management Actions -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-list fa-3x text-primary mb-3"></i>
                                <h5>Program Learning Outcomes</h5>
                                <p class="text-muted">Manage PLOs for this program</p>
                                <a href="{{ route('admin.programs.plos', $program) }}" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Manage PLOs
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-book fa-3x text-info mb-3"></i>
                                <h5>Course Learning Outcomes</h5>
                                <p class="text-muted">Manage CLOs for courses</p>
                                <a href="{{ route('admin.programs.clos', $program) }}" class="btn btn-info">
                                    <i class="fas fa-cog"></i> Manage CLOs
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                                <h5>Program Subjects</h5>
                                <p class="text-muted">Manage subjects and curriculum</p>
                                <a href="{{ route('admin.programs.subjects', $program) }}" class="btn btn-success">
                                    <i class="fas fa-cog"></i> Manage Subjects
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent PLOs -->
                @if($program->programLearningOutcomes->count() > 0)
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Recent Program Learning Outcomes</h5>
                        <a href="{{ route('admin.programs.plos', $program) }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>PLO Code</th>
                                        <th>Description</th>
                                        <th>MQF Domain</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($program->programLearningOutcomes->take(5) as $plo)
                                    <tr>
                                        <td><strong>{{ $plo->plo_code }}</strong></td>
                                        <td>{{ Str::limit($plo->description, 60) }}</td>
                                        <td><span class="badge bg-secondary">{{ $plo->mqf_code }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
