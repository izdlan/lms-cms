@extends('layouts.admin')

@section('title', 'Home Page Management')

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
                            <h1>Home Page Management</h1>
                            <p>Edit the public home page content</p>
                        </div>
                        <a href="{{ route('admin.home-page.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="settings" width="20" height="20"></i>
                            Manage Sections
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i data-feather="check-circle" width="20" height="20"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Home Page Preview with Edit Buttons -->
                <div class="home-page-preview">
                    @php
                        $heroSection = $homePageContents->where('section_name', 'hero')->first();
                    @endphp
                    
                    <!-- Hero Section -->
                    <section class="slider-container slider-hero-section2" style="background-image: url('{{ $heroSection->image_url ?? '/assets/default/img/home/world.png' }}')">
                        <div class="container user-select-none">
                            <div class="admin-edit-overlay">
                                <button class="btn btn-sm btn-warning" onclick="editHeroSection()">
                                    <i class="fas fa-edit"></i> Edit Hero Section
                                </button>
                            </div>
                            <div class="row slider-content align-items-center hero-section2 flex-column-reverse flex-md-row">
                                <div class="col-12 col-md-7 col-lg-6">
                                    <h1 class="text-secondary font-weight-bold">{{ $heroSection->title ?? 'Advance Your Career with Olympia Education' }}</h1>
                                    <p class="slide-hint text-gray mt-15">{{ $heroSection->content ?? 'We offer industry relevant programs, hands-on learning, and strong career support empowering students with the skills, knowledge, and confidence to thrive in today\'s fast-paced world.' }}</p>

                                    <form action="/search" method="get" class="d-inline-flex mt-20 mt-lg-20 w-100">
                                        <div class="form-group d-flex align-items-center m-0 slider-search p-10 bg-white w-100">
                                            <input type="text" name="search" class="form-control border-0 mr-lg-50" placeholder="Search courses, instructors and organizations..." />
                                            <button type="submit" class="btn btn-primary rounded-pill">Search</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 col-md-5 col-lg-6">
                                    <lottie-player src="/store/1/animated-header.json" background="transparent" speed="1" class="w-100" loop autoplay></lottie-player>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Gallery Section -->
                    <section class="home-sections top-links-section">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- TOP LINK Sidebar -->
                                <div class="col-md-2 col-lg-2 top-links-sidebar">
                                    <div class="sidebar-content">
                                        <h2 class="sidebar-title">Student Portal</h2>
                                        <ul class="top-links-list">
                                            <li><a href="/maintenance" class="top-link-item">LMS</a></li>
                                            <li><a href="/login" class="top-link-item">Login</a></li>
                                            <li><a href="/maintenance" class="top-link-item">Course Registration</a></li>
                                            <li><a href="/maintenance" class="top-link-item">Student Bills</a></li>
                                            <li><a href="/maintenance" class="top-link-item">Exam Result</a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- Gallery Container with Admin Controls -->
                                <div class="col-md-10 col-lg-10 gallery-container">
                                    <div class="gallery-admin-controls">
                                        <button class="btn btn-sm btn-primary" onclick="openGalleryManager()">
                                            <i class="fas fa-cog"></i> Manage Gallery
                                        </button>
                                    </div>
                                    <div class="single-gallery">
                                        <div class="gallery-main">
                                            <div class="gallery-image-container">
                                                @php
                                                    $gallerySection = $homePageContents->where('section_name', 'gallery')->first();
                                                    $galleryImages = $gallerySection ? json_decode($gallerySection->metadata, true)['images'] ?? [] : [];
                                                    $defaultImages = [
                                                        '/assets/default/img/home/poster1.jpeg',
                                                        '/assets/default/img/home/poster2.jpeg',
                                                        '/assets/default/img/home/poster3.jpeg',
                                                        '/assets/default/img/home/poster4.jpeg',
                                                        '/assets/default/img/home/poster5.jpeg',
                                                        '/assets/default/img/home/poster6.jpg',
                                                        '/assets/default/img/home/poster7.jpg'
                                                    ];
                                                    $images = !empty($galleryImages) ? $galleryImages : $defaultImages;
                                                @endphp
                                                
                                                @foreach($images as $index => $image)
                                                    <div class="gallery-image-wrapper">
                                                        <img src="{{ $image }}" alt="Olympia Education Poster {{ $index + 1 }}" class="main-gallery-image {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                                                        <div class="gallery-image-actions">
                                                            <button class="btn btn-sm btn-danger" onclick="removeGalleryImage({{ $index }})" title="Remove Image">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <!-- Navigation Arrows -->  
                                            <button class="gallery-nav gallery-prev" onclick="changeImage(-1)">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <button class="gallery-nav gallery-next" onclick="changeImage(1)">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            
                                            <!-- Dots Indicator -->
                                            <div class="gallery-dots">
                                                @foreach($images as $index => $image)
                                                    <span class="dot {{ $index === 0 ? 'active' : '' }}" onclick="currentImage({{ $index }})"></span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Announcements Section -->
                    @if($announcements->count() > 0)
                    <section class="home-sections announcements-section">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h2 class="section-title">Latest Announcements</h2>
                                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-cog"></i> Manage Announcements
                                        </a>
                                    </div>
                                    <div class="row">
                                        @foreach($announcements as $announcement)
                                            <div class="col-md-4 mb-4">
                                                <div class="announcement-card">
                                                    @if($announcement->image_url)
                                                        <div class="announcement-image">
                                                            <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="img-fluid">
                                                        </div>
                                                    @endif
                                                    <div class="announcement-content">
                                                        <div class="announcement-meta">
                                                            <span class="badge badge-{{ $announcement->priority === 'high' ? 'danger' : ($announcement->priority === 'medium' ? 'warning' : 'success') }}">
                                                                {{ ucfirst($announcement->priority) }}
                                                            </span>
                                                            <span class="announcement-category">{{ ucfirst($announcement->category) }}</span>
                                                        </div>
                                                        <h4 class="announcement-title">{{ $announcement->title }}</h4>
                                                        <p class="announcement-excerpt">{{ Str::limit(strip_tags($announcement->content), 100) }}</p>
                                                        <div class="announcement-date">
                                                            <i class="fas fa-calendar"></i>
                                                            {{ $announcement->published_at->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Management Modal -->
<div id="galleryManagerModal" class="gallery-manager-modal">
    <div class="gallery-manager-content">
        <span class="close" onclick="closeGalleryManager()">&times;</span>
        <h2>Manage Gallery Images</h2>
        <p>Add or remove images from the gallery carousel.</p>
        
        <div class="gallery-images-grid" id="galleryImagesGrid">
            <!-- Images will be loaded here -->
        </div>
        
        <div class="add-image-section">
            <h4>Add New Image</h4>
            <input type="url" id="newImageUrl" placeholder="Enter image URL...">
            <button class="btn btn-primary" onclick="addGalleryImage()">Add Image</button>
        </div>
        
        <div class="modal-actions mt-3">
            <button class="btn btn-success" onclick="saveGalleryChanges()">Save Changes</button>
            <button class="btn btn-secondary" onclick="closeGalleryManager()">Cancel</button>
        </div>
    </div>
</div>

<!-- Hero Section Edit Modal -->
<div id="heroEditModal" class="gallery-manager-modal">
    <div class="gallery-manager-content">
        <span class="close" onclick="closeHeroEdit()">&times;</span>
        <h2>Edit Hero Section</h2>
        
        <form id="heroEditForm">
            <div class="mb-3">
                <label for="heroTitle" class="form-label">Title</label>
                <input type="text" class="form-control" id="heroTitle" value="{{ $heroSection->title ?? 'Advance Your Career with Olympia Education' }}">
            </div>
            <div class="mb-3">
                <label for="heroContent" class="form-label">Content</label>
                <textarea class="form-control" id="heroContent" rows="3">{{ $heroSection->content ?? 'We offer industry relevant programs, hands-on learning, and strong career support empowering students with the skills, knowledge, and confidence to thrive in today\'s fast-paced world.' }}</textarea>
            </div>
            <div class="mb-3">
                <label for="heroImageUrl" class="form-label">Background Image URL</label>
                <input type="url" class="form-control" id="heroImageUrl" value="{{ $heroSection->image_url ?? '/assets/default/img/home/world.png' }}">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-success" onclick="saveHeroChanges()">Save Changes</button>
                <button type="button" class="btn btn-secondary" onclick="closeHeroEdit()">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Admin Edit Controls */
.admin-edit-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
}

.gallery-admin-controls {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
}

.gallery-image-wrapper {
    position: relative;
    display: inline-block;
}

.gallery-image-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-image-wrapper:hover .gallery-image-actions {
    opacity: 1;
}

.gallery-image-actions .btn {
    padding: 5px 8px;
    font-size: 12px;
}

/* Gallery Management Modal */
.gallery-manager-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.gallery-manager-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 800px;
    border-radius: 10px;
    max-height: 80vh;
    overflow-y: auto;
}

