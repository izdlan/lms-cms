@extends('layouts.admin')

@section('title', 'Edit Announcement')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Edit Announcement</h1>
                            <p>Update: {{ $announcement->title }}</p>
                        </div>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" width="20" height="20"></i>
                            Back to Announcements
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="general" {{ old('category', $announcement->category) == 'general' ? 'selected' : '' }}>General</option>
                                            <option value="academic" {{ old('category', $announcement->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                                            <option value="important" {{ old('category', $announcement->category) == 'important' ? 'selected' : '' }}>Important</option>
                                            <option value="event" {{ old('category', $announcement->category) == 'event' ? 'selected' : '' }}>Event</option>
                                            <option value="maintenance" {{ old('category', $announcement->category) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="8" required>{{ old('content', $announcement->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="low" {{ old('priority', $announcement->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority', $announcement->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('priority', $announcement->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">Upload a new image file (JPG, PNG, GIF, SVG) - Max 2MB</div>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="imagePreview" class="image-preview">
                                            @if($announcement->image_url)
                                                <img id="previewImg" src="{{ $announcement->image_url }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                                <div class="form-text mt-2">Current image</div>
                                            @else
                                                <div class="text-muted">No image selected</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="image_url" class="form-label">Or Image URL</label>
                                        <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                                               id="image_url" name="image_url" value="{{ old('image_url', $announcement->image_url) }}" placeholder="https://example.com/image.jpg">
                                        <div class="form-text">Alternative: Enter an image URL instead of uploading</div>
                                        @error('image_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="published_at" class="form-label">Published Date</label>
                                        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                               id="published_at" name="published_at" value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}">
                                        <div class="form-text">Leave empty to publish immediately</div>
                                        @error('published_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="expires_at" class="form-label">Expiry Date</label>
                                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                               id="expires_at" name="expires_at" value="{{ old('expires_at', $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '') }}">
                                        <div class="form-text">Leave empty for no expiry</div>
                                        @error('expires_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                                                   {{ old('is_featured', $announcement->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                Featured announcement
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                                   {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active (show on website)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save" width="20" height="20"></i>
                                    Update Announcement
                                </button>
                                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            // Update the preview text
            const previewText = preview.querySelector('.form-text');
            if (previewText) {
                previewText.textContent = 'New image preview';
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Initialize Feather icons
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        if (typeof safeFeatherReplace === 'function') {
            safeFeatherReplace();
        } else if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }, 200);
});
</script>
@endpush
