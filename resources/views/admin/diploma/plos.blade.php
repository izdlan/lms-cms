@extends('layouts.admin')

@section('title', 'Diploma PLO Management - ' . $program->name . ' | Admin | Olympia Education')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            @include('admin.partials.sidebar')
            
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Diploma Program Learning Outcomes (PLOs)</h1>
                        <p class="text-muted">{{ $program->code }} - {{ $program->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.diploma.index') }}" class="btn btn-secondary">
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

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Add PLO Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle text-primary"></i> Add New Diploma PLO
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.diploma.store-plo', $program) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="plo_code" class="form-label">PLO Code</label>
                                        <input type="text" class="form-control" id="plo_code" name="plo_code" 
                                               placeholder="PLO1" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="mqf_domain" class="form-label">MQF Domain</label>
                                        <select class="form-select" id="mqf_domain" name="mqf_domain" required>
                                            <option value="">Select Domain</option>
                                            <option value="C1: Knowledge & Understanding">C1: Knowledge & Understanding</option>
                                            <option value="C2: Practical Skills">C2: Practical Skills</option>
                                            <option value="C3: Thinking Skills">C3: Thinking Skills</option>
                                            <option value="C4: Communication Skills">C4: Communication Skills</option>
                                            <option value="C5: Social Skills & Responsibility">C5: Social Skills & Responsibility</option>
                                            <option value="C6: Values, Attitudes & Professionalism">C6: Values, Attitudes & Professionalism</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="mqf_code" class="form-label">MQF Code</label>
                                        <input type="text" class="form-control" id="mqf_code" name="mqf_code" 
                                               placeholder="C1" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                               value="0" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block w-100">
                                            <i class="fas fa-plus"></i> Add PLO
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="2" 
                                                  placeholder="Describe the learning outcome..." required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="mapped_courses" class="form-label">Mapped Courses</label>
                                        <textarea class="form-control" id="mapped_courses" name="mapped_courses" rows="2" 
                                                  placeholder="Course 1, Course 2, Course 3..." required></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="assessment_methods" class="form-label">Assessment Methods</label>
                                        <textarea class="form-control" id="assessment_methods" name="assessment_methods" rows="2" 
                                                  placeholder="Exams, Assignments, Projects..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="practical_skills" class="form-label">Practical Skills</label>
                                        <textarea class="form-control" id="practical_skills" name="practical_skills" rows="2" 
                                                  placeholder="Hands-on skills specific to diploma level..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="industry_requirements" class="form-label">Industry Requirements</label>
                                        <textarea class="form-control" id="industry_requirements" name="industry_requirements" rows="2" 
                                                  placeholder="Industry-specific requirements and standards..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Document Upload -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-upload text-info"></i> Extract PLOs from Document
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.diploma.extract', $program) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="document" class="form-label">Upload Document (Word/PDF)</label>
                                        <input type="file" class="form-control" id="document" name="document" 
                                               accept=".doc,.docx,.pdf" required>
                                        <div class="form-text">Supported formats: .doc, .docx, .pdf (Max: 10MB)</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-info d-block w-100">
                                            <i class="fas fa-magic"></i> Extract PLOs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing PLOs -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list text-success"></i> Existing Diploma PLOs ({{ $plos->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($plos as $plo)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge bg-primary me-2">{{ $plo->plo_code }}</span>
                                            {{ $plo->mqf_domain }} ({{ $plo->mqf_code }})
                                        </h6>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" 
                                                data-bs-target="#editPloModal{{ $plo->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.diploma.destroy-plo', $plo) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this PLO?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mb-2">{{ $plo->description }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Courses:</strong> {{ $plo->mapped_courses }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Assessment:</strong> {{ $plo->assessment_methods ?? 'Not specified' }}
                                        </small>
                                    </div>
                                </div>
                                @if($plo->practical_skills)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Practical Skills:</strong> {{ $plo->practical_skills }}
                                        </small>
                                    </div>
                                @endif
                                @if($plo->industry_requirements)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Industry Requirements:</strong> {{ $plo->industry_requirements }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-graduation-cap fa-2x text-muted mb-3"></i>
                                <h6 class="text-muted">No Diploma PLOs found</h6>
                                <p class="text-muted">Add your first PLO using the form above.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
