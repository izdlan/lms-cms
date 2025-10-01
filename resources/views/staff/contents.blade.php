@extends('layouts.staff-course')

@section('title', 'Course Materials | Staff | Olympia Education')

@section('content')
<div class="courses-header">
    <h1>Course Materials</h1>
    <p>Upload and manage course materials for your subjects and classes</p>
</div>

<!-- Subject and Class Selection -->
<div class="subject-class-selection">
    <h5><i class="fas fa-file-text"></i> Select Subject and Class</h5>
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
        <button class="action-btn success" id="uploadMaterialBtn">
            <i class="fas fa-upload"></i>
            Upload Material
        </button>
        <button class="action-btn primary" id="viewMaterialsBtn">
            <i class="fas fa-list"></i>
            View All Materials
        </button>
    </div>
</div>

<!-- Materials Management Section -->
<div id="materialsSection" class="materials-section" style="display: none;">
    <!-- Upload Material Form -->
    <div id="uploadMaterialForm" class="upload-form" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-upload"></i> Upload New Material</h5>
            </div>
            <div class="card-body">
                <form id="materialForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="materialTitle" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="materialTitle" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="materialType" class="form-label">Type</label>
                                <select class="form-select" id="materialType">
                                    <option value="document">Document</option>
                                    <option value="video">Video</option>
                                    <option value="image">Image</option>
                                    <option value="audio">Audio</option>
                                    <option value="link">External Link</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="materialDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="materialDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3" id="fileUploadSection">
                        <label for="materialFile" class="form-label">File *</label>
                        <input type="file" class="form-control" id="materialFile" required>
                        <div class="form-text">Supported formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR, JPG, PNG</div>
                    </div>
                    <div class="mb-3" id="externalUrlSection" style="display: none;">
                        <label for="externalUrl" class="form-label">External URL *</label>
                        <input type="url" class="form-control" id="externalUrl" placeholder="https://example.com">
                        <div class="form-text">Enter the external link URL</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Classes</label>
                        <div id="targetClasses" class="target-classes">
                            <!-- Target classes will be populated here -->
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Material
                        </button>
                        <button type="button" class="btn btn-secondary" id="cancelUploadBtn">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Materials -->
    <div id="viewMaterials" class="view-materials" style="display: none;">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list"></i> Course Materials</h5>
                <button class="btn btn-sm btn-outline-primary" id="refreshMaterialsBtn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="materialsList">
                    <!-- Materials will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Default Message -->
<div id="defaultMessage" class="content-card">
    <div class="text-center py-5">
        <i class="fas fa-file-text text-muted mb-3" style="font-size: 3rem;"></i>
        <h5 class="text-muted">Select Subject and Class</h5>
        <p class="text-muted">Choose a subject and class to manage course materials.</p>
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

/* Materials Section */
.materials-section {
    margin-top: 2rem;
}

