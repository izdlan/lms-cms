@extends('layouts.staff-course')

@section('title', 'My Students | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>My Students</h1>
    <p>View and manage students by course and class</p>
</div>

<!-- Course Selection -->
<div class="course-selection">
    <h5><i class="fas fa-users"></i> Select Course and Class</h5>
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
            Add Student
        </a>
        <a href="/maintenance" class="action-btn success">
            <i class="fas fa-download"></i>
            Export List
        </a>
        <a href="/maintenance" class="action-btn info">
            <i class="fas fa-chart-bar"></i>
            View Analytics
        </a>
    </div>
</div>

<!-- Students Content -->
<div class="content-card">
    <h5><i class="fas fa-users"></i> Student List</h5>
    <div id="studentsContent">
        <div class="empty-state">
            <i class="fas fa-info-circle"></i>
            <h4>Select a Course and Class</h4>
            <p>Choose a course and class above to view students enrolled in that specific group.</p>
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
    const studentsContent = document.getElementById('studentsContent');
    
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
            studentsContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <h4>Select a Class</h4>
                    <p>Choose a class above to view students enrolled in that specific group.</p>
                </div>
            `;
        } else {
            classSelect.disabled = true;
            courseInfo.style.display = 'none';
            actionButtons.style.display = 'none';
            studentsContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <h4>Select a Course and Class</h4>
                    <p>Choose a course and class above to view students enrolled in that specific group.</p>
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
            
            // Update content with sample students
            studentsContent.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>IC Number</th>
                                <th>Phone</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>STU001</td>
                                <td>John Doe</td>
                                <td>john.doe@student.edu</td>
                                <td>123456789012</td>
                                <td>012-3456789</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>STU002</td>
                                <td>Jane Smith</td>
                                <td>jane.smith@student.edu</td>
                                <td>123456789013</td>
                                <td>012-3456790</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>STU003</td>
                                <td>Mike Johnson</td>
                                <td>mike.johnson@student.edu</td>
                                <td>123456789014</td>
                                <td>012-3456791</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                            <tr>
                                <td>STU004</td>
                                <td>Sarah Wilson</td>
                                <td>sarah.wilson@student.edu</td>
                                <td>123456789015</td>
                                <td>012-3456792</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>STU005</td>
                                <td>David Brown</td>
                                <td>david.brown@student.edu</td>
                                <td>123456789016</td>
                                <td>012-3456793</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
            
            console.log(`Selected: ${courseNames[selectedCourse]} - ${this.options[this.selectedIndex].text}`);
        } else {
            actionButtons.style.display = 'none';
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
            studentsContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <h4>Select a Class</h4>
                    <p>Choose a class above to view students enrolled in that specific group.</p>
                </div>
            `;
        }
    });
});
</script>
@endpush
@endsection
