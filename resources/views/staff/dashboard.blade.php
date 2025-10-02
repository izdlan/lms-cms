@extends('layouts.staff-course')

@section('title', 'Dashboard | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>Lecturer Dashboard</h1>
    <p>Manage your courses, classes, and students</p>
</div>

<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-content">
        <h1>Welcome, {{ $user->name }}!</h1>
        <p>Lecturer Dashboard - Manage your courses, classes, and students</p>
    </div>
</div>

<!-- My Subjects Section -->
<div class="my-subjects-section">
    <h5><i class="fas fa-book"></i> My Subjects</h5>
    <div class="row">
        @foreach($subjects as $subject)
            @php
                $subjectClasses = $subject->classSchedules->where('lecturer_id', $lecturer->id);
                $totalStudents = \App\Models\StudentEnrollment::whereIn('class_code', $subjectClasses->pluck('class_code'))->count();
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="course-status">
                            <span class="badge badge-active">Active</span>
                        </div>
                    </div>
                    <div class="course-content">
                        <h6 class="course-title">{{ $subject->name }}</h6>
                        <p class="course-code">{{ $subject->code }}</p>
                        <p class="course-description">{{ Str::limit($subject->description, 80) }}</p>
                        <div class="course-stats">
                            <div class="stat-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $subjectClasses->count() }} Classes</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $totalStudents }} Students</span>
                            </div>
                        </div>
                        <div class="course-actions">
                            <button class="btn btn-primary btn-sm select-course-btn" 
                                    data-subject-code="{{ $subject->code }}" 
                                    data-subject-name="{{ $subject->name }}">
                                Select Course
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Class Selection Modal -->
<div class="modal fade" id="classSelectionModal" tabindex="-1" aria-labelledby="classSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classSelectionModalLabel">Select Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="selectedSubjectInfo" class="mb-3">
                    <h6 id="modalSubjectName"></h6>
                    <p id="modalSubjectCode" class="text-muted"></p>
                </div>
                <div class="row" id="classOptions">
                    <!-- Class options will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="content-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $totalSubjects }}</h3>
                    <p>My Subjects</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="content-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $totalClasses }}</h3>
                    <p>Classes</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="content-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $totalStudents }}</h3>
                    <p>Total Students</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="content-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="fas fa-file-text"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $totalMaterials }}</h3>
                    <p>Course Materials</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="content-card">
    <h5>Quick Actions</h5>
    <div class="row">
        <div class="col-md-3">
            <a href="{{ route('staff.courses') }}" class="action-btn primary">
                <i class="fas fa-book"></i>
                Manage Courses
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('staff.announcements') }}" class="action-btn success">
                <i class="fas fa-megaphone"></i>
                Course Announcements
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('staff.contents') }}" class="action-btn info">
                <i class="fas fa-upload"></i>
                Course Materials
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Staff Dashboard Styling */
.courses-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0.5rem;
    text-align: center;
}

.courses-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.courses-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

/* Welcome Section */
.welcome-section {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
}

.welcome-content h1 {
    color: #2c3e50;
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.welcome-content p {
    color: #6c757d;
    margin: 0;
    font-size: 1rem;
}

.content-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #dee2e6;
}

