@extends('layouts.staff-course')

@section('title', 'My Courses | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>My Courses</h1>
    <p>Select a course to manage its content, announcements, and students</p>
</div>

<!-- Course Selection -->
<div class="course-selection">
    <h5><i class="fas fa-book"></i> Select Course and Class</h5>
    <div class="row">
        <div class="col-md-6">
            <label for="courseSelect" class="form-label">Subject</label>
            <select class="form-select" id="courseSelect">
                <option value="">Choose a subject...</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->code }}">{{ $subject->name }} ({{ $subject->code }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="classSelect" class="form-label">Class</label>
            <select class="form-select" id="classSelect" disabled>
                <option value="">Choose a class...</option>
            </select>
        </div>
    </div>
    
    <!-- Course Info Display -->
    <div id="courseInfo" class="course-info" style="display: none;">
        <h4 id="selectedCourseName"></h4>
        <p id="selectedClassInfo"></p>
    </div>
    
    <!-- Action Buttons -->
    <div id="actionButtons" class="action-buttons" style="display: none;">
        <a href="/maintenance" class="action-btn primary">
            <i class="fas fa-plus"></i>
            Add Content
        </a>
        <a href="/maintenance" class="action-btn success">
            <i class="fas fa-bullhorn"></i>
            Add Announcement
        </a>
        <a href="/maintenance" class="action-btn warning">
            <i class="fas fa-clipboard"></i>
            Create Assignment
        </a>
        <a href="/maintenance" class="action-btn info">
            <i class="fas fa-users"></i>
            View Students
        </a>
        <a href="/maintenance" class="action-btn danger">
            <i class="fas fa-chart-bar"></i>
            View Analytics
        </a>
    </div>
</div>

<!-- Student List Section -->
<div id="studentListSection" class="student-list-section" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-users"></i> Class Students</h5>
            <span id="studentCount" class="badge badge-primary">0 Students</span>
        </div>
        <div class="card-body">
            <div id="studentListContent">
                <!-- Student list will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- My Subjects Cards -->
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
                        <span class="badge badge-enrolled">Active</span>
                    </div>
                </div>
                <div class="course-body">
                    <h5 class="course-title">{{ $subject->name }}</h5>
                    <p class="course-code">{{ $subject->code }}</p>
                    <p class="course-description">{{ Str::limit($subject->description, 80) }}</p>
                    <div class="course-meta">
                        <div class="course-duration">
                            <i class="fas fa-clock"></i>
                            <span>{{ $subjectClasses->count() }} Classes</span>
                        </div>
                        <div class="course-level">
                            <i class="fas fa-users"></i>
                            <span>{{ $totalStudents }} Students</span>
                        </div>
                    </div>
                </div>
                <div class="course-footer">
                    <div class="course-actions">
                        <button class="btn btn-sm btn-primary" onclick="selectSubject('{{ $subject->code }}')">Select Course</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>


