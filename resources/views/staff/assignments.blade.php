@extends('layouts.staff-course')

@section('title', 'Assignments | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>Assignments</h1>
    <p>Create and manage assignments for your courses and classes</p>
</div>

<!-- Assignment Creation Form -->
<div class="content-card mb-4">
    <h5><i class="fas fa-plus"></i> Create New Assignment</h5>
    <form id="assignmentForm">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label for="subjectSelect" class="form-label">Subject</label>
                <select class="form-select" id="subjectSelect" name="subject_code" required>
                    <option value="">Choose a subject...</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->code }}">{{ $subject->code }} - {{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="classSelect" class="form-label">Class</label>
                <select class="form-select" id="classSelect" name="class_code" required>
                    <option value="">Choose a class...</option>
                </select>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-8">
                <label for="assignmentTitle" class="form-label">Assignment Title</label>
                <input type="text" class="form-control" id="assignmentTitle" name="title" required>
            </div>
            <div class="col-md-4">
                <label for="assignmentType" class="form-label">Type</label>
                <select class="form-select" id="assignmentType" name="type" required>
                    <option value="individual">Individual</option>
                    <option value="group">Group</option>
                </select>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="totalMarks" class="form-label">Total Marks</label>
                <input type="number" class="form-control" id="totalMarks" name="total_marks" step="0.01" required>
            </div>
            <div class="col-md-6">
                <label for="passingMarks" class="form-label">Passing Marks</label>
                <input type="number" class="form-control" id="passingMarks" name="passing_marks" step="0.01" required>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="availableFrom" class="form-label">Available From</label>
                <input type="datetime-local" class="form-control" id="availableFrom" name="available_from" required>
            </div>
            <div class="col-md-6">
                <label for="dueDate" class="form-label">Due Date</label>
                <input type="datetime-local" class="form-control" id="dueDate" name="due_date" required>
            </div>
        </div>
        
        <div class="mt-3">
            <label for="assignmentDescription" class="form-label">Description</label>
            <textarea class="form-control" id="assignmentDescription" name="description" rows="4" required></textarea>
        </div>
        
        <div class="mt-3">
            <label for="instructions" class="form-label">Instructions (Optional)</label>
            <textarea class="form-control" id="instructions" name="instructions" rows="3"></textarea>
        </div>
        
        <div class="mt-3">
            <label for="assignmentFiles" class="form-label">Assignment Files (PDF only)</label>
            <input type="file" class="form-control" id="assignmentFiles" name="assignment_files[]" multiple accept=".pdf">
            <div class="form-text">Upload assignment files in PDF format. Students will download these files.</div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="allowLateSubmission" name="allow_late_submission">
                    <label class="form-check-label" for="allowLateSubmission">
                        Allow Late Submission
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <label for="latePenalty" class="form-label">Late Penalty (%)</label>
                <input type="number" class="form-control" id="latePenalty" name="late_penalty_percentage" min="0" max="100" value="0">
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Assignment
            </button>
            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>
    </form>
</div>