.gallery-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.gallery-image-item {
    position: relative;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    aspect-ratio: 16/9;
}

.gallery-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-image-item .remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    font-size: 12px;
    cursor: pointer;
}

.gallery-image-item .remove-btn:hover {
    background: rgba(220, 53, 69, 1);
}

.add-image-section {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin-top: 20px;
}

.add-image-section input[type="url"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

/* Announcement Cards */
.announcement-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
    height: 100%;
}

.announcement-card:hover {
    transform: translateY(-5px);
}

.announcement-image {
    height: 200px;
    overflow: hidden;
}

.announcement-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.announcement-content {
    padding: 20px;
}

.announcement-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.announcement-category {
    color: #666;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 500;
}

.announcement-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.4;
}

.announcement-excerpt {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.announcement-date {
    color: #999;
    font-size: 12px;
    display: flex;
    align-items: center;
}

.announcement-date i {
    margin-right: 5px;
}
</style>
@endsection

@section('scripts')
<script>
let currentGalleryImages = @json($images);
let currentImageIndex = 0;

function openGalleryManager() {
    document.getElementById('galleryManagerModal').style.display = 'block';
    loadGalleryImages();
}

function closeGalleryManager() {
    document.getElementById('galleryManagerModal').style.display = 'none';
}

function loadGalleryImages() {
    const grid = document.getElementById('galleryImagesGrid');
    grid.innerHTML = '';
    
    currentGalleryImages.forEach((image, index) => {
        const imageItem = document.createElement('div');
        imageItem.className = 'gallery-image-item';
        imageItem.innerHTML = `
            <img src="${image}" alt="Gallery Image ${index + 1}">
            <button class="remove-btn" onclick="removeImageFromGrid(${index})">×</button>
        `;
        grid.appendChild(imageItem);
    });
}

function removeImageFromGrid(index) {
    if (confirm('Are you sure you want to remove this image?')) {
        currentGalleryImages.splice(index, 1);
        loadGalleryImages();
    }
}

function addGalleryImage() {
    const url = document.getElementById('newImageUrl').value.trim();
    if (url) {
        currentGalleryImages.push(url);
        document.getElementById('newImageUrl').value = '';
        loadGalleryImages();
    } else {
        alert('Please enter a valid image URL');
    }
}

function saveGalleryChanges() {
    // Send AJAX request to save gallery changes
    fetch('{{ route("admin.gallery.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            images: currentGalleryImages
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Gallery updated successfully!');
            closeGalleryManager();
            location.reload(); // Refresh to show changes
        } else {
            alert('Error updating gallery: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating gallery');
    });
}