.upload-form, .view-materials {
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

/* Material Cards */
.material-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.material-card:hover {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
}

.material-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.material-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.material-meta {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.material-description {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.material-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* File Upload Styling */
.form-control[type="file"] {
    padding: 0.375rem 0.75rem;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Subject and Class Selection Logic
    const subjectSelect = document.getElementById('subjectSelect');
    const classSelect = document.getElementById('classSelect');
    const subjectInfo = document.getElementById('subjectInfo');
    const actionButtons = document.getElementById('actionButtons');
    const materialsSection = document.getElementById('materialsSection');
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
            materialsSection.style.display = 'none';
            defaultMessage.style.display = 'block';
        } else {
            classSelect.disabled = true;
            subjectInfo.style.display = 'none';
            actionButtons.style.display = 'none';
            materialsSection.style.display = 'none';
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
            materialsSection.style.display = 'block';
            defaultMessage.style.display = 'none';
            
            // Populate target classes for material upload
            populateTargetClasses(selectedSubject);
            
            console.log(`Selected: ${subjectNames[selectedSubject]} - ${this.options[this.selectedIndex].text}`);
        } else {
            actionButtons.style.display = 'none';
            materialsSection.style.display = 'none';
            defaultMessage.style.display = 'block';
            document.getElementById('selectedClassInfo').textContent = 'Select a class to continue...';
        }
    });
    
    // Action button handlers
    document.getElementById('uploadMaterialBtn').addEventListener('click', function() {
        document.getElementById('uploadMaterialForm').style.display = 'block';
        document.getElementById('viewMaterials').style.display = 'none';
    });
    
    document.getElementById('viewMaterialsBtn').addEventListener('click', function() {
        document.getElementById('viewMaterials').style.display = 'block';
        document.getElementById('uploadMaterialForm').style.display = 'none';
        loadMaterials();
    });
    
    document.getElementById('cancelUploadBtn').addEventListener('click', function() {
        document.getElementById('uploadMaterialForm').style.display = 'none';
        document.getElementById('materialForm').reset();
    });
    
    // Material form submission
    document.getElementById('materialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        uploadMaterial();
    });
    
    // Also add click listener to the submit button as backup
    document.addEventListener('click', function(e) {
        if (e.target && e.target.type === 'submit' && e.target.closest('#materialForm')) {
            e.preventDefault();
            console.log('Submit button clicked');
            uploadMaterial();
        }
    });
    
    // Handle material type change
    document.getElementById('materialType').addEventListener('change', function() {
        const type = this.value;
        const fileSection = document.getElementById('fileUploadSection');
        const urlSection = document.getElementById('externalUrlSection');
        const fileInput = document.getElementById('materialFile');
        const urlInput = document.getElementById('externalUrl');
        
        if (type === 'link') {
            fileSection.style.display = 'none';
            urlSection.style.display = 'block';
            fileInput.required = false;
            urlInput.required = true;
        } else {
            fileSection.style.display = 'block';
            urlSection.style.display = 'none';
            fileInput.required = true;
            urlInput.required = false;
        }
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
    
    function uploadMaterial() {
        console.log('Upload material function called');
        
        const title = document.getElementById('materialTitle').value.trim();
        const description = document.getElementById('materialDescription').value.trim();
        const type = document.getElementById('materialType').value;
        const file = document.getElementById('materialFile').files[0];
        const externalUrl = document.getElementById('externalUrl').value.trim();
        const selectedClasses = Array.from(document.querySelectorAll('#targetClasses input[type="checkbox"]:checked'))
            .map(cb => cb.value);
        
        console.log('Form data:', {
            title: title,
            description: description,
            type: type,
            file: file ? file.name : 'No file',
            externalUrl: externalUrl,
            selectedClasses: selectedClasses,
            selectedSubject: selectedSubject,
            selectedClass: selectedClass
        });
        
        // Validation
        if (!title) {
            alert('Please enter a title for the material.');
            return;
        }
        
        if (selectedClasses.length === 0) {
            alert('Please select at least one target class.');
            return;
        }
        
        if (type !== 'link' && !file) {
            alert('Please select a file to upload.');
            return;
        }
        
        if (type === 'link' && !externalUrl) {
            alert('Please enter an external URL for link materials.');
            return;
        }
        
        if (!selectedSubject || !selectedClass) {
            alert('Please select a subject and class first.');
            return;
        }
        
        // Show loading state
        const submitBtn = document.querySelector('#materialForm button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        submitBtn.disabled = true;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        // Create FormData for file upload
        const formData = new FormData();
        formData.append('subject_code', selectedSubject);
        formData.append('class_code', selectedClass);
        formData.append('title', title);
        formData.append('description', description);
        formData.append('material_type', type);
        if (file) {
            formData.append('file', file);
        }
        if (externalUrl) {
            formData.append('external_url', externalUrl);
        }
        formData.append('_token', csrfToken);
        
        // Upload material
        console.log('Sending upload request to:', '{{ route("staff.materials.upload") }}');
        console.log('FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        fetch('{{ route("staff.materials.upload") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            console.log('Upload response status:', response.status);
            console.log('Upload response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Upload response data:', data);
            if (data.success) {
                alert('Material uploaded successfully!');
                document.getElementById('materialForm').reset();
                document.getElementById('uploadMaterialForm').style.display = 'none';
                // Refresh materials list if viewing
                if (document.getElementById('viewMaterials').style.display !== 'none') {
                    loadMaterials();
                }
            } else {
                alert('Error uploading material: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Error uploading material. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
    
    function loadMaterials() {
        const materialsList = document.getElementById('materialsList');
        materialsList.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading materials...</div>';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        // Fetch materials from server
        fetch('{{ route("staff.materials.get") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                subject_code: selectedSubject,
                class_code: selectedClass
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMaterials(data.materials);
            } else {
                materialsList.innerHTML = '<div class="alert alert-danger">Error loading materials: ' + (data.message || 'Unknown error') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            materialsList.innerHTML = '<div class="alert alert-danger">Error loading materials. Please try again.</div>';
        });
    }
    
    function displayMaterials(materials) {
        const materialsList = document.getElementById('materialsList');
        
        if (materials.length === 0) {
            materialsList.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-file text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-muted">No materials uploaded yet</h5>
                    <p class="text-muted">Upload your first material for this subject and class.</p>
                </div>
            `;
            return;
        }
        
        let html = '<div class="row">';
        materials.forEach(material => {
            const fileIcon = getFileIcon(material.material_type);
            const fileSize = formatFileSize(material.file_size);
            const publishedDate = new Date(material.published_at).toLocaleDateString();
            
            html += `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 material-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="material-icon me-3">
                                    <i class="${fileIcon} fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">${material.title}</h6>
                                    <small class="text-muted">${material.author_name}</small>
                                </div>
                            </div>
                            
                            ${material.description ? `<p class="card-text text-muted small">${material.description.substring(0, 100)}${material.description.length > 100 ? '...' : ''}</p>` : ''}
                            
                            <div class="material-meta mb-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Type</small>
                                        <span class="badge bg-secondary">${material.material_type.charAt(0).toUpperCase() + material.material_type.slice(1)}</span>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Size</small>
                                        <small class="fw-bold">${fileSize}</small>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Downloads</small>
                                        <span class="badge bg-info">${material.download_count}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">${publishedDate}</small>
                                <div>
                                    ${material.external_url ? 
                                        `<a href="${material.external_url}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-external-link-alt me-1"></i>Open
                                        </a>` : 
                                        `<a href="/staff/materials/download/${material.id}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>`
                                    }
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteMaterial(${material.id})">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        materialsList.innerHTML = html;
    }
    
    function getFileIcon(materialType) {
        switch (materialType) {
            case 'document': return 'fas fa-file-pdf';
            case 'video': return 'fas fa-video';
            case 'image': return 'fas fa-image';
            case 'audio': return 'fas fa-music';
            case 'link': return 'fas fa-link';
            default: return 'fas fa-file';
        }
    }
    
    function formatFileSize(bytes) {
        if (!bytes) return 'N/A';
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        return Math.round(size * 100) / 100 + ' ' + units[unitIndex];
    }
    
    function deleteMaterial(materialId) {
        if (!confirm('Are you sure you want to delete this material?')) {
            return;
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        fetch(`/staff/materials/delete/${materialId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Material deleted successfully!');
                loadMaterials(); // Refresh the list
            } else {
                alert('Error deleting material: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting material. Please try again.');
        });
    }
});
</script>
@endpush
@endsection