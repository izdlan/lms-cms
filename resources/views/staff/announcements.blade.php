@extends('layouts.staff-course')

@section('title', 'Announcements | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>Course Announcements</h1>
    <p>Create and manage announcements for your subjects and classes</p>
</div>

<!-- Subject and Class Selection -->
<div class="subject-class-selection">
    <h5><i class="fas fa-bullhorn"></i> Select Subject and Class</h5>
    <div class="row">
        <div class="col-md-6">
            <label for="subjectSelect" class="form-label">Subject</label>
            <select class="form-select" id="subjectSelect">
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
    
    <!-- Subject Info Display -->
    <div id="subjectInfo" class="subject-info" style="display: none;">
        <h4 id="selectedSubjectName"></h4>
        <p id="selectedClassInfo"></p>
    </div>
    
    <!-- Action Buttons -->
    <div id="actionButtons" class="action-buttons" style="display: none;">
        <button class="action-btn success" id="createAnnouncementBtn">
            <i class="fas fa-plus"></i>
            Create Announcement
        </button>
        <button class="action-btn primary" id="viewAnnouncementsBtn">
            <i class="fas fa-list"></i>
            View All Announcements
        </button>
    </div>
</div>

<!-- Announcements Management Section -->
<div id="announcementsSection" class="announcements-section" style="display: none;">
    <!-- Create Announcement Form -->
    <div id="createAnnouncementForm" class="create-form" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Create New Announcement</h5>
            </div>
            <div class="card-body">
                <form id="announcementForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="announcementTitle" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="announcementTitle" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isImportant">
                                    <label class="form-check-label" for="isImportant">
                                        Mark as Important
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="announcementContent" class="form-label">Content *</label>
                        <textarea class="form-control" id="announcementContent" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Classes</label>
                        <div id="targetClasses" class="target-classes">
                            <!-- Target classes will be populated here -->
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Announcement
                        </button>
                        <button type="button" class="btn btn-secondary" id="cancelCreateBtn">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Announcements -->
    <div id="viewAnnouncements" class="view-announcements" style="display: none;">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list"></i> Announcements</h5>
                <button class="btn btn-sm btn-outline-primary" id="refreshAnnouncementsBtn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="announcementsList">
                    <!-- Announcements will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Default Message -->
<div id="defaultMessage" class="content-card">
    <div class="text-center py-5">
        <i class="fas fa-bullhorn text-muted mb-3" style="font-size: 3rem;"></i>
        <h5 class="text-muted">Select Subject and Class</h5>
        <p class="text-muted">Choose a subject and class to manage announcements.</p>
    </div>
</div>

@push('styles')
<style>
/* Subject and Class Selection */
.subject-class-selection {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #dee2e6;
}

.subject-class-selection h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.subject-class-selection h5 i {
    color: #0056d2;
}

.subject-info {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 1rem;
}

.subject-info h4 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.subject-info p {
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
    cursor: pointer;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.action-btn.success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
}

.action-btn.success:hover {
    background: linear-gradient(135deg, #1e7e34, #1c7430);
    color: white;
}

.action-btn.primary {
    background: linear-gradient(135deg, #0056d2, #0041a3);
    color: white;
}

.action-btn.primary:hover {
    background: linear-gradient(135deg, #0041a3, #003d99);
    color: white;
}

/* Announcements Section */
.announcements-section {
    margin-top: 2rem;
}

.create-form, .view-announcements {
    margin-bottom: 2rem;
}

.target-classes {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.class-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.class-checkbox:hover {
    background: #e9ecef;
}

.class-checkbox input[type="checkbox"] {
    margin: 0;
}

.class-checkbox.selected {
    background: #d1ecf1;
    border-color: #bee5eb;
}

/* Announcement Cards */
.announcement-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.announcement-card:hover {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
}

.announcement-card.important {
    border-left: 4px solid #ffc107;
    background: #fffbf0;
}

.announcement-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.announcement-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.announcement-meta {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.announcement-content {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.announcement-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Delete Button Styling */
.announcement-item .btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    opacity: 0.7;
    transition: all 0.2s ease;
}

.announcement-item .btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    opacity: 1;
    transform: scale(1.05);
}

.announcement-item:hover .btn-outline-danger {
    opacity: 1;
}
</style>
@endpush

@push('scripts')
<script>
// Function to convert URLs to clickable links
    function convertUrlsToLinks(text) {
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, function(url) {
            return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-underline">' + url + '</a>';
        });
    }
    
    function deleteAnnouncement(announcementId) {
        if (!confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
            return;
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        fetch(`/staff/announcements/delete/${announcementId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Announcement deleted successfully!');
                loadAnnouncements(); // Refresh the list
            } else {
                alert('Error deleting announcement: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting announcement. Please try again.');
        });
    }

document.addEventListener('DOMContentLoaded', function() {
    // Subject and Class Selection Logic
    const subjectSelect = document.getElementById('subjectSelect');
    const classSelect = document.getElementById('classSelect');
    const subjectInfo = document.getElementById('subjectInfo');
    const actionButtons = document.getElementById('actionButtons');
    const announcementsSection = document.getElementById('announcementsSection');
    const defaultMessage = document.getElementById('defaultMessage');
    
    // Subject classes data from backend
    const subjectClasses = {
        @foreach($subjects as $subject)
        '{{ $subject->code }}': [
            @foreach($subject->classSchedules->where('lecturer_id', $lecturer->id) as $class)
            { 
                classCode: '{{ $class->class_code }}', 
                className: '{{ $class->class_name }}',
                description: '{{ $class->description }}',
                venue: '{{ $class->venue }}',
                dayOfWeek: '{{ $class->day_of_week }}',
                startTime: '{{ $class->start_time }}',
                endTime: '{{ $class->end_time }}'
            },
            @endforeach
        ],
        @endforeach
    };
    
    const subjectNames = {
        @foreach($subjects as $subject)
        '{{ $subject->code }}': '{{ $subject->name }}',
        @endforeach
    };
    
    let selectedSubject = null;
    let selectedClass = null;
    
    subjectSelect.addEventListener('change', function() {
        const subjectCode = this.value;
        classSelect.innerHTML = '<option value="">Choose a class...</option>';
        
        if (subjectCode && subjectClasses[subjectCode]) {
            classSelect.disabled = false;
            subjectClasses[subjectCode].forEach(classData => {
                const option = document.createElement('option');
                option.value = classData.classCode;
                option.textContent = `${classData.className} (${classData.classCode})`;
                classSelect.appendChild(option);
            });
            
            // Show subject info
            subjectInfo.style.display = 'block';
            document.getElementById('selectedSubjectName').textContent = subjectNames[subjectCode];
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
            
            // Hide action buttons until class is selected
            actionButtons.style.display = 'none';
            announcementsSection.style.display = 'none';
            defaultMessage.style.display = 'block';
        } else {
            classSelect.disabled = true;
            subjectInfo.style.display = 'none';
            actionButtons.style.display = 'none';
            announcementsSection.style.display = 'none';
            defaultMessage.style.display = 'block';
        }
    });
    
    classSelect.addEventListener('change', function() {
        const classCode = this.value;
        if (classCode) {
            selectedSubject = subjectSelect.value;
            selectedClass = classCode;
            
            // Update subject info
            document.getElementById('selectedClassInfo').textContent = `Selected: ${this.options[this.selectedIndex].text}`;
            
            // Show action buttons
            actionButtons.style.display = 'flex';
            announcementsSection.style.display = 'block';
            defaultMessage.style.display = 'none';
            
            // Populate target classes for announcement creation
            populateTargetClasses(selectedSubject);
            
            console.log(`Selected: ${subjectNames[selectedSubject]} - ${this.options[this.selectedIndex].text}`);
        } else {
            actionButtons.style.display = 'none';
            announcementsSection.style.display = 'none';
            defaultMessage.style.display = 'block';
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
        }
    });
    
    // Action button handlers
    document.getElementById('createAnnouncementBtn').addEventListener('click', function() {
        document.getElementById('createAnnouncementForm').style.display = 'block';
        document.getElementById('viewAnnouncements').style.display = 'none';
    });
    
    document.getElementById('viewAnnouncementsBtn').addEventListener('click', function() {
        document.getElementById('viewAnnouncements').style.display = 'block';
        document.getElementById('createAnnouncementForm').style.display = 'none';
        loadAnnouncements();
    });
    
    document.getElementById('cancelCreateBtn').addEventListener('click', function() {
        document.getElementById('createAnnouncementForm').style.display = 'none';
        document.getElementById('announcementForm').reset();
    });
    
    // Announcement form submission
    document.getElementById('announcementForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createAnnouncement();
    });
    
    function populateTargetClasses(subjectCode) {
        const targetClassesContainer = document.getElementById('targetClasses');
        targetClassesContainer.innerHTML = '';
        
        if (subjectClasses[subjectCode]) {
            subjectClasses[subjectCode].forEach(classData => {
                const classCheckbox = document.createElement('div');
                classCheckbox.className = 'class-checkbox';
                classCheckbox.innerHTML = `
                    <input type="checkbox" id="class_${classData.classCode}" value="${classData.classCode}">
                    <label for="class_${classData.classCode}">${classData.className} (${classData.classCode})</label>
                `;
                
                // Add click handler for visual feedback
                classCheckbox.addEventListener('click', function() {
                    this.classList.toggle('selected');
                });
                
                targetClassesContainer.appendChild(classCheckbox);
            });
        }
    }
    
    function createAnnouncement() {
        const title = document.getElementById('announcementTitle').value;
        const content = document.getElementById('announcementContent').value;
        const isImportant = document.getElementById('isImportant').checked;
        const selectedClasses = Array.from(document.querySelectorAll('#targetClasses input[type="checkbox"]:checked'))
            .map(cb => cb.value);
        
        if (!title || !content || selectedClasses.length === 0) {
            alert('Please fill in all required fields and select at least one class.');
            return;
        }
        
        // Show loading state
        const submitBtn = document.querySelector('#announcementForm button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        // Debug logging
        console.log('Creating announcement with data:', {
            subject_code: selectedSubject,
            class_code: selectedClass,
            title: title,
            content: content,
            is_important: isImportant,
            target_classes: selectedClasses,
            csrf_token: csrfToken
        });
        
        // Send data to server
        fetch('{{ route("staff.announcements.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                subject_code: selectedSubject,
                class_code: selectedClass,
                title: title,
                content: content,
                is_important: isImportant,
                target_classes: selectedClasses
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Announcement created successfully!');
                document.getElementById('announcementForm').reset();
                document.getElementById('createAnnouncementForm').style.display = 'none';
                // Reload announcements if viewing
                if (document.getElementById('viewAnnouncements').style.display !== 'none') {
                    loadAnnouncements();
                }
            } else {
                alert('Error creating announcement: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating announcement. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
    
    function loadAnnouncements() {
        const announcementsList = document.getElementById('announcementsList');
        announcementsList.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading announcements...</div>';
        
        // Fetch announcements from server
        fetch(`{{ route("staff.announcements.get") }}?subject_code=${selectedSubject}&class_code=${selectedClass}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.announcements.length > 0) {
                    announcementsList.innerHTML = '';
                    data.announcements.forEach(announcement => {
                        const announcementDiv = document.createElement('div');
                        announcementDiv.className = 'announcement-item mb-3';
                        announcementDiv.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">
                                    ${announcement.is_important ? '<i class="fas fa-star text-warning me-2"></i>' : '<i class="fas fa-info-circle text-info me-2"></i>'}
                                    ${announcement.title}
                                </h6>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">${new Date(announcement.published_at).toLocaleDateString()}</small>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAnnouncement(${announcement.id})" title="Delete announcement">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2">${convertUrlsToLinks(announcement.content)}</div>
                            <small class="text-muted">By ${announcement.author_name}</small>
                        `;
                        announcementsList.appendChild(announcementDiv);
                    });
                } else {
                    announcementsList.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-bullhorn text-muted mb-3" style="font-size: 2rem;"></i>
                            <h5 class="text-muted">No announcements yet</h5>
                            <p class="text-muted">Create your first announcement for this subject and class.</p>
                        </div>
                    `;
                }
            } else {
                announcementsList.innerHTML = '<div class="alert alert-danger">Error loading announcements.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            announcementsList.innerHTML = '<div class="alert alert-danger">Error loading announcements. Please try again.</div>';
        });
    }
});
</script>
@endpush
@endsection