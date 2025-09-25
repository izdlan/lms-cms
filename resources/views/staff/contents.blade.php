@extends('layouts.staff')

@section('title', 'Course Contents')

@section('content')
<div class="dashboard-header">
    <h1>Course Contents</h1>
    <p class="text-muted">Upload and manage course materials for students</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary">
        <i class="fas fa-upload"></i> Upload Content
    </button>
</div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Search contents...">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">All Courses</option>
                <option value="cs101">CS101 - Introduction to Programming</option>
                <option value="cs201">CS201 - Web Development</option>
                <option value="cs301">CS301 - Database Management</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">All Types</option>
                <option value="pdf">PDF</option>
                <option value="video">Video</option>
                <option value="document">Document</option>
            </select>
        </div>
    </div>

    <!-- Contents Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Course Materials</h5>
                </div>
                <div class="card-body">
                    @if(count($contents) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Uploaded</th>
                                        <th>Downloads</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contents as $content)
                                    <tr>
                                        <td>
                                            <div class="content-title">
                                                <i class="fas fa-file-{{ strtolower($content['type']) === 'pdf' ? 'pdf' : (strtolower($content['type']) === 'video' ? 'video' : 'alt') }} text-primary"></i>
                                                <span>{{ $content['title'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="course-badge">{{ $content['course'] }}</span>
                                        </td>
                                        <td>
                                            <span class="type-badge {{ strtolower($content['type']) }}">
                                                {{ $content['type'] }}
                                            </span>
                                        </td>
                                        <td>{{ $content['file_size'] }}</td>
                                        <td>{{ $content['uploaded_at'] }}</td>
                                        <td>
                                            <span class="download-count">{{ $content['downloads'] }}</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No content uploaded yet</h5>
                            <p class="text-muted">Upload course materials for students to access</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count($contents) }}</div>
                    <div class="stat-label">Total Files</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-download"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ array_sum(array_column($contents, 'downloads')) }}</div>
                    <div class="stat-label">Total Downloads</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count(array_filter($contents, fn($c) => strtolower($c['type']) === 'pdf')) }}</div>
                    <div class="stat-label">PDF Files</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-video"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count(array_filter($contents, fn($c) => strtolower($c['type']) === 'video')) }}</div>
                    <div class="stat-label">Video Files</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.staff-contents {
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-title {
    color: #2d3748;
    font-size: 1.8rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.search-box .form-control {
    padding-left: 45px;
    border-radius: 25px;
    border: 2px solid #e2e8f0;
}

.search-box .form-control:focus {
    border-color: #20c997;
    box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25);
}

.form-select {
    border-radius: 8px;
    border: 2px solid #e2e8f0;
}

.form-select:focus {
    border-color: #20c997;
    box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25);
}

.table {
    margin: 0;
}

.table th {
    background: #f8f9fa;
    border: none;
    color: #2d3748;
    font-weight: 600;
    padding: 15px;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
}

.content-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.content-title i {
    font-size: 1.2rem;
}

.course-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.type-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-badge.pdf {
    background: #ffebee;
    color: #c62828;
}

.type-badge.video {
    background: #e8f5e8;
    color: #2e7d32;
}

.download-count {
    color: #20c997;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-icon {
    background: #20c997;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 20px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-header {
    background: #20c997;
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
    border: none;
}

.card-title {
    margin: 0;
    font-weight: 600;
    font-size: 1.2rem;
}

.card-body {
    padding: 25px;
}

.btn-primary {
    background: #20c997;
    border-color: #20c997;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
}

.btn-primary:hover {
    background: #1a9f7a;
    border-color: #1a9f7a;
}

.btn-outline-primary {
    color: #20c997;
    border-color: #20c997;
}

.btn-outline-primary:hover {
    background: #20c997;
    border-color: #20c997;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
}

.btn-outline-success {
    color: #28a745;
    border-color: #28a745;
}

.btn-outline-success:hover {
    background: #28a745;
    border-color: #28a745;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
}

@media (max-width: 768px) {
    .staff-contents {
        padding: 15px;
    }
    
    .page-header .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
}
</style>
@endpush
