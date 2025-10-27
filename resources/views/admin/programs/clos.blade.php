@extends('layouts.admin')

@section('title', 'Course Learning Outcomes (CLOs) - ' . $program->name . ' | Admin | Olympia Education')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            @include('admin.partials.sidebar')

            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Course Learning Outcomes (CLOs) for {{ $program->name }}</h1>
                        <p class="text-muted">Manage Course Learning Outcomes for {{ $program->code }} program.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCloModal">
                            <i class="fas fa-plus"></i> Add CLO
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

                <!-- CLOs Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>Course Learning Outcomes</h5>
                    </div>
                    <div class="card-body">
                        @if($clos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Course Name</th>
                                        <th>CLO Code</th>
                                        <th>Description</th>
                                        <th>MQF Domain</th>
                                        <th>MQF Code</th>
                                        <th>Topics Covered</th>
                                        <th>Assessment Methods</th>
                                        <th>Sort Order</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clos as $clo)
                                    <tr>
                                        <td><strong>{{ $clo->course_name }}</strong></td>
                                        <td><span class="badge bg-primary">{{ $clo->clo_code }}</span></td>
                                        <td>{{ Str::limit($clo->description, 80) }}</td>
                                        <td>{{ $clo->mqf_domain }}</td>
                                        <td><span class="badge bg-secondary">{{ $clo->mqf_code }}</span></td>
                                        <td>
                                            @if($clo->topics_covered)
                                                @foreach(json_decode($clo->topics_covered) as $topic)
                                                    <span class="badge bg-light text-dark me-1">{{ $topic }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if($clo->assessment_methods)
                                                @foreach(json_decode($clo->assessment_methods) as $method)
                                                    <small class="d-block">{{ $method }}</small>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $clo->sort_order }}</td>
                                        <td>
                                            <span class="badge bg-{{ $clo->is_active ? 'success' : 'secondary' }}">
                                                {{ $clo->is_active ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editCloModal{{ $clo->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.programs.destroy-clo', $clo) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this CLO?')" title="Delete">
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
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Course Learning Outcomes Found</h4>
                            <p class="text-muted">Start by adding your first CLO for this program.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCloModal">
                                <i class="fas fa-plus"></i> Add First CLO
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add CLO Modal -->
<div class="modal fade" id="addCloModal" tabindex="-1" aria-labelledby="addCloModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCloModalLabel">Add New Course Learning Outcome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.programs.store-clo', $program) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="clo_code" class="form-label">CLO Code</label>
                            <input type="text" class="form-control" id="clo_code" name="clo_code" placeholder="e.g., CLO1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mqf_domain" class="form-label">MQF Domain</label>
                            <input type="text" class="form-control" id="mqf_domain" name="mqf_domain" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mqf_code" class="form-label">MQF Code</label>
                            <input type="text" class="form-control" id="mqf_code" name="mqf_code" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="topics_covered" class="form-label">Topics Covered (one per line)</label>
                        <textarea class="form-control" id="topics_covered" name="topics_covered" rows="3" placeholder="Enter each topic on a new line"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="assessment_methods" class="form-label">Assessment Methods (one per line)</label>
                        <textarea class="form-control" id="assessment_methods" name="assessment_methods" rows="3" placeholder="Enter each assessment method on a new line"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add CLO</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