<!-- Existing Assignments -->
<div class="content-card">
    <h5><i class="fas fa-list"></i> Your Assignments</h5>
    <div id="assignmentsList">
        @if($assignments->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Submissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->title }}</td>
                            <td>{{ $assignment->subject->code }}</td>
                            <td>{{ $assignment->class_code }}</td>
                            <td>{{ $assignment->due_date->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $assignment->status === 'published' ? 'success' : ($assignment->status === 'draft' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </td>
                            <td>{{ $assignment->submissions->count() }}</td>
                            <td>
                                @if($assignment->status === 'draft')
                                    <button class="btn btn-sm btn-success" onclick="publishAssignment({{ $assignment->id }})">
                                        <i class="fas fa-eye"></i> Publish
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-info" onclick="viewSubmissions({{ $assignment->id }})">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <p class="text-muted">No assignments created yet. Create your first assignment above!</p>
            </div>
        @endif
    </div>
</div>

<!-- Submissions Modal -->
<div class="modal fade" id="submissionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assignment Submissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="submissionsContent">
                <!-- Submissions will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Load classes when subject is selected
document.getElementById('subjectSelect').addEventListener('change', function() {
    const subjectCode = this.value;
    const classSelect = document.getElementById('classSelect');
    
    if (subjectCode) {
        // Get classes for this subject
        const subject = @json($subjects);
        const selectedSubject = subject.find(s => s.code === subjectCode);
        
        classSelect.innerHTML = '<option value="">Choose a class...</option>';
        
        if (selectedSubject && selectedSubject.class_schedules) {
            selectedSubject.class_schedules.forEach(function(classSchedule) {
                const option = document.createElement('option');
                option.value = classSchedule.class_code;
                option.textContent = classSchedule.class_name;
                classSelect.appendChild(option);
            });
        }
        
        classSelect.disabled = false;
    } else {
        classSelect.innerHTML = '<option value="">Choose a class...</option>';
        classSelect.disabled = true;
    }
});

// Assignment form submission
document.getElementById('assignmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("staff.assignments.create") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assignment created successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to create assignment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the assignment');
    });
});

// Publish assignment
function publishAssignment(assignmentId) {
    if (confirm('Are you sure you want to publish this assignment?')) {
        fetch(`/staff/assignments/${assignmentId}/publish`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment published successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to publish assignment'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while publishing the assignment');
        });
    }
}

// View submissions
function viewSubmissions(assignmentId) {
    fetch(`/staff/assignments/${assignmentId}/submissions`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySubmissions(data.assignment, data.submissions);
            new bootstrap.Modal(document.getElementById('submissionsModal')).show();
        } else {
            alert('Error: ' + (data.error || 'Failed to load submissions'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading submissions');
    });
}

// Display submissions in modal
function displaySubmissions(assignment, submissions) {
    const content = document.getElementById('submissionsContent');
    
    let html = `
        <h6>${assignment.title}</h6>
        <p><strong>Subject:</strong> ${assignment.subject.code} - ${assignment.subject.name}</p>
        <p><strong>Class:</strong> ${assignment.class_code}</p>
        <p><strong>Due Date:</strong> ${new Date(assignment.due_date).toLocaleString()}</p>
        <hr>
    `;
    
    if (submissions.length > 0) {
        html += `
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th>Marks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        submissions.forEach(submission => {
            html += `
                <tr>
                    <td>${submission.user.name}</td>
                    <td>${new Date(submission.submitted_at).toLocaleString()}</td>
                    <td>
                        <span class="badge bg-${submission.status === 'graded' ? 'success' : 'warning'}">
                            ${submission.status}
                        </span>
                    </td>
                    <td>${submission.marks_obtained || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="gradeSubmission(${submission.id})">
                            Grade
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    } else {
        html += '<p class="text-muted">No submissions yet.</p>';
    }
    
    content.innerHTML = html;
}

// Grade submission
function gradeSubmission(submissionId) {
    const marks = prompt('Enter marks obtained:');
    if (marks !== null && marks !== '') {
        const feedback = prompt('Enter feedback (optional):');
        
        fetch(`/staff/assignments/submissions/${submissionId}/grade`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                marks_obtained: parseFloat(marks),
                feedback: feedback || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Submission graded successfully!');
                // Refresh the submissions view
                const assignmentId = document.querySelector('[onclick*="viewSubmissions"]').getAttribute('onclick').match(/\d+/)[0];
                viewSubmissions(assignmentId);
            } else {
                alert('Error: ' + (data.error || 'Failed to grade submission'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while grading the submission');
        });
    }
}

// Reset form
function resetForm() {
    document.getElementById('assignmentForm').reset();
    document.getElementById('classSelect').innerHTML = '<option value="">Choose a class...</option>';
    document.getElementById('classSelect').disabled = true;
}
</script>
@endsection