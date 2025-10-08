@extends('layouts.app')

@section('content')
<div class="container-fluid exam-results-page">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Exam Results</li>
                    </ol>
                </div>
                <h4 class="page-title">Exam Results</h4>
            </div>
        </div>
    </div>
    <!-- Academic Year Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.exam-results') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="year" class="form-label">Academic Year</label>
                            <select name="year" id="year" class="form-select">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Filter Results</button>
                                <a href="{{ route('student.exam-results') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall GPA Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-dark">{{ $currentYear }} Academic Year</h3>
                    <h1 class="display-4 mb-0 text-dark fw-bold">{{ number_format($overallGpa, 2) }}</h1>
                    <p class="mb-0 text-muted">Overall GPA (12 Subjects)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Subject Results -->
    @foreach($enrolledSubjects as $enrollment)
        @if($enrollment && is_object($enrollment))
            @php
                $result = $examResults->get($enrollment->subject_code);
                $assessments = $result ? $result->assessments : [];
            @endphp
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0 text-dark">
                            {{ $enrollment->subject_code }} - {{ $enrollment->subject->name ?? 'N/A' }}
                        </h5>
                        <small class="text-muted">
                            Lecturer: {{ $enrollment->lecturer->name ?? 'N/A' }} | 
                            Credit Hours: {{ $enrollment->subject->credit_hours ?? 'N/A' }}
                        </small>
                    </div>
                    <div class="card-body">
                        @if($result && $assessments)
                            <!-- Assessment Results -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Assessment Results</h6>
                                    <div class="row">
                                        @foreach($assessments as $assessment)
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title text-dark">{{ $assessment['name'] ?? 'Assessment' }}</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-dark fs-6">
                                                                {{ $assessment['score'] ?? '-' }} / {{ $assessment['max_score'] ?? '-' }}
                                                            </span>
                                                            <small class="text-muted">
                                                                {{ isset($assessment['percentage']) ? number_format($assessment['percentage'], 1) . '%' : '' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Summary Results -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-muted">Total Marks</h6>
                                            <h4 class="text-dark fw-bold">{{ $result->total_marks ?? '-' }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-muted">Percentage</h6>
                                            <h4 class="text-dark fw-bold">
                                                {{ number_format($result->percentage, 1) }}%
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-muted">Grade</h6>
                                            <h4><span class="badge bg-dark fs-6">{{ $result->grade ?? '-' }}</span></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-muted">GPA</h6>
                                            <h4 class="text-dark fw-bold">{{ number_format($result->gpa, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($result->notes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-light border">
                                            <h6 class="alert-heading text-dark">Lecturer Notes:</h6>
                                            <p class="mb-0 text-dark">{{ $result->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- No Results Available -->
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Results Available</h5>
                                <p class="text-muted">Results for this subject will be published by your lecturer once they are finalized.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Skip invalid enrollment records -->
        @endif
    @endforeach

    <!-- No Results Message -->
    @if($examResults->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Exam Results Available</h4>
                        <p class="text-muted">Your exam results for {{ $currentYear }} will be published by your lecturers once they are finalized.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Professional styling for exam results page - Override any conflicting styles */
.exam-results-page {
    background-color: #f8f9fa !important;
}

.exam-results-page .card {
    border: none !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    transition: box-shadow 0.15s ease-in-out;
    background-color: #ffffff !important;
}

.exam-results-page .card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.exam-results-page .card-header {
    background-color: #ffffff !important;
    border-bottom: 1px solid #e9ecef !important;
    font-weight: 600 !important;
    color: #212529 !important;
}

.exam-results-page .page-title-box {
    margin-bottom: 2rem !important;
    padding-bottom: 1rem !important;
    border-bottom: 1px solid #e9ecef !important;
}

.exam-results-page .display-4 {
    font-weight: 700 !important;
    color: #212529 !important;
}

.exam-results-page .badge {
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    padding: 0.5rem 0.75rem !important;
    background-color: #495057 !important;
    color: #ffffff !important;
}

.exam-results-page .alert {
    border: 1px solid #e9ecef !important;
    background-color: #ffffff !important;
    color: #212529 !important;
}

.exam-results-page .btn-primary {
    background-color: #495057 !important;
    border-color: #495057 !important;
    color: #ffffff !important;
}

.exam-results-page .btn-primary:hover {
    background-color: #343a40 !important;
    border-color: #343a40 !important;
    color: #ffffff !important;
}

.exam-results-page .btn-outline-secondary {
    color: #6c757d !important;
    border-color: #6c757d !important;
    background-color: transparent !important;
}

.exam-results-page .btn-outline-secondary:hover {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #ffffff !important;
}

/* Clean table styling */
.exam-results-page .table th {
    background-color: #f8f9fa !important;
    color: #495057 !important;
    border-color: #dee2e6 !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.exam-results-page .table td {
    vertical-align: middle !important;
    border-color: #e9ecef !important;
    color: #212529 !important;
}

/* Professional form styling */
.exam-results-page .form-select, 
.exam-results-page .form-control {
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    background-color: #ffffff !important;
    color: #212529 !important;
}

.exam-results-page .form-select:focus, 
.exam-results-page .form-control:focus {
    border-color: #495057 !important;
    box-shadow: 0 0 0 0.2rem rgba(73, 80, 87, 0.25) !important;
}

/* Clean breadcrumb */
.exam-results-page .breadcrumb {
    background-color: transparent !important;
    padding: 0 !important;
    margin: 0 !important;
}

.exam-results-page .breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d !important;
}

.exam-results-page .breadcrumb-item a {
    color: #495057 !important;
    text-decoration: none !important;
}

.exam-results-page .breadcrumb-item.active {
    color: #6c757d !important;
}

/* Professional spacing */
.exam-results-page .mb-4 {
    margin-bottom: 2rem !important;
}

.exam-results-page .py-5 {
    padding-top: 3rem !important;
    padding-bottom: 3rem !important;
}

/* Override any colorful text classes */
.exam-results-page .text-primary,
.exam-results-page .text-success,
.exam-results-page .text-danger,
.exam-results-page .text-warning,
.exam-results-page .text-info {
    color: #212529 !important;
}

/* Override any colorful background classes */
.exam-results-page .bg-primary,
.exam-results-page .bg-success,
.exam-results-page .bg-danger,
.exam-results-page .bg-warning,
.exam-results-page .bg-info {
    background-color: #ffffff !important;
    color: #212529 !important;
}

/* Ensure all text is professional */
.exam-results-page h1,
.exam-results-page h2,
.exam-results-page h3,
.exam-results-page h4,
.exam-results-page h5,
.exam-results-page h6 {
    color: #212529 !important;
}

.exam-results-page p,
.exam-results-page span,
.exam-results-page div {
    color: #212529 !important;
}

.exam-results-page .text-muted {
    color: #6c757d !important;
}
</style>
@endsection

