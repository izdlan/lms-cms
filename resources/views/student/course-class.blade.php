@extends('layouts.app')

@section('title', $subjectDetails['name'] . ' - Class')

@section('content')
<div class="student-dashboard">
    <!-- Student Navigation Bar -->
    @include('student.partials.student-navbar')
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('student.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <!-- Course Header -->
                <div class="courses-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>{{ $subjectDetails['name'] }}</h1>
                            <p class="mb-0">
                                <strong>{{ $enrollment->subject_code }}</strong> - {{ $enrollment->class_code }} | 
                                Lecturer: {{ $enrollment->lecturer ? $enrollment->lecturer->name : 'TBA' }} | 
                                Duration: {{ $subjectDetails['duration'] ?? '4 weeks' }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('student.courses') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Back to Courses
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Course Navigation Tabs -->
                <div class="course-navigation mb-4">
                    <ul class="nav nav-tabs" id="courseTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab">
                                <i class="fas fa-info-circle me-2"></i>Course Summary
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="announcements-tab" data-bs-toggle="tab" data-bs-target="#announcements" type="button" role="tab">
                                <i class="fas fa-bullhorn me-2"></i>Announcements
                                @if($announcements->count() > 0)
                                    <span class="badge bg-primary ms-2">{{ $announcements->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab">
                                <i class="fas fa-book me-2"></i>Course Content
                                @if($courseContents->count() > 0)
                                    <span class="badge bg-success ms-2">{{ $courseContents->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab">
                                <i class="fas fa-tasks me-2"></i>Assignments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="discussions-tab" data-bs-toggle="tab" data-bs-target="#discussions" type="button" role="tab">
                                <i class="fas fa-comments me-2"></i>Discussions
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="courseTabContent">
                    <!-- Course Summary Tab -->
                    <div class="tab-pane fade show active" id="summary" role="tabpanel">
                        <!-- Course Description -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle me-2"></i>Course Description</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $subjectDetails['description'] }}</p>
                            </div>
                        </div>

                        @if(isset($subjectDetails['clos']) && count($subjectDetails['clos']) > 0)
                            <!-- Course Learning Outcomes -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-graduation-cap me-2"></i>Course Learning Outcomes (CLOs)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 10%;">CLO</th>
                                                    <th style="width: 60%;">Learning Outcome Description</th>
                                                    <th style="width: 30%;">MQF2.0 Alignment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subjectDetails['clos'] as $clo)
                                                    <tr>
                                                        <td><strong>{{ $clo['clo'] }}</strong></td>
                                                        <td>{{ $clo['description'] }}</td>
                                                        <td><strong>{{ $clo['mqf'] }}</strong></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($subjectDetails['topics']) && count($subjectDetails['topics']) > 0)
                            <!-- Topics Covered -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-book me-2"></i>Topics Covered According to CLOs and Assessment Methods</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 10%;">CLO</th>
                                                    <th style="width: 70%;">Topic</th>
                                                    <th style="width: 20%;">Assessment Methods</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subjectDetails['topics'] as $index => $topic)
                                                    <tr>
                                                        <td><strong>{{ $topic['clo'] }}</strong></td>
                                                        <td>{{ $index + 1 }}. {{ $topic['topic'] }}</td>
                                                        <td>{{ $index === 0 ? $subjectDetails['assessment'] : '' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Announcements Tab -->
                    <div class="tab-pane fade" id="announcements" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-bullhorn me-2"></i>Announcements</h5>
                            </div>
                            <div class="card-body">
                                @if($announcements->count() > 0)
                                    @foreach($announcements as $announcement)
                                        <div class="announcement-item mb-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">
                                                    @if($announcement->is_important)
                                                        <i class="fas fa-star text-warning me-2"></i>
                                                    @else
                                                        <i class="fas fa-info-circle text-info me-2"></i>
                                                    @endif
                                                    {{ $announcement->title }}
                                                </h6>
                                                <small class="text-muted">{{ $announcement->published_at->format('M d, Y') }}</small>
                                            </div>
                                            <div class="mb-0">{!! nl2br(e($announcement->content)) !!}</div>
                                            <small class="text-muted">By {{ $announcement->author_name }}</small>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-bullhorn text-muted mb-3" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted">No announcements yet</h5>
                                        <p class="text-muted">Announcements from your lecturer will appear here.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Course Content Tab -->
                    <div class="tab-pane fade" id="content" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-book me-2"></i>Course Materials</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Please use PC or laptop to download files other than PDF files.</strong>
                                </div>
                                
                                @if($courseMaterials->count() > 0)
                                    <div class="row">
                                        @foreach($courseMaterials as $material)
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card h-100 material-card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="material-icon me-3">
                                                                <i class="{{ $material->file_icon }} fa-2x text-primary"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="card-title mb-1">{{ $material->title }}</h6>
                                                                <small class="text-muted">{{ $material->author_name }}</small>
                                                            </div>
                                                        </div>
                                                        
                                                        @if($material->description)
                                                            <p class="card-text text-muted small">{{ Str::limit($material->description, 100) }}</p>
                                                        @endif
                                                        
                                                        <div class="material-meta mb-3">
                                                            <div class="row text-center">
                                                                <div class="col-4">
                                                                    <small class="text-muted d-block">Type</small>
                                                                    <span class="badge bg-secondary">{{ ucfirst($material->material_type) }}</span>
                                                                </div>
                                                                <div class="col-4">
                                                                    <small class="text-muted d-block">Size</small>
                                                                    <small class="fw-bold">{{ $material->file_size_formatted }}</small>
                                                                </div>
                                                                <div class="col-4">
                                                                    <small class="text-muted d-block">Downloads</small>
                                                                    <span class="badge bg-info">{{ $material->download_count }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">{{ $material->published_at->format('M d, Y') }}</small>
                                                            <div>
                                                                @if($material->external_url)
                                                                    <a href="{{ $material->external_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-external-link-alt me-1"></i>Open
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadMaterial({{ $material->id }}, '{{ $material->title }}')">
                                                                        <i class="fas fa-download me-1"></i>Download
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-book text-muted mb-3" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted">No course materials available</h5>
                                        <p class="text-muted">Course materials will be uploaded by your lecturer.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Assignments Tab -->
                    <div class="tab-pane fade" id="assignments" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-tasks me-2"></i>Assignments</h5>
                            </div>
                            <div class="card-body">
                                @if($assignments->count() > 0)
                                    <div class="row">
                                        @foreach($assignments as $assignment)
                                            @php
                                                $submission = $submissions->get($assignment->id);
                                                $isOverdue = $assignment->due_date < now() && !$submission;
                                                $isSubmitted = $submission && $submission->status === 'submitted';
                                                $isGraded = $submission && $submission->status === 'graded';
                                                $isAvailable = $assignment->isAvailableForSubmission();
                                                $isNotYetAvailable = $assignment->available_from > now();
                                                $isPastDue = $assignment->due_date < now() && !$assignment->allow_late_submission;
                                            @endphp
                                            
                                            <div class="col-md-6 mb-4">
                                                <div class="card h-100 {{ $isOverdue ? 'border-danger' : ($isGraded ? 'border-success' : '') }}">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="card-title mb-0">{{ $assignment->title }}</h6>
                                                        <span class="badge bg-{{ $isGraded ? 'success' : ($isSubmitted ? 'info' : ($isOverdue ? 'danger' : ($isNotYetAvailable ? 'secondary' : 'warning'))) }}">
                                                            @if($isGraded)
                                                                Graded
                                                            @elseif($isSubmitted)
                                                                Submitted
                                                            @elseif($isOverdue)
                                                                Overdue
                                                            @elseif($isNotYetAvailable)
                                                                Not Yet Available
                                                            @else
                                                                Available
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="card-text">{{ Str::limit($assignment->description, 100) }}</p>
                                                        
                                                        <div class="row mb-2">
                                                            <div class="col-6">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar"></i> Due: {{ $assignment->due_date->format('M d, Y H:i') }}
                                                                </small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-star"></i> {{ $assignment->total_marks }} marks
                                                                </small>
                                                            </div>
                                                        </div>
                                                        
                                                        @if($isNotYetAvailable)
                                                            <div class="alert alert-info mb-2">
                                                                <small>
                                                                    <i class="fas fa-clock"></i> 
                                                                    Available from: {{ $assignment->available_from->format('M d, Y H:i') }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <button class="btn btn-sm btn-outline-primary" onclick="viewAssignmentDetails({{ $assignment->id }})">
                                                                <i class="fas fa-eye"></i> View Details
                                                            </button>
                                                            
                                                            @if($isNotYetAvailable)
                                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                                    <i class="fas fa-clock"></i> Not Yet Available
                                                                </button>
                                                            @elseif($isAvailable && !$isSubmitted)
                                                                <button class="btn btn-sm btn-primary" onclick="submitAssignment({{ $assignment->id }})">
                                                                    <i class="fas fa-upload"></i> Submit
                                                                </button>
                                                            @elseif($isSubmitted && !$isGraded)
                                                                <button class="btn btn-sm btn-secondary" disabled>
                                                                    <i class="fas fa-clock"></i> Submitted
                                                                </button>
                                                            @elseif($isGraded)
                                                                <button class="btn btn-sm btn-success" onclick="viewAssignmentDetails({{ $assignment->id }})">
                                                                    <i class="fas fa-eye"></i> View Grade
                                                                </button>
                                                            @else
                                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                                    <i class="fas fa-lock"></i> Not Available
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">No Assignments Available</h4>
                                        <p class="text-muted">You don't have any assignments yet. Check back later or contact your lecturer.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Discussions Tab -->
                    <div class="tab-pane fade" id="discussions" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-comments me-2"></i>Discussions</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">No discussions available yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Course Navigation Tabs Styling */
.course-navigation .nav-tabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 0;
}

.course-navigation .nav-tabs .nav-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-bottom: none;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.course-navigation .nav-tabs .nav-link:hover {
    color: #495057;
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.course-navigation .nav-tabs .nav-link.active {
    color: #212529;
    background-color: #ffffff;
    border-color: #dee2e6 #dee2e6 #ffffff;
    font-weight: 600;
}

.course-navigation .nav-tabs .nav-link i {
    margin-right: 0.5rem;
    font-size: 0.9rem;
}

.course-navigation .nav-tabs .nav-link .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    margin-left: 0.5rem;
}

/* Course Header Styling */
.courses-header h1 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.courses-header p {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 0;
}

/* Card Styling */
.card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.25rem;
}

.card-header h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0;
}

.card-body {
    padding: 1.25rem;
}

/* Table Styling */
.table thead th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 0.75rem;
}

.table tbody td {
    padding: 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Announcement Styling */
.announcement-item {
    padding: 1rem 0;
    border-bottom: 1px solid #e9ecef;
}

.announcement-item:last-child {
    border-bottom: none;
}

.announcement-item h6 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.announcement-item p {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.announcement-item small {
    color: #adb5bd;
    font-size: 0.875rem;
}

/* Button Styling */
.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.25rem;
}

/* Badge Styling */
.badge {
    font-size: 0.75em;
    font-weight: 500;
    padding: 0.35em 0.65em;
    border-radius: 0.375rem;
}

/* Link Styling in Announcements */
.announcement-item a {
    color: #0d6efd !important;
    text-decoration: underline !important;
    word-break: break-all;
}

.announcement-item a:hover {
    color: #0a58ca !important;
    text-decoration: underline !important;
}

/* Course Materials Styling */
.material-card {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.material-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    border-color: #0d6efd;
}

.material-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 10px;
}

.material-meta {
    border-top: 1px solid #f8f9fa;
    padding-top: 15px;
    margin-top: 15px;
}

.bg-primary {
    background-color: #0d6efd !important;
}

.bg-success {
    background-color: #198754 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .course-navigation .nav-tabs .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .courses-header h1 {
        font-size: 1.5rem;
    }
    
    .courses-header p {
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function downloadFile(filePath, fileName) {
    // For demo purposes, we'll just show an alert
    // In a real application, you would implement actual file download
    alert('Downloading: ' + fileName + '\nPath: ' + filePath);
    
    // In a real implementation, you might do:
    // window.location.href = '/download/' + encodeURIComponent(filePath);
    // or use a proper download endpoint
}

function downloadMaterial(materialId, materialTitle) {
    // Download the material from the server
    window.location.href = '/student/materials/download/' + materialId;
}

// Function to convert URLs to clickable links
function convertUrlsToLinks(text) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, function(url) {
        return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-underline">' + url + '</a>';
    });
}

// Initialize tooltips and convert URLs to links
document.addEventListener('DOMContentLoaded', function() {
    // Convert URLs to clickable links in announcement content
    const announcementItems = document.querySelectorAll('.announcement-item');
    announcementItems.forEach(function(item) {
        const contentDiv = item.querySelector('div.mb-0');
        if (contentDiv) {
            contentDiv.innerHTML = convertUrlsToLinks(contentDiv.innerHTML);
        }
    });
    
    // Initialize tooltips if any
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
