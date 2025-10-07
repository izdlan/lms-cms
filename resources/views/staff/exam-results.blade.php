@extends('layouts.staff-course')

@section('title', 'Exam Results Management | Lecturer | Olympia Education')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Exam Results</li>
                    </ol>
                </div>
                <h4 class="page-title">Exam Results Management</h4>
            </div>
        </div>
    </div>

    <!-- Subject and Class Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Select Subject and Class</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('staff.exam-results') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="subject_code" class="form-label">Subject</label>
                            <select name="subject_code" id="subject_code" class="form-select" onchange="loadClasses()">
                                <option value="">Select a subject...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->code }}" {{ $selectedSubject && $selectedSubject->code == $subject->code ? 'selected' : '' }}>
                                        {{ $subject->code }} - {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="class_code" class="form-label">Class</label>
                            <select name="class_code" id="class_code" class="form-select" onchange="this.form.submit()">
                                <option value="">Select a class...</option>
                                @if($selectedSubject)
                                    @foreach($selectedSubject->classSchedules->where('lecturer_id', $lecturer->id) as $class)
                                        <option value="{{ $class->class_code }}" {{ $selectedClass && $selectedClass->class_code == $class->class_code ? 'selected' : '' }}>
                                            {{ $class->class_name }} ({{ $class->class_code }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                @if($selectedSubject)
                                    <a href="{{ route('staff.exam-results') }}" class="btn btn-outline-secondary">Clear Selection</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($selectedSubject)
        <!-- Subject and Class Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title mb-1">{{ $selectedSubject->code }} - {{ $selectedSubject->name }}</h5>
                        @if($selectedClass)
                            <p class="card-text mb-2">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Class: {{ $selectedClass->class_name }} ({{ $selectedClass->class_code }})
                            </p>
                        @endif
                        <p class="card-text mb-0">
                            <i class="fas fa-users me-2"></i>{{ $students->count() }} Students {{ $selectedClass ? 'in Class' : 'Enrolled' }}
                            <span class="ms-4">
                                <i class="fas fa-chart-line me-2"></i>{{ $examResults->count() }} Results Entered
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students and Results Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Students & Results</h5>
                        <div>
                            <button type="button" class="btn btn-success" onclick="addBulkResults()">
                                <i class="fas fa-plus me-1"></i>Add Bulk Results
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Total Marks</th>
                                        <th>Percentage</th>
                                        <th>Grade</th>
                                        <th>GPA</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $enrollment)
                                        @php
                                            $result = $examResults->get($enrollment->user_id);
                                        @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $enrollment->user->student_id ?? $enrollment->user->id }}</td>
                                        <td>{{ $enrollment->user->name }}</td>
                                        <td>{{ $enrollment->user->email }}</td>
                                        <td>
                                            @if($result)
                                                <span class="badge bg-success">Results Entered</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($result)
                                                {{ number_format($result->total_marks, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($result)
                                                <span class="badge bg-{{ $result->percentage >= 50 ? 'success' : 'danger' }}">
                                                    {{ number_format($result->percentage, 1) }}%
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($result)
                                                @php
                                                    $gradeColor = 'secondary';
                                                    if($result->grade) {
                                                        switch($result->grade) {
                                                            case 'A+': case 'A': case 'A-': $gradeColor = 'success'; break;
                                                            case 'B+': case 'B': case 'B-': $gradeColor = 'primary'; break;
                                                            case 'C+': case 'C': case 'C-': $gradeColor = 'warning'; break;
                                                            case 'D+': case 'D': $gradeColor = 'danger'; break;
                                                            case 'F': $gradeColor = 'dark'; break;
                                                        }
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ $gradeColor }}">{{ $result->grade }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($result)
                                                {{ number_format($result->gpa, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('staff.exam-results.form', [
                                                    'subject_code' => $selectedSubject->code,
                                                    'class_code' => $selectedClass ? $selectedClass->class_code : null,
                                                    'student_id' => $enrollment->user_id,
                                                    'academic_year' => date('Y'),
                                                    'semester' => 'Semester 1'
                                                ]) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                    {{ $result ? 'Edit' : 'Add' }}
                                                </a>
                                                @if($result)
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteResult({{ $result->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Subject Selected -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Select a Subject</h4>
                        <p class="text-muted">Please select a subject from the dropdown above to manage exam results.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this exam result? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteResultId = null;

function deleteResult(resultId) {
    deleteResultId = resultId;
    $('#deleteModal').modal('show');
}

$('#confirmDelete').click(function() {
    if (deleteResultId) {
        $.ajax({
            url: '{{ route("staff.exam-results.delete") }}',
            method: 'DELETE',
            data: {
                exam_result_id: deleteResultId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the result.');
            }
        });
    }
});

// Dynamic subject classes from backend (same as courses page)
const subjectClasses = {
    @foreach($subjects as $subject)
    '{{ $subject->code }}': [
        @foreach($subject->classSchedules->where('lecturer_id', $lecturer->id) as $class)
        { value: '{{ $class->class_code }}', text: '{{ $class->class_name }} ({{ $class->class_code }})' },
        @endforeach
    ],
    @endforeach
};

// Load classes on page load if subject is already selected
document.addEventListener('DOMContentLoaded', function() {
    const subjectSelect = document.getElementById('subject_code');
    if (subjectSelect.value) {
        loadClasses();
    }
});

function loadClasses() {
    const subjectCode = document.getElementById('subject_code').value;
    const classSelect = document.getElementById('class_code');
    
    // Clear existing options
    classSelect.innerHTML = '<option value="">Select a class...</option>';
    
    if (subjectCode && subjectClasses[subjectCode]) {
        subjectClasses[subjectCode].forEach(classOption => {
            const option = document.createElement('option');
            option.value = classOption.value;
            option.textContent = classOption.text;
            classSelect.appendChild(option);
        });
    }
}

function addBulkResults() {
    // This would open a modal or redirect to a bulk results page
    alert('Bulk results feature coming soon!');
}
</script>

<style>
.table th {
    background-color: #343a40 !important;
    color: white !important;
    border-color: #495057 !important;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.8em;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.page-title-box {
    margin-bottom: 1.5rem;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endsection
