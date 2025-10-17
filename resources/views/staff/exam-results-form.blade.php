@extends('layouts.staff-course')

@section('title', 'Exam Results Form | Lecturer | Olympia Education')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.exam-results') }}">Exam Results</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
                <h4 class="page-title">Exam Results Form</h4>
            </div>
        </div>
    </div>

    <!-- Student and Subject Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title mb-1">{{ $subject->code }} - {{ $subject->name }}</h5>
                            <p class="card-text mb-0">Academic Year: {{ $academicYear }} | Semester: {{ $semester }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="card-title mb-1">{{ $student->name }}</h6>
                            <p class="card-text mb-0">
                                Student ID: {{ $student->student_id ?? $student->id }} | 
                                Email: {{ $student->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Results Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assessment Results</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.exam-results.store') }}" method="POST" id="examResultsForm">
                        @csrf
                        <input type="hidden" name="subject_code" value="{{ $subject->code }}">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="hidden" name="academic_year" value="{{ $academicYear }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">

                        <!-- Assessment Headers Configuration -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-cog me-2"></i>Assessment Headers Configuration
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">
                                            Configure the assessment headers for this subject. You can add, remove, or modify assessment types.
                                        </p>
                                        <div id="assessmentHeaders">
                                            <!-- Default assessment headers -->
                                            <div class="row mb-2">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control assessment-name" value="Quiz" placeholder="Assessment Name">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control assessment-max" value="20" placeholder="Max Score" min="1">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control assessment-score" value="0" placeholder="Score" min="0" step="0.01">
                                                </div>
                                                <div class="col-md-2">
                                                    <span class="badge bg-secondary assessment-percentage">0%</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeAssessment(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addAssessment()">
                                            <i class="fas fa-plus me-1"></i>Add Assessment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment Results Table -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="30%">Assessment</th>
                                                <th width="20%">Max Score</th>
                                                <th width="20%">Score Obtained</th>
                                                <th width="15%">Percentage</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assessmentTableBody">
                                            <!-- Assessment rows will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Obtained</h6>
                                        <h4 class="text-info" id="totalObtained">0</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Percentage</h6>
                                        <h4 class="text-success" id="totalPercentage">0%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Grade</h6>
                                        <h4 class="text-warning" id="finalGrade">F</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Grade Value (GPA)</h6>
                                        <h4 class="text-primary" id="finalGpa">0.00</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter any additional notes about the student's performance...">{{ $examResult->notes ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('staff.exam-results', ['subject_code' => $subject->code]) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>Back to Results
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary me-2" onclick="previewResults()">
                                            <i class="fas fa-eye me-1"></i>Preview
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="submitForm()">
                                            <i class="fas fa-save me-1"></i>Save Results
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Results Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">Save Results</button>
            </div>
        </div>
    </div>
</div>

<script>
// Default assessments
const defaultAssessments = [
    { name: 'Quiz', max_score: 20, score: 0 },
    { name: 'Assignment 1', max_score: 25, score: 0 },
    { name: 'Assignment 2', max_score: 25, score: 0 },
    { name: 'Midterm Exam', max_score: 30, score: 0 },
    { name: 'Final Exam', max_score: 50, score: 0 },
    { name: 'Project', max_score: 20, score: 0 }
];

// Load existing data if editing
@if($examResult && $examResult->assessments)
    const existingAssessments = @json($examResult->assessments);
@else
    const existingAssessments = defaultAssessments;
@endif

// Initialize the form
document.addEventListener('DOMContentLoaded', function() {
    loadAssessments();
    updateSummary();
});

function loadAssessments() {
    const container = document.getElementById('assessmentHeaders');
    const tableBody = document.getElementById('assessmentTableBody');
    
    container.innerHTML = '';
    tableBody.innerHTML = '';
    
    existingAssessments.forEach((assessment, index) => {
        addAssessmentRow(assessment.name, assessment.max_score, assessment.score || 0);
    });
}

function addAssessment() {
    addAssessmentRow('', 0, 0);
}

function addAssessmentRow(name = '', maxScore = 0, score = 0) {
    const container = document.getElementById('assessmentHeaders');
    const tableBody = document.getElementById('assessmentTableBody');
    const index = container.children.length;
    
    // Add to configuration section
    const configRow = document.createElement('div');
    configRow.className = 'row mb-2';
    configRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control assessment-name" value="${name}" placeholder="Assessment Name" onchange="updateTable()">
        </div>
        <div class="col-md-2">
            <input type="number" class="form-control assessment-max" value="${maxScore}" placeholder="Max Score" min="1" onchange="updateTable()">
        </div>
        <div class="col-md-2">
            <input type="number" class="form-control assessment-score" value="${score}" placeholder="Score" min="0" step="0.01" onchange="updateTable()">
        </div>
        <div class="col-md-2">
            <span class="badge bg-secondary assessment-percentage">0%</span>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeAssessment(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(configRow);
    
    updateTable();
}

function removeAssessment(button) {
    const row = button.closest('.row');
    row.remove();
    updateTable();
}

function updateTable() {
    const tableBody = document.getElementById('assessmentTableBody');
    const configRows = document.querySelectorAll('#assessmentHeaders .row');
    
    if (!tableBody) return; // Exit if table body doesn't exist
    tableBody.innerHTML = '';
    
    configRows.forEach((row, index) => {
        const name = row.querySelector('.assessment-name').value || `Assessment ${index + 1}`;
        const maxScore = parseFloat(row.querySelector('.assessment-max').value) || 0;
        const score = parseFloat(row.querySelector('.assessment-score').value) || 0;
        const percentage = maxScore > 0 ? ((score / maxScore) * 100).toFixed(1) : 0;
        
        // Update percentage badge
        const percentageBadge = row.querySelector('.assessment-percentage');
        if (percentageBadge) {
            percentageBadge.textContent = percentage + '%';
            percentageBadge.className = `badge ${percentage >= 50 ? 'bg-success' : 'bg-warning'}`;
        }
        
        // Add to table
        const tableRow = document.createElement('tr');
        tableRow.innerHTML = `
            <td>${name}</td>
            <td>${maxScore}</td>
            <td>${score}</td>
            <td><span class="badge ${percentage >= 50 ? 'bg-success' : 'bg-warning'}">${percentage}%</span></td>
            <td>${calculateGrade(percentage)}</td>
            <td>${calculateGpa(percentage).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeAssessmentByIndex(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(tableRow);
    });
    
    updateSummary();
}

function removeAssessmentByIndex(index) {
    const configRows = document.querySelectorAll('#assessmentHeaders .row');
    if (configRows[index]) {
        configRows[index].remove();
        updateTable();
    }
}

function updateSummary() {
    const configRows = document.querySelectorAll('#assessmentHeaders .row');
    let totalPossible = 0;
    let totalObtained = 0;
    
    configRows.forEach(row => {
        const maxScore = parseFloat(row.querySelector('.assessment-max').value) || 0;
        const score = parseFloat(row.querySelector('.assessment-score').value) || 0;
        totalPossible += maxScore;
        totalObtained += score;
    });
    
    const percentage = totalPossible > 0 ? ((totalObtained / totalPossible) * 100).toFixed(1) : 0;
    const grade = calculateGrade(percentage);
    const gradePoint = calculateGpa(percentage).toFixed(2);
    const gpa = calculateGpa(percentage).toFixed(2);
    
    // Add null checks to prevent errors
    const totalPossibleEl = document.getElementById('totalPossible');
    const totalObtainedEl = document.getElementById('totalObtained');
    const totalPercentageEl = document.getElementById('totalPercentage');
    const finalGradeEl = document.getElementById('finalGrade');
    
    if (totalPossibleEl) totalPossibleEl.textContent = totalPossible;
    if (totalObtainedEl) totalObtainedEl.textContent = totalObtained;
    if (totalPercentageEl) totalPercentageEl.textContent = percentage + '%';
    if (finalGradeEl) finalGradeEl.textContent = grade;
    const finalGpaEl = document.getElementById('finalGpa');
    if (finalGpaEl) finalGpaEl.textContent = gpa;
}

function calculateGrade(percentage) {
    if (percentage >= 90) return 'A+';
    if (percentage >= 80) return 'A';
    if (percentage >= 75) return 'A-';
    if (percentage >= 70) return 'B+';
    if (percentage >= 65) return 'B';
    if (percentage >= 60) return 'B-';
    if (percentage >= 55) return 'C+';
    if (percentage >= 50) return 'C';
    if (percentage >= 47) return 'C-';
    if (percentage >= 44) return 'D+';
    if (percentage >= 40) return 'D';
    if (percentage >= 30) return 'E';
    return 'F';
}

function calculateGpa(percentage) {
    if (percentage >= 90) return 4.00; // A+
    if (percentage >= 80) return 4.00; // A
    if (percentage >= 75) return 3.67; // A-
    if (percentage >= 70) return 3.33; // B+
    if (percentage >= 65) return 3.00; // B
    if (percentage >= 60) return 2.67; // B-
    if (percentage >= 55) return 2.33; // C+
    if (percentage >= 50) return 2.00; // C
    if (percentage >= 47) return 1.67; // C-
    if (percentage >= 44) return 1.33; // D+
    if (percentage >= 40) return 1.00; // D
    if (percentage >= 30) return 0.67; // E
    return 0.00; // F
}

function previewResults() {
    const configRows = document.querySelectorAll('#assessmentHeaders .row');
    const assessments = [];
    
    configRows.forEach(row => {
        const name = row.querySelector('.assessment-name').value;
        const maxScore = parseFloat(row.querySelector('.assessment-max').value) || 0;
        const score = parseFloat(row.querySelector('.assessment-score').value) || 0;
        
        if (name && maxScore > 0) {
            assessments.push({
                name: name,
                max_score: maxScore,
                score: score,
                percentage: maxScore > 0 ? ((score / maxScore) * 100).toFixed(1) : 0
            });
        }
    });
    
    const totalPossible = assessments.reduce((sum, a) => sum + a.max_score, 0);
    const totalObtained = assessments.reduce((sum, a) => sum + a.score, 0);
    const percentage = totalPossible > 0 ? ((totalObtained / totalPossible) * 100).toFixed(1) : 0;
    const grade = calculateGrade(percentage);
    
    let previewHtml = `
        <div class="row">
            <div class="col-12">
                <h6>Assessment Results for {{ $student->name }}</h6>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Assessment</th>
                            <th>Max Score</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>GPA</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    assessments.forEach(assessment => {
        previewHtml += `
            <tr>
                <td>${assessment.name}</td>
                <td>${assessment.max_score}</td>
                <td>${assessment.score}</td>
                <td><span class="badge ${assessment.percentage >= 50 ? 'bg-success' : 'bg-warning'}">${assessment.percentage}%</span></td>
            </tr>
        `;
    });
    
    previewHtml += `
                    </tbody>
                </table>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <strong>Total Obtained:</strong> ${totalObtained}
                    </div>
                    <div class="col-md-3">
                        <strong>Percentage:</strong> ${percentage}%
                    </div>
                    <div class="col-md-3">
                        <strong>Grade:</strong> <span class="badge bg-primary">${grade}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>GPA:</strong> ${gradePoint}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewHtml;
    $('#previewModal').modal('show');
}

function submitForm() {
    // Prepare assessment data for submission
    const configRows = document.querySelectorAll('#assessmentHeaders .row');
    const assessments = [];
    
    configRows.forEach(row => {
        const name = row.querySelector('.assessment-name').value;
        const maxScore = parseFloat(row.querySelector('.assessment-max').value) || 0;
        const score = parseFloat(row.querySelector('.assessment-score').value) || 0;
        
        if (name && maxScore > 0) {
            assessments.push({
                name: name,
                max_score: maxScore,
                score: score
            });
        }
    });
    
    // Add hidden inputs for assessments
    const form = document.getElementById('examResultsForm');
    
    // Remove existing assessment inputs
    const existingInputs = form.querySelectorAll('input[name^="assessments"]');
    existingInputs.forEach(input => input.remove());
    
    // Add new assessment inputs
    assessments.forEach((assessment, index) => {
        Object.keys(assessment).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `assessments[${index}][${key}]`;
            input.value = assessment[key];
            form.appendChild(input);
        });
    });
    form.submit();
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

.assessment-name, .assessment-max, .assessment-score {
    font-size: 0.9em;
}

#assessmentHeaders .row {
    margin-bottom: 0.5rem;
}

#assessmentHeaders .row:last-child {
    margin-bottom: 0;
}
</style>
@endsection
