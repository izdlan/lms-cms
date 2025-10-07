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

    <!-- Filter Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.exam-results') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="year" class="form-label">Academic Year</label>
                            <select name="year" id="year" class="form-select">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="semester" class="form-label">Semester</label>
                            <select name="semester" id="semester" class="form-select">
                                @foreach($availableSemesters as $semester)
                                    <option value="{{ $semester }}" {{ $semester == $currentSemester ? 'selected' : '' }}>
                                        {{ $semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
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
                    <h3 class="mb-1">{{ $currentYear }} - {{ $currentSemester }}</h3>
                    <h1 class="display-4 mb-0">{{ number_format($overallGpa, 2) }}</h1>
                    <p class="mb-0">Overall GPA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Results by Program -->
    @foreach($enrolledSubjects as $programCode => $subjects)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ strtoupper($programCode) }} Program Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">Subject Code</th>
                                        <th rowspan="2" class="text-center align-middle">Subject Name</th>
                                        <th rowspan="2" class="text-center align-middle">Lecturer</th>
                                        <th rowspan="2" class="text-center align-middle">Credit Hours</th>
                                        
                                        <!-- Dynamic Assessment Columns -->
                                        @if($subjects->count() > 0)
                                            @php
                                                $firstSubject = $subjects->first();
                                                $sampleResult = $firstSubject ? $examResults->get($firstSubject->subject_code) : null;
                                                $assessments = $sampleResult ? $sampleResult->assessments : [];
                                            @endphp
                                            
                                            @if($assessments)
                                                @foreach($assessments as $assessment)
                                                    <th class="text-center">{{ $assessment['name'] ?? 'Assessment' }}</th>
                                                @endforeach
                                            @else
                                                <!-- Default assessment columns if no data -->
                                                <th class="text-center">Quiz</th>
                                                <th class="text-center">Assignment 1</th>
                                                <th class="text-center">Assignment 2</th>
                                                <th class="text-center">Midterm</th>
                                                <th class="text-center">Final Exam</th>
                                            @endif
                                        @endif
                                        
                                        <th rowspan="2" class="text-center align-middle">Total</th>
                                        <th rowspan="2" class="text-center align-middle">Percentage</th>
                                        <th rowspan="2" class="text-center align-middle">Grade</th>
                                        <th rowspan="2" class="text-center align-middle">GPA</th>
                                    </tr>
                                    <tr>
                                        @if($subjects->count() > 0)
                                            @php
                                                $firstSubject = $subjects->first();
                                                $sampleResult = $firstSubject ? $examResults->get($firstSubject->subject_code) : null;
                                                $assessments = $sampleResult ? $sampleResult->assessments : [];
                                            @endphp
                                            
                                            @if($assessments)
                                                @foreach($assessments as $assessment)
                                                    <th class="text-center small">
                                                        @if(isset($assessment['max_score']))
                                                            /{{ $assessment['max_score'] }}
                                                        @endif
                                                    </th>
                                                @endforeach
                                            @else
                                                <th class="text-center small">/10</th>
                                                <th class="text-center small">/20</th>
                                                <th class="text-center small">/20</th>
                                                <th class="text-center small">/30</th>
                                                <th class="text-center small">/40</th>
                                            @endif
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $enrollment)
                                        @if($enrollment && is_object($enrollment))
                                            @php
                                                $result = $examResults->get($enrollment->subject_code);
                                            @endphp
                                        <tr>
                                            <td class="text-center fw-bold">{{ $enrollment->subject_code }}</td>
                                            <td>{{ $enrollment->subject->name ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->lecturer->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $enrollment->subject->credit_hours ?? 'N/A' }}</td>
                                            
                                            <!-- Assessment Scores -->
                                            @if($result && $result->assessments)
                                                @foreach($result->assessments as $assessment)
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $assessment['score'] >= ($assessment['max_score'] * 0.5) ? 'success' : 'warning' }}">
                                                            {{ $assessment['score'] ?? '-' }}
                                                        </span>
                                                    </td>
                                                @endforeach
                                            @else
                                                <!-- Show empty cells for subjects without results -->
                                                @php
                                                    $firstSubject = $subjects->first();
                                                    $sampleResult = $firstSubject ? $examResults->get($firstSubject->subject_code) : null;
                                                    $assessments = $sampleResult ? $sampleResult->assessments : [];
                                                    $assessmentCount = $assessments ? count($assessments) : 5;
                                                @endphp
                                                @for($i = 0; $i < $assessmentCount; $i++)
                                                    <td class="text-center">-</td>
                                                @endfor
                                            @endif
                                            
                                            <!-- Summary Columns -->
                                            <td class="text-center fw-bold">
                                                @if($result)
                                                    {{ $result->total_marks ?? '-' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($result)
                                                    <span class="badge bg-{{ $result->percentage >= 50 ? 'success' : 'danger' }}">
                                                        {{ number_format($result->percentage, 1) }}%
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($result)
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
                                                    <span class="badge bg-{{ $gradeColor }}">
                                                        {{ $result->grade ?? '-' }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold">
                                                @if($result)
                                                    {{ number_format($result->gpa, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @else
                                        <!-- Skip invalid enrollment records -->
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- No Results Message -->
    @if($examResults->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Exam Results Available</h4>
                        <p class="text-muted">Your exam results for {{ $currentYear }} - {{ $currentSemester }} will be published by your lecturers once they are finalized.</p>
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