.dashboard-header h1 {
    color: #2c3e50;
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.dashboard-header p {
    color: #6c757d;
    margin: 0;
}

.course-selection {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #dee2e6;
}

.course-selection h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.course-selection h5 i {
    color: #0056d2;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-select {
    border: 2px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:focus {
    border-color: #0056d2;
    box-shadow: 0 0 0 0.2rem rgba(0, 86, 210, 0.25);
    outline: 0;
}

.course-info {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 1rem;
}

.course-info h4 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.course-info p {
    color: #6c757d;
    margin: 0;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.action-btn.primary {
    background: linear-gradient(135deg, #0056d2, #0041a3);
    color: white;
}

.action-btn.primary:hover {
    background: linear-gradient(135deg, #0041a3, #003d99);
    color: white;
}

.action-btn.success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
}

.action-btn.success:hover {
    background: linear-gradient(135deg, #1e7e34, #1c7430);
    color: white;
}

.action-btn.info {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
}

.action-btn.info:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    color: white;
}

.action-btn.warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #212529;
}

.action-btn.warning:hover {
    background: linear-gradient(135deg, #e0a800, #d39e00);
    color: #212529;
}

.stats-icon {
    width: 70px;
    height: 70px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1.5rem;
    font-size: 28px;
    color: white;
    background: linear-gradient(135deg, #0056d2, #0041a3);
    box-shadow: 0 4px 15px rgba(0, 86, 210, 0.3);
}

.stats-content h3 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    line-height: 1;
}

.stats-content p {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* My Subjects Section */
.my-subjects-section {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
}

.my-subjects-section h5 {
    color: #2c3e50;
    font-weight: 700;
    font-size: 1.3rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.my-subjects-section h5 i {
    color: #0056d2;
    font-size: 1.5rem;
    background: #f0f2ff;
    padding: 0.75rem;
    border-radius: 0.75rem;
}

/* Course Cards */
.course-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.course-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.15);
    border-color: #0056d2;
}

.course-header {
    background: linear-gradient(135deg, #0056d2, #0041a3);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.course-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
    pointer-events: none;
}

.course-icon {
    font-size: 1.5rem;
}

.course-status .badge-active {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
}

.course-content {
    padding: 1.75rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.course-title {
    color: #2c3e50;
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.course-code {
    color: #0056d2;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    font-weight: 600;
    background: #f0f2ff;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    display: inline-block;
    width: fit-content;
}

.course-description {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    flex-grow: 1;
}

.course-stats {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.75rem;
    border: 1px solid #e9ecef;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #495057;
    font-size: 0.9rem;
    font-weight: 500;
}

.stat-item i {
    color: #0056d2;
    font-size: 1rem;
}

.course-actions {
    margin-top: auto;
}

.select-course-btn {
    width: 100%;
    background: linear-gradient(135deg, #0056d2, #0041a3);
    border: none;
    color: white;
    padding: 0.875rem 1.25rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.select-course-btn:hover {
    background: linear-gradient(135deg, #0041a3, #003d99);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 86, 210, 0.4);
}

/* Modal Styling */
.modal-content {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.modal-header {
    background: linear-gradient(135deg, #0056d2, #0041a3);
    color: white;
    border-radius: 0.75rem 0.75rem 0 0;
    border-bottom: none;
}

.modal-title {
    font-weight: 600;
}

.btn-close {
    filter: invert(1);
}

.class-option {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.class-option:hover {
    border-color: #0056d2;
    background: #f0f2ff;
}

.class-option.selected {
    border-color: #0056d2;
    background: #f0f2ff;
}

.class-option h6 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.class-option p {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .courses-header h1 {
        font-size: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Course Card Selection Logic
    const selectCourseBtns = document.querySelectorAll('.select-course-btn');
    const classSelectionModal = new bootstrap.Modal(document.getElementById('classSelectionModal'));
    
    // Subject classes data from backend
    const subjectClasses = {!! json_encode($subjects->mapWithKeys(function($subject) use ($lecturer) {
        return [
            $subject->code => $subject->classSchedules->where('lecturer_id', $lecturer->id)->map(function($class) {
                return [
                    'classCode' => $class->class_code,
                    'className' => $class->class_name,
                    'description' => $class->description,
                    'venue' => $class->venue,
                    'dayOfWeek' => $class->day_of_week,
                    'startTime' => $class->start_time,
                    'endTime' => $class->end_time
                ];
            })->values()->toArray()
        ];
    })) !!};
    
    const subjectNames = {!! json_encode($subjects->pluck('name', 'code')) !!};
    
    selectCourseBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const subjectCode = this.dataset.subjectCode;
            const subjectName = this.dataset.subjectName;
            
            // Update modal title and subject info
            document.getElementById('modalSubjectName').textContent = subjectName;
            document.getElementById('modalSubjectCode').textContent = subjectCode;
            
            // Populate class options
            const classOptionsContainer = document.getElementById('classOptions');
            classOptionsContainer.innerHTML = '';
            
            if (subjectClasses[subjectCode] && subjectClasses[subjectCode].length > 0) {
                subjectClasses[subjectCode].forEach(classData => {
                    const classOption = document.createElement('div');
                    classOption.className = 'col-md-6 class-option';
                    classOption.innerHTML = `
                        <h6>${classData.className}</h6>
                        <p><strong>Code:</strong> ${classData.classCode}</p>
                        <p><strong>Schedule:</strong> ${classData.dayOfWeek} ${classData.startTime} - ${classData.endTime}</p>
                        <p><strong>Venue:</strong> ${classData.venue}</p>
                    `;
                    
                    classOption.addEventListener('click', function() {
                        // Remove selected class from other options
                        document.querySelectorAll('.class-option').forEach(opt => opt.classList.remove('selected'));
                        this.classList.add('selected');
                        
                        // Store selected subject and class
                        sessionStorage.setItem('selectedSubject', subjectCode);
                        sessionStorage.setItem('selectedClass', classData.classCode);
                        
                        // Close modal and redirect to management page
                        classSelectionModal.hide();
                        
                        // Redirect to announcements page for now (you can change this)
                        window.location.href = `/staff/announcements/${subjectCode}/${classData.classCode}`;
                    });
                    
                    classOptionsContainer.appendChild(classOption);
                });
            } else {
                classOptionsContainer.innerHTML = '<div class="col-12"><p class="text-muted">No classes available for this subject.</p></div>';
            }
            
            // Show modal
            classSelectionModal.show();
        });
    });
    
    // Handle modal events
    document.getElementById('classSelectionModal').addEventListener('hidden.bs.modal', function() {
        // Clear selections when modal is closed
        document.querySelectorAll('.class-option').forEach(opt => opt.classList.remove('selected'));
    });
});
</script>
@endpush
@endsection
