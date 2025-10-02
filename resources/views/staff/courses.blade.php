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
/* Professional Course Cards */
.course-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e0;
}

.course-header {
    background: #ffffff;
    padding: 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
}

.course-icon {
    color: #718096;
    font-size: 1.25rem;
}

.course-status .badge-enrolled {
    background: #edf2f7;
    color: #4a5568;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.course-body {
    padding: 1.25rem;
    flex-grow: 1;
}

.course-title {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
    line-height: 1.4;
}

.course-code {
    color: #718096;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.75rem;
    background: #f7fafc;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    display: inline-block;
    border: 1px solid #e2e8f0;
}

.course-description {
    color: #718096;
    font-size: 0.875rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.course-meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #f7fafc;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}

.course-duration, .course-level {
    display: flex;
    align-items: center;
    color: #718096;
    font-size: 0.8rem;
    font-weight: 500;
}

.course-duration i, .course-level i {
    margin-right: 0.5rem;
    color: #a0aec0;
}

.course-footer {
    padding: 1rem 1.25rem;
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
}

.course-actions {
    text-align: center;
}

.course-actions .btn {
    background: #4a5568;
    border: 1px solid #4a5568;
    color: white;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.course-actions .btn:hover {
    background: #2d3748;
    border-color: #2d3748;
    color: white;
}

/* Professional Course Selection */
.course-selection {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    padding: 2rem;
    margin-bottom: 2rem;
}

.course-selection h5 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
}

.course-selection h5 i {
    color: #718096;
    font-size: 1.25rem;
}

.form-label {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-select {
    border: 1px solid #cbd5e0;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
    background: white;
}

.form-select:focus {
    border-color: #718096;
    box-shadow: 0 0 0 3px rgba(113, 128, 150, 0.1);
    outline: none;
}

.course-info {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1.25rem;
    margin-top: 1.5rem;
}

.course-info h4 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.course-info p {
    color: #718096;
    margin: 0;
    font-size: 0.875rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.action-btn:hover {
    transform: translateY(-1px);
    text-decoration: none;
}

.action-btn.primary {
    background: #4a5568;
    color: white;
    border-color: #4a5568;
}

.action-btn.primary:hover {
    background: #2d3748;
    border-color: #2d3748;
    color: white;
}

.action-btn.success {
    background: #68d391;
    color: #2d3748;
    border-color: #68d391;
}

.action-btn.success:hover {
    background: #48bb78;
    border-color: #48bb78;
    color: #2d3748;
}

.action-btn.warning {
    background: #f6e05e;
    color: #2d3748;
    border-color: #f6e05e;
}

.action-btn.warning:hover {
    background: #ed8936;
    border-color: #ed8936;
    color: white;
}

.action-btn.info {
    background: #90cdf4;
    color: #2d3748;
    border-color: #90cdf4;
}

.action-btn.info:hover {
    background: #63b3ed;
    border-color: #63b3ed;
    color: #2d3748;
}

.action-btn.danger {
    background: #feb2b2;
    color: #2d3748;
    border-color: #feb2b2;
}

.action-btn.danger:hover {
    background: #fc8181;
    border-color: #fc8181;
    color: #2d3748;
}

/* Professional Student List Section */
.student-list-section {
    margin-top: 2rem;
}

.student-list-section .card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.student-list-section .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f7fafc;
    color: #4a5568;
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem;
}

.student-list-section .card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.student-list-section .card-header h5 i {
    color: #718096;
}

.student-list-section .badge {
    font-size: 0.8rem;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    background: #edf2f7;
    color: #4a5568;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.student-list-section .table th {
    background-color: #f7fafc;
    border-top: none;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.875rem;
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}

.student-list-section .table td {
    vertical-align: middle;
    padding: 0.75rem;
    font-size: 0.875rem;
    color: #718096;
    border-bottom: 1px solid #f1f5f9;
}

.student-list-section .badge-success {
    background-color: #c6f6d5;
    color: #2f855a;
    border: 1px solid #9ae6b4;
}

.student-list-section .badge-warning {
    background-color: #faf089;
    color: #744210;
    border: 1px solid #f6e05e;
}

/* Professional Header */
.courses-header {
    background: #f7fafc;
    color: #4a5568;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #e2e8f0;
}

.courses-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #2d3748;
}

.courses-header p {
    font-size: 1rem;
    color: #718096;
    margin: 0;
}

@media (max-width: 768px) {
    .course-meta {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        justify-content: center;
    }
    
    .student-list-section .table-responsive {
        font-size: 0.8rem;
    }
    
    .courses-header h1 {
        font-size: 1.5rem;
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
