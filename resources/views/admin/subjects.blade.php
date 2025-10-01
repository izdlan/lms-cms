@extends('layouts.admin')

@section('title', 'Subject Images | Admin | Olympia Education')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subject Images</li>
                    </ol>
                </div>
                <h4 class="page-title">Subject Images Management</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Manage Subject Images</h5>
                    <p class="text-muted mb-0">Upload and manage images for each subject to make the student dashboard more visually appealing.</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        @foreach($subjects as $subject)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="subject-preview" style="height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); background-size: cover; background-position: center; background-repeat: no-repeat; border-radius: 8px; position: relative; {{ $subject->image ? 'background-image: url(' . asset('storage/' . $subject->image) . ');' : '' }}">
                                                @if($subject->image)
                                                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%); border-radius: 8px;"></div>
                                                @endif
                                                <div style="position: relative; z-index: 2; display: flex; align-items: center; justify-content: center; height: 100%; color: white; font-size: 1.5rem;">
                                                    @if(!$subject->image)
                                                        <i class="fas fa-book"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <h6 class="card-title">{{ $subject->name }}</h6>
                                        <p class="text-muted small mb-3">{{ $subject->code }}</p>
                                        
                                        <div class="d-grid gap-2">
                                            <form id="uploadForm{{ $subject->id }}" enctype="multipart/form-data">
                                                @csrf
                                                <input type="file" id="imageInput{{ $subject->id }}" name="image" accept="image/*" style="display: none;" onchange="uploadImage({{ $subject->id }})">
                                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('imageInput{{ $subject->id }}').click()">
                                                    <i class="fas fa-upload me-1"></i>
                                                    {{ $subject->image ? 'Change Image' : 'Upload Image' }}
                                                </button>
                                            </form>
                                            
                                            @if($subject->image)
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeImage({{ $subject->id }})">
                                                    <i class="fas fa-trash me-1"></i> Remove Image
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function uploadImage(subjectId) {
    const fileInput = document.getElementById('imageInput' + subjectId);
    const file = fileInput.files[0];
    
    if (!file) return;
    
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Show loading state
    const button = fileInput.parentElement.querySelector('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
    button.disabled = true;
    
    fetch(`/admin/subjects/${subjectId}/upload-image`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show updated image
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading the image');
    })
    .finally(() => {
        // Reset button state
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function removeImage(subjectId) {
    if (!confirm('Are you sure you want to remove this subject image?')) {
        return;
    }
    
    fetch(`/admin/subjects/${subjectId}/remove-image`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show updated state
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the image');
    });
}
</script>
@endpush
