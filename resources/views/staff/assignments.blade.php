@extends('layouts.staff-course')

@section('title', 'Assignments | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>Assignments</h1>
    <p>Create and manage assignments for your courses and classes</p>
</div>

<!-- Course Selection -->
<div class="course-selection">
    <h5><i class="fas fa-clipboard"></i> Select Course and Class</h5>
    <div class="row">
        <div class="col-md-6">
            <label for="courseSelect" class="form-label">Course</label>
            <select class="form-select" id="courseSelect">
                <option value="">Choose a program...</option>
                @foreach($programs as $program)
                    <option value="{{ strtolower($program->code) }}">{{ $program->full_name }}</option>
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
            Create Assignment
        </a>
        <a href="/maintenance" class="action-btn success">
            <i class="fas fa-list"></i>
            View All Assignments
        </a>
        <a href="/maintenance" class="action-btn warning">
            <i class="fas fa-edit"></i>
            Grade Assignments
        </a>
    </div>
</div>

<!-- Assignments Content -->
<div class="content-card">
    <h5><i class="fas fa-clipboard"></i> Recent Assignments</h5>
    <div id="assignmentsContent">
        <div class="empty-state">
            <i class="fas fa-info-circle"></i>
            <h4>Select a Course and Class</h4>
            <p>Choose a course and class above to view and manage assignments for that specific group of students.</p>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Course and Class Selection Logic
    const courseSelect = document.getElementById('courseSelect');
    const classSelect = document.getElementById('classSelect');
    const courseInfo = document.getElementById('courseInfo');
    const actionButtons = document.getElementById('actionButtons');
    const assignmentsContent = document.getElementById('assignmentsContent');
    
    // Dynamic program classes from backend
    const courseClasses = {
        @foreach($programs as $program)
        '{{ strtolower($program->code) }}': [
            { value: '{{ strtolower($program->code) }}_a', text: '{{ $program->code }}-A' },
            { value: '{{ strtolower($program->code) }}_b', text: '{{ $program->code }}-B' },
            { value: '{{ strtolower($program->code) }}_c', text: '{{ $program->code }}-C' },
            { value: '{{ strtolower($program->code) }}_d', text: '{{ $program->code }}-D' }
        ],
        @endforeach
    };
    
    const courseNames = {
        @foreach($programs as $program)
        '{{ strtolower($program->code) }}': '{{ $program->full_name }}',
        @endforeach
    };
    
    courseSelect.addEventListener('change', function() {
        const selectedCourse = this.value;
        classSelect.innerHTML = '<option value="">Choose a class...</option>';
        
        if (selectedCourse && courseClasses[selectedCourse]) {
            classSelect.disabled = false;
            courseClasses[selectedCourse].forEach(classOption => {
                const option = document.createElement('option');
                option.value = classOption.value;
                option.textContent = classOption.text;
                classSelect.appendChild(option);
            });
            
            // Show course info
            courseInfo.style.display = 'block';
            document.getElementById('selectedCourseName').textContent = courseNames[selectedCourse];
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
            
            // Hide action buttons until class is selected
            actionButtons.style.display = 'none';
            
            // Reset content
            assignmentsContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <h4>Select a Class</h4>
                    <p>Choose a class above to view and manage assignments for that specific group of students.</p>
                </div>
            `;
        } else {
            classSelect.disabled = true;
            courseInfo.style.display = 'none';
            actionButtons.style.display = 'none';
            assignmentsContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <h4>Select a Course and Class</h4>
                    <p>Choose a course and class above to view and manage assignments for that specific group of students.</p>
                </div>
            `;
        }
    });
    
    classSelect.addEventListener('change', function() {
        const selectedClass = this.value;
        if (selectedClass) {
            // Update course info
            const selectedCourse = courseSelect.value;
            document.getElementById('selectedClassInfo').textContent = `Selected: ${this.options[this.selectedIndex].text}`;
            
            // Show action buttons
            actionButtons.style.display = 'flex';
            
            // Store selected course and class in session storage
            sessionStorage.setItem('selectedCourse', selectedCourse);
            sessionStorage.setItem('selectedClass', selectedClass);
            
            // Update content with sample assignments
            assignmentsContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Programming Assignment 1</h6>
                                <p class="card-text">Create a simple calculator program using basic programming concepts.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Due: Dec 15, 2024</small>
                                    <span class="badge bg-warning">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Data Structures Exercise</h6>
                                <p class="card-text">Implement a linked list data structure with basic operations.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Due: Dec 20, 2024</small>
                                    <span class="badge bg-success">Graded</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Database Design Project</h6>
                                <p class="card-text">Design a database schema for a library management system.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Due: Dec 25, 2024</small>
                                    <span class="badge bg-info">Draft</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Software Engineering Report</h6>
                                <p class="card-text">Write a comprehensive report on software development methodologies.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Due: Jan 5, 2025</small>
                                    <span class="badge bg-warning">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            console.log(`Selected: ${courseNames[selectedCourse]} - ${this.options[this.selectedIndex].text}`);
        } else {
            actionButtons.style.display = 'none';
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
            assignmentsContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <h4>Select a Class</h4>
                    <p>Choose a class above to view and manage assignments for that specific group of students.</p>
                </div>
            `;
        }
    });
});
</script>
@endpush
@endsection
