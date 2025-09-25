@extends('layouts.course')

@section('title', 'Course Summary')

@section('content')
<div class="dashboard-header">
    <h1>Course Summary</h1>
    <p class="text-muted">{{ $courseInfo['title'] ?? 'Course Title' }}</p>
</div>

<div class="row">
    <!-- Course Basic Information -->
    <div class="col-12">
        <div class="course-card">
            <div class="course-card-header">
                <h5>{{ $courseInfo['name'] }}</h5>
                <i class="fas fa-minus"></i>
            </div>
            <div class="course-card-body">
                <div class="course-info-item">
                    <span class="course-info-label">Course Name:</span>
                    <span class="course-info-value">{{ $courseInfo['name'] }}</span>
                </div>
                <div class="course-info-item">
                    <span class="course-info-label">MOOC Title:</span>
                    <span class="course-info-value">{{ $courseInfo['mooc_title'] }}</span>
                </div>
                <div class="course-info-item">
                    <span class="course-info-label">Introductory Video:</span>
                    <span class="course-info-value text-muted">-- No Introductory Video Currently Available --</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon resource-person">
                <i class="fas fa-user"></i>
            </div>
            <div class="stats-label">Resource Person</div>
            <div class="stats-number">{{ $courseInfo['instructor'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon instructors">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number">{{ $courseInfo['total_instructors'] }}</div>
            <div class="stats-label">Total Instructors</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon students">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stats-number">{{ $courseInfo['total_students'] }}</div>
            <div class="stats-label">Total Students</div>
        </div>
    </div>
</div>

<!-- Course Details Tabs -->
<div class="row">
    <div class="col-12">
        <div class="content-section">
            <ul class="nav nav-tabs" id="courseTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">
                        Course Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="skills-tab" data-bs-toggle="tab" data-bs-target="#skills" type="button" role="tab" aria-controls="skills" aria-selected="false">
                        Transferable Skills
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="outcomes-tab" data-bs-toggle="tab" data-bs-target="#outcomes" type="button" role="tab" aria-controls="outcomes" aria-selected="false">
                        Course Learning Outcomes
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="courseTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <h3>Course Description</h3>
                    <p>{{ $courseInfo['description'] }}</p>
                    <p>This subject is designed to inculcate the entrepreneurial skills among science and technology cluster students and promote the development of technology-based entrepreneurship knowledge. The course delivery combines both theoretical and practical aspects of technology entrepreneurship.</p>
                    <p>Theoretical aspect is looking at the important elements in understanding technology entrepreneurship, including opportunity recognition, business model development, and venture creation. Practical aspect involves hands-on activities such as business plan development, market research, and prototype development.</p>
                </div>
                
                <div class="tab-pane fade" id="skills" role="tabpanel" aria-labelledby="skills-tab">
                    <h3>Transferable Skills</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            Critical thinking and problem-solving
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            Business planning and strategy development
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            Market research and analysis
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            Project management and leadership
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            Communication and presentation skills
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            Innovation and creativity
                        </li>
                    </ul>
                </div>
                
                <div class="tab-pane fade" id="outcomes" role="tabpanel" aria-labelledby="outcomes-tab">
                    <h3>Course Learning Outcomes</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">CLO1</h5>
                                    <p class="card-text">Demonstrate understanding of technology entrepreneurship concepts and principles.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">CLO2</h5>
                                    <p class="card-text">Apply entrepreneurial skills in developing technology-based business ideas.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">CLO3</h5>
                                    <p class="card-text">Create comprehensive business plans for technology ventures.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">CLO4</h5>
                                    <p class="card-text">Evaluate market opportunities and competitive advantages.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any additional functionality
    console.log('Course Summary page loaded');
});
</script>
@endpush
