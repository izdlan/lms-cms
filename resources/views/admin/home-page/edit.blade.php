@extends('layouts.admin')

@section('title', 'Edit Home Page Section')

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
                            <h1>Edit Home Page Section</h1>
                            <p>Update section: {{ $content->section_name }}</p>
                        </div>
                        <a href="{{ route('admin.home-page.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" width="20" height="20"></i>
                            Back to Sections
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.home-page.update', $content) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="section_name" class="form-label">Section Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('section_name') is-invalid @enderror" 
                                               id="section_name" name="section_name" value="{{ old('section_name', $content->section_name) }}" required>
                                        <div class="form-text">Unique identifier for this section (e.g., hero, about, features)</div>
                                        @error('section_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', $content->sort_order) }}" min="0" required>
                                        <div class="form-text">Lower numbers appear first</div>
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $content->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="6">{{ old('content', $content->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">Upload a new image file (JPG, PNG, GIF, SVG) - Max 2MB</div>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div id="imagePreview" class="image-preview">
                                            @if($content->image_url)
                                                <img id="previewImg" src="{{ $content->image_url }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                                <div class="form-text mt-2">Current image</div>
                                            @else
                                                <div class="text-muted">No image selected</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image_url" class="form-label">Or Image URL</label>
                                <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                                       id="image_url" name="image_url" value="{{ old('image_url', $content->image_url) }}" placeholder="https://example.com/image.jpg">
                                <div class="form-text">Alternative: Enter an image URL instead of uploading</div>
                                @error('image_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $content->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active (show on home page)
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save" width="20" height="20"></i>
                                    Update Section
                                </button>
                                <a href="{{ route('admin.home-page.index') }}" class="btn btn-outline-secondary">
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
