@extends('layouts.student')

@section('title', 'Assignments | Student | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>My Assignments</h1>
    <p>View and submit your course assignments</p>
</div>

<!-- Assignment Filters -->
<div class="content-card mb-4">
    <div class="row">
        <div class="col-md-4">
            <label for="statusFilter" class="form-label">Filter by Status</label>
            <select class="form-select" id="statusFilter">
                <option value="all">All Assignments</option>
                <option value="available">Available</option>
                <option value="not_yet_available">Not Yet Available</option>
                <option value="submitted">Submitted</option>
                <option value="graded">Graded</option>
                <option value="overdue">Overdue</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="subjectFilter" class="form-label">Filter by Subject</label>
            <select class="form-select" id="subjectFilter">
                <option value="all">All Subjects</option>
                @foreach($enrolledSubjects as $enrollment)
                    <option value="{{ $enrollment->subject_code }}">{{ $enrollment->subject_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="sortBy" class="form-label">Sort By</label>
            <select class="form-select" id="sortBy">
                <option value="due_date">Due Date</option>
                <option value="created_at">Created Date</option>
                <option value="title">Title</option>
            </select>
        </div>
    </div>
</div>

<!-- Assignments List -->
<div class="content-card">
    <h5><i class="fas fa-clipboard-list"></i> Assignments</h5>
    <div id="assignmentsList">
        @if($assignments->count() > 0)
            <div class="row" id="assignmentsContainer">
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
                    
                    <div class="col-md-6 mb-4 assignment-card" 
                         data-status="{{ $isGraded ? 'graded' : ($isSubmitted ? 'submitted' : ($isOverdue ? 'overdue' : ($isNotYetAvailable ? 'not_yet_available' : 'available'))) }}"
                         data-subject="{{ $assignment->subject_code }}">
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
                                            <i class="fas fa-book"></i> {{ $assignment->subject->code }}
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i> {{ $assignment->class_code }}
                                        </small>
                                    </div>
                                </div>
                                
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
                                
                                @if($assignment->attachments && count($assignment->attachments) > 0)
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-file-pdf"></i> Assignment Files:
                                            @foreach($assignment->attachments as $index => $attachment)
                                                <a href="{{ route('student.assignments.download', ['assignmentId' => $assignment->id, 'fileIndex' => $index]) }}" class="text-primary">
                                                    <i class="fas fa-file-pdf text-danger"></i> {{ $attachment['original_name'] }}
                                                </a>
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        </small>
                                    </div>
                                @endif
                                
                                @if($submission)
                                    <div class="alert alert-info mb-2">
                                        <small>
                                            <i class="fas fa-check-circle"></i> 
                                            Submitted on {{ $submission->submitted_at->format('M d, Y H:i') }}
                                            @if($submission->is_late)
                                                <span class="text-warning">(Late)</span>
                                            @endif
                                        </small>
                                    </div>
                                    
                                    @if($isGraded)
                                        <div class="alert alert-success mb-2">
                                            <small>
                                                <i class="fas fa-trophy"></i> 
                                                Grade: {{ $submission->marks_obtained }}/{{ $assignment->total_marks }}
                                                ({{ number_format(($submission->marks_obtained / $assignment->total_marks) * 100, 1) }}%)
                                            </small>
                                        </div>
                                        
                                        @if($submission->feedback)
                                            <div class="alert alert-light mb-2">
                                                <small>
                                                    <strong>Feedback:</strong><br>
                                                    {{ $submission->feedback }}
                                                </small>
                                            </div>
                                        @endif
                                    @endif
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
                                    @elseif($isPastDue)
                                        <button class="btn btn-sm btn-outline-danger" disabled>
                                            <i class="fas fa-times"></i> Past Due
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
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Assignments Available</h4>
                <p class="text-muted">You don't have any assignments yet. Check back later or contact your lecturer.</p>
            </div>
        @endif
    </div>
</div>

<!-- Assignment Details Modal -->
<div class="modal fade" id="assignmentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assignment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="assignmentDetailsContent">
                <!-- Assignment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Submission Modal -->
<div class="modal fade" id="submissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="submissionForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="assignmentId" name="assignment_id">
                    
                    <div class="mb-3">
                        <label for="submissionText" class="form-label">Submission Text</label>
                        <textarea class="form-control" id="submissionText" name="submission_text" rows="6" placeholder="Enter your submission here..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Attachments (PDF only) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept=".pdf" required>
                        <div class="form-text">Upload your assignment files in PDF format only. Maximum 10MB per file. At least one PDF file is required.</div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Only PDF files are accepted. Please convert your documents to PDF before uploading.
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Once submitted, you cannot modify your submission. Please review carefully before submitting.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignmentForm()">
                    <i class="fas fa-upload"></i> Submit Assignment
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Filter assignments
document.getElementById('statusFilter').addEventListener('change', filterAssignments);
document.getElementById('subjectFilter').addEventListener('change', filterAssignments);
document.getElementById('sortBy').addEventListener('change', sortAssignments);

function filterAssignments() {
    const statusFilter = document.getElementById('statusFilter').value;
    const subjectFilter = document.getElementById('subjectFilter').value;
    const assignmentCards = document.querySelectorAll('.assignment-card');
    
    assignmentCards.forEach(card => {
        const status = card.getAttribute('data-status');
        const subject = card.getAttribute('data-subject');
        
        let showCard = true;
        
        if (statusFilter !== 'all' && status !== statusFilter) {
            showCard = false;
        }
        
        if (subjectFilter !== 'all' && subject !== subjectFilter) {
            showCard = false;
        }
        
        card.style.display = showCard ? 'block' : 'none';
    });
}

function sortAssignments() {
    const sortBy = document.getElementById('sortBy').value;
    const container = document.getElementById('assignmentsContainer');
    const cards = Array.from(container.children);
    
    cards.sort((a, b) => {
        // This is a simplified sort - in a real implementation, you'd sort by the actual data
        return a.textContent.localeCompare(b.textContent);
    });
    
    cards.forEach(card => container.appendChild(card));
}

// View assignment details
function viewAssignmentDetails(assignmentId) {
    fetch(`/student/assignments/${assignmentId}/details`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayAssignmentDetails(data.assignment, data.submission);
            new bootstrap.Modal(document.getElementById('assignmentDetailsModal')).show();
        } else {
            alert('Error: ' + (data.error || 'Failed to load assignment details'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading assignment details');
    });
}

// Display assignment details
function displayAssignmentDetails(assignment, submission) {
    const content = document.getElementById('assignmentDetailsContent');
    
    let html = `
        <div class="row">
            <div class="col-md-8">
                <h4>${assignment.title}</h4>
                <p class="text-muted">${assignment.subject.code} - ${assignment.subject.name}</p>
                <p class="text-muted">Class: ${assignment.class_code}</p>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-primary fs-6">${assignment.total_marks} marks</span>
            </div>
        </div>
        
        <hr>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Available From:</strong><br>
                ${new Date(assignment.available_from).toLocaleString()}
            </div>
            <div class="col-md-6">
                <strong>Due Date:</strong><br>
                ${new Date(assignment.due_date).toLocaleString()}
            </div>
        </div>
        
        <div class="mb-3">
            <strong>Description:</strong>
            <p>${assignment.description}</p>
        </div>
        
        ${assignment.instructions ? `
            <div class="mb-3">
                <strong>Instructions:</strong>
                <p>${assignment.instructions}</p>
            </div>
        ` : ''}
        
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Type:</strong> ${assignment.type.charAt(0).toUpperCase() + assignment.type.slice(1)}
            </div>
            <div class="col-md-6">
                <strong>Passing Marks:</strong> ${assignment.passing_marks}
            </div>
        </div>
        
        ${assignment.attachments && assignment.attachments.length > 0 ? `
            <div class="mb-3">
                <strong>Assignment Files:</strong>
                <ul class="list-group mt-2">
                    ${assignment.attachments.map((attachment, index) => 
                        `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="/student/assignments/download/${assignment.id}/${index}" class="text-decoration-none">
                                <i class="fas fa-file-pdf text-danger"></i> ${attachment.original_name}
                            </a>
                            <small class="text-muted">${(attachment.file_size / 1024 / 1024).toFixed(2)} MB</small>
                        </li>`
                    ).join('')}
                </ul>
            </div>
        ` : ''}
    `;
    
    if (submission) {
        html += `
            <hr>
            <h5>Your Submission</h5>
            <div class="alert alert-info">
                <strong>Submitted:</strong> ${new Date(submission.submitted_at).toLocaleString()}
                ${submission.is_late ? '<span class="text-warning">(Late)</span>' : ''}
            </div>
            
            ${submission.submission_text ? `
                <div class="mb-3">
                    <strong>Submission Text:</strong>
                    <p>${submission.submission_text}</p>
                </div>
            ` : ''}
            
            ${submission.attachments && submission.attachments.length > 0 ? `
                <div class="mb-3">
                    <strong>Attachments:</strong>
                    <ul>
                        ${submission.attachments.map(attachment => 
                            `<li><a href="/storage/${attachment.file_path}" target="_blank">${attachment.original_name}</a></li>`
                        ).join('')}
                    </ul>
                </div>
            ` : ''}
            
            ${submission.status === 'graded' ? `
                <div class="alert alert-success">
                    <h6>Grade: ${submission.marks_obtained}/${assignment.total_marks} (${((submission.marks_obtained / assignment.total_marks) * 100).toFixed(1)}%)</h6>
                    ${submission.feedback ? `<p><strong>Feedback:</strong><br>${submission.feedback}</p>` : ''}
                </div>
            ` : ''}
        `;
    }
    
    content.innerHTML = html;
}

// Submit assignment
function submitAssignment(assignmentId) {
    document.getElementById('assignmentId').value = assignmentId;
    new bootstrap.Modal(document.getElementById('submissionModal')).show();
}

// Submit assignment form
function submitAssignmentForm() {
    const form = document.getElementById('submissionForm');
    const formData = new FormData(form);
    const assignmentId = document.getElementById('assignmentId').value;
    
    fetch(`/student/assignments/${assignmentId}/submit`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assignment submitted successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to submit assignment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the assignment');
    });
}
</script>
@endsection
