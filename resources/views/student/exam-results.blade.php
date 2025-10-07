@extends('layouts.app')

@section('content')
<div class="container-fluid">
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
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $currentYear }} Academic Year</h3>
                    <h1 class="display-4 mb-0">{{ number_format($overallGpa, 2) }}</h1>
                    <p class="mb-0">Overall GPA (12 Subjects)</p>
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
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            {{ $enrollment->subject_code }} - {{ $enrollment->subject->name ?? 'N/A' }}
                        </h5>
                        <small class="text-white-50">
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
                                                <div class="card border">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">{{ $assessment['name'] ?? 'Assessment' }}</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-{{ $assessment['score'] >= ($assessment['max_score'] * 0.5) ? 'success' : 'warning' }} fs-6">
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
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Marks</h6>
                                            <h4 class="text-primary">{{ $result->total_marks ?? '-' }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Percentage</h6>
                                            <h4 class="text-{{ $result->percentage >= 50 ? 'success' : 'danger' }}">
                                                {{ number_format($result->percentage, 1) }}%
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Grade</h6>
                                            @php
                                                $gradeColor = 'secondary';
                                                if($result->grade) {
                                                    switch($result->grade) {
                                                        case 'A+':
                                                        case 'A':
                                                        case 'A-':
                                                            $gradeColor = 'success';
                                                            break;
                                                        case 'B+':
                                                        case 'B':
                                                        case 'B-':
                                                            $gradeColor = 'primary';
                                                            break;
                                                        case 'C+':
                                                        case 'C':
                                                        case 'C-':
                                                            $gradeColor = 'warning';
                                                            break;
                                                        case 'D+':
                                                        case 'D':
                                                            $gradeColor = 'danger';
                                                            break;
                                                        case 'F':
                                                            $gradeColor = 'dark';
                                                            break;
                                                    }
                                                }
                                            @endphp
                                            <h4><span class="badge bg-{{ $gradeColor }} fs-6">{{ $result->grade ?? '-' }}</span></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">GPA</h6>
                                            <h4 class="text-info">{{ number_format($result->gpa, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($result->notes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h6 class="alert-heading">Lecturer Notes:</h6>
                                            <p class="mb-0">{{ $result->notes }}</p>
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

.display-4 {
    font-weight: 700;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}
</style>
@endsection