function removeGalleryImage(index) {
    if (confirm('Are you sure you want to remove this image from the gallery?')) {
        currentGalleryImages.splice(index, 1);
        saveGalleryChanges();
    }
}

function editHeroSection() {
    document.getElementById('heroEditModal').style.display = 'block';
}

function closeHeroEdit() {
    document.getElementById('heroEditModal').style.display = 'none';
}

function saveHeroChanges() {
    const title = document.getElementById('heroTitle').value;
    const content = document.getElementById('heroContent').value;
    const imageUrl = document.getElementById('heroImageUrl').value;
    
    fetch('{{ route("admin.hero.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            title: title,
            content: content,
            image_url: imageUrl
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Hero section updated successfully!');
            closeHeroEdit();
            location.reload();
        } else {
            alert('Error updating hero section: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating hero section');
    });
}

// Original gallery functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.main-gallery-image');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    if (n >= slides.length) currentSlide = 0;
    if (n < 0) currentSlide = slides.length - 1;
    
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}

function changeImage(direction) {
    currentSlide += direction;
    showSlide(currentSlide);
}

function currentImage(n) {
    currentSlide = n;
    showSlide(currentSlide);
}

// Auto-advance slides
setInterval(() => {
    currentSlide++;
    showSlide(currentSlide);
}, 5000);
</script>
@endsection
