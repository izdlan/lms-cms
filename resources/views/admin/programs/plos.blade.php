@extends('layouts.admin')

@section('title', 'PLO Management - ' . $program->name . ' | Admin | Olympia Education')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            @include('admin.partials.sidebar')
            
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Program Learning Outcomes (PLOs)</h1>
                        <p class="text-muted">{{ $program->name }} ({{ $program->code }})</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPloModal">
                            <i class="fas fa-plus"></i> Add PLO
                        </button>
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

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Document Upload Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-file-upload"></i> Extract PLOs from Document</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.programs.extract', $program) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="file" class="form-control" name="document" accept=".docx,.doc,.pdf" required>
                                    <small class="form-text text-muted">Upload Word or PDF document containing PLOs/CLOs</small>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Extract PLOs/CLOs
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- PLOs Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>Program Learning Outcomes</h5>
                    </div>
                    <div class="card-body">
                        @if($plos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>PLO Code</th>
                                        <th>Description</th>
                                        <th>MQF Domain</th>
                                        <th>MQF Code</th>
                                        <th>Mapped Courses</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plos as $plo)
                                    <tr>
                                        <td><strong>{{ $plo->plo_code }}</strong></td>
                                        <td>{{ $plo->description }}</td>
                                        <td>{{ $plo->mqf_domain }}</td>
                                        <td><span class="badge bg-info">{{ $plo->mqf_code }}</span></td>
                                        <td>{{ $plo->mapped_courses }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#editPloModal{{ $plo->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.programs.destroy-plo', $plo) }}" method="POST" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this PLO?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No PLOs Found</h4>
                            <p class="text-muted">Add PLOs for this program or upload a document to extract them.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add PLO Modal -->
<div class="modal fade" id="addPloModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New PLO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.programs.store-plo', $program) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="plo_code" class="form-label">PLO Code</label>
                                <input type="text" class="form-control" id="plo_code" name="plo_code" 
                                       placeholder="PLO1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       value="{{ $plos->count() + 1 }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mqf_domain" class="form-label">MQF Domain</label>
                                <input type="text" class="form-control" id="mqf_domain" name="mqf_domain" 
                                       placeholder="C1: Knowledge & Understanding" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mqf_code" class="form-label">MQF Code</label>
                                <input type="text" class="form-control" id="mqf_code" name="mqf_code" 
                                       placeholder="C1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mapped_courses" class="form-label">Mapped Courses</label>
                        <input type="text" class="form-control" id="mapped_courses" name="mapped_courses" 
                               placeholder="Strategic Management, Strategic HRM, Strategic Marketing" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add PLO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit PLO Modals -->
@foreach($plos as $plo)
<div class="modal fade" id="editPloModal{{ $plo->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit PLO - {{ $plo->plo_code }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.programs.update-plo', $plo) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="plo_code{{ $plo->id }}" class="form-label">PLO Code</label>
                                <input type="text" class="form-control" id="plo_code{{ $plo->id }}" name="plo_code" 
                                       value="{{ $plo->plo_code }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order{{ $plo->id }}" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order{{ $plo->id }}" name="sort_order" 
                                       value="{{ $plo->sort_order }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description{{ $plo->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="description{{ $plo->id }}" name="description" rows="3" required>{{ $plo->description }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mqf_domain{{ $plo->id }}" class="form-label">MQF Domain</label>
                                <input type="text" class="form-control" id="mqf_domain{{ $plo->id }}" name="mqf_domain" 
                                       value="{{ $plo->mqf_domain }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mqf_code{{ $plo->id }}" class="form-label">MQF Code</label>
                                <input type="text" class="form-control" id="mqf_code{{ $plo->id }}" name="mqf_code" 
                                       value="{{ $plo->mqf_code }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mapped_courses{{ $plo->id }}" class="form-label">Mapped Courses</label>
                        <input type="text" class="form-control" id="mapped_courses{{ $plo->id }}" name="mapped_courses" 
                               value="{{ $plo->mapped_courses }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update PLO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