@push('styles')
<style>
.course-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.course-header {
    background: linear-gradient(135deg, #0056d2, #0041a3);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.course-icon {
    color: white;
    font-size: 1.5rem;
}

.course-status .badge-enrolled {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.course-body {
    padding: 1.5rem;
    flex-grow: 1;
}

.course-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.25rem;
    font-size: 1.1rem;
}

.course-code {
    color: #0056d2;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 0.75rem;
}

.course-description {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.course-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.course-duration, .course-level {
    display: flex;
    align-items: center;
    color: #718096;
    font-size: 0.85rem;
}

.course-duration i, .course-level i {
    margin-right: 0.25rem;
}

.course-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.course-actions {
    text-align: center;
}

/* Student List Section Styles */
.student-list-section {
    margin-top: 2rem;
}

.student-list-section .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}

.student-list-section .card-header h5 {
    margin: 0;
    font-weight: 600;
}

.student-list-section .badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.student-list-section .table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.student-list-section .table td {
    vertical-align: middle;
}

.student-list-section .badge-success {
    background-color: #28a745;
}

.student-list-section .badge-warning {
    background-color: #ffc107;
    color: #212529;
}

@media (max-width: 768px) {
    .course-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .student-list-section .table-responsive {
        font-size: 0.9rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Course and Class Selection Logic
    const courseSelect = document.getElementById('courseSelect');
    const classSelect = document.getElementById('classSelect');
    const courseInfo = document.getElementById('courseInfo');
    const actionButtons = document.getElementById('actionButtons');
    
    // Dynamic subject classes from backend
    const subjectClasses = {
        @foreach($subjects as $subject)
        '{{ $subject->code }}': [
            @foreach($subject->classSchedules->where('lecturer_id', $lecturer->id) as $class)
            { value: '{{ $class->class_code }}', text: '{{ $class->class_name }} ({{ $class->class_code }})' },
            @endforeach
        ],
        @endforeach
    };
    
    const subjectNames = {
        @foreach($subjects as $subject)
        '{{ $subject->code }}': '{{ $subject->name }}',
        @endforeach
    };
    
    courseSelect.addEventListener('change', function() {
        const selectedSubject = this.value;
        classSelect.innerHTML = '<option value="">Choose a class...</option>';
        
        if (selectedSubject && subjectClasses[selectedSubject]) {
            classSelect.disabled = false;
            subjectClasses[selectedSubject].forEach(classOption => {
                const option = document.createElement('option');
                option.value = classOption.value;
                option.textContent = classOption.text;
                classSelect.appendChild(option);
            });
            
            // Show course info
            courseInfo.style.display = 'block';
            document.getElementById('selectedCourseName').textContent = subjectNames[selectedSubject];
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
            
            // Hide action buttons until class is selected
            actionButtons.style.display = 'none';
        } else {
            classSelect.disabled = true;
            courseInfo.style.display = 'none';
            actionButtons.style.display = 'none';
        }
    });
    
    classSelect.addEventListener('change', function() {
        const selectedClass = this.value;
        if (selectedClass) {
            // Update course info
            const selectedSubject = courseSelect.value;
            document.getElementById('selectedClassInfo').textContent = `Selected: ${this.options[this.selectedIndex].text}`;
            
            // Show action buttons
            actionButtons.style.display = 'flex';
            
            // Store selected subject and class in session storage
            sessionStorage.setItem('selectedSubject', selectedSubject);
            sessionStorage.setItem('selectedClass', selectedClass);
            
            // Load students for the selected class
            loadClassStudents(selectedClass);
            
            console.log(`Selected: ${subjectNames[selectedSubject]} - ${this.options[this.selectedIndex].text}`);
        } else {
            actionButtons.style.display = 'none';
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
            hideStudentList();
        }
    });
});

// Function to load students for a selected class
function loadClassStudents(classCode) {
    const studentListSection = document.getElementById('studentListSection');
    const studentListContent = document.getElementById('studentListContent');
    const studentCount = document.getElementById('studentCount');
    
    // Show loading state
    studentListContent.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading students...</div>';
    studentListSection.style.display = 'block';
    
    // Make AJAX request to get students
    fetch('{{ route("staff.courses.class-students") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            class_code: classCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            studentListContent.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            return;
        }
        
        // Update student count
        studentCount.textContent = `${data.student_count} Students`;
        
        // Display students
        if (data.students && data.students.length > 0) {
            let studentsHtml = '<div class="table-responsive"><table class="table table-hover">';
            studentsHtml += '<thead><tr><th>Student ID</th><th>Name</th><th>Email</th><th>Enrollment Date</th><th>Status</th></tr></thead><tbody>';
            
            data.students.forEach(student => {
                studentsHtml += `
                    <tr>
                        <td>${student.user.student ? student.user.student.student_id : 'N/A'}</td>
                        <td>${student.user.name}</td>
                        <td>${student.user.email}</td>
                        <td>${new Date(student.enrollment_date).toLocaleDateString()}</td>
                        <td><span class="badge badge-${student.status === 'enrolled' ? 'success' : 'warning'}">${student.status}</span></td>
                    </tr>
                `;
            });
            
            studentsHtml += '</tbody></table></div>';
            studentListContent.innerHTML = studentsHtml;
        } else {
            studentListContent.innerHTML = '<div class="alert alert-info">No students enrolled in this class.</div>';
        }
    })
    .catch(error => {
        console.error('Error loading students:', error);
        studentListContent.innerHTML = '<div class="alert alert-danger">Error loading students. Please try again.</div>';
    });
}

// Function to hide student list
function hideStudentList() {
    const studentListSection = document.getElementById('studentListSection');
    studentListSection.style.display = 'none';
}

// Function to select subject from card
function selectSubject(subjectCode) {
    const courseSelect = document.getElementById('courseSelect');
    courseSelect.value = subjectCode;
    courseSelect.dispatchEvent(new Event('change'));
}
</script>
@endpush
@endsection
