@extends('layouts.app')

@section('title', 'Home')

@section('content')
@php
    $heroSection = $homePageContents->where('section_name', 'hero')->first();
@endphp
<section class="slider-container  slider-hero-section2" style="background-image: url('{{ $heroSection->image_url ?? '/assets/default/img/home/world.png' }}')">
    <div class="container user-select-none">
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

<section class="home-sections top-links-section">
    <div class="container-fluid">
        <div class="row">
            <!-- TOP LINK Sidebar -->
            <div class="col-md-2 col-lg-2 top-links-sidebar">
                <div class="sidebar-content">
                    <h2 class="sidebar-title">Student Portal</h2>
                    <ul class="top-links-list">
                        <li><a href="/login" class="top-link-item">Login</a></li>
                        <li><a href="/login" class="top-link-item">Course Registration</a></li>
                        <li><a href="/login" class="top-link-item">Student Bills</a></li>
                        <li><a href="/login" class="top-link-item">Exam Result</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Single Image Gallery with Navigation -->
            <div class="col-md-10 col-lg-10 gallery-container">
                <div class="single-gallery">
                    <div class="gallery-main">
                        <div class="gallery-image-container">
                            @php
                                // Use announcement images instead of static gallery images
                                $announcementImages = $galleryAnnouncements->filter(function($announcement) {
                                    return !empty($announcement->image_url);
                                });
                            @endphp
                            
                            @if($announcementImages->count() > 0)
                                @foreach($announcementImages as $index => $announcement)
                                    <img src="{{ $announcement->image_url }}" 
                                         alt="{{ $announcement->title }}" 
                                         class="main-gallery-image {{ $index === 0 ? 'active' : '' }}" 
                                         data-index="{{ $index }}" 
                                         data-announcement-id="{{ $announcement->id }}"
                                         style="width: 100% !important; height: 100% !important; object-fit: cover !important; cursor: pointer;"
                                         onclick="redirectToAnnouncement({{ $announcement->id }})">
                                @endforeach
                            @else
                                <!-- Fallback message when no announcements with images exist -->
                                <div class="no-gallery-message text-center p-5">
                                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">No Announcements with Images</h4>
                                    <p class="text-muted">Gallery will automatically update when announcements with images are added.</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Navigation Arrows -->  
                        @if($announcementImages->count() > 1)
                            <button class="gallery-nav gallery-prev" onclick="changeImage(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="gallery-nav gallery-next" onclick="changeImage(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                        
                        <!-- Dots Indicator -->
                        @if($announcementImages->count() > 1)
                            <div class="gallery-dots">
                                @foreach($announcementImages as $index => $announcement)
                                    <span class="dot {{ $index === 0 ? 'active' : '' }}" onclick="currentImage({{ $index }})"></span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($announcements->count() > 0)
<section class="home-sections announcements-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title text-center mb-4">Latest Announcements</h2>
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
                <div class="text-center mt-4">
                    <a href="/announcements" class="btn btn-primary">View All Announcements</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<section class="home-sections collaboration-section">
    <div class="container text-center">
        <h2 class="section-title">
            We collaborate with <span class="highlight">350+ leading universities and companies</span>
        </h2>
        <div class="logo-container d-flex flex-wrap justify-content-center align-items-center">
            <div class="logo-item">
                <img src="/store/1/logo/AIU.png" alt="Collaborate logo">
            </div>
            <div class="logo-item">
                <img src="/store/1/logo/BESTARI.png" alt="Collaborate logo">
            </div>
            <div class="logo-item">
                <img src="/store/1/logo/DERBY.png" alt="Collaborate logo">
            </div>
            <div class="logo-item">
                <img src="/store/1/logo/National PHD.png" alt="Collaborate logo">
            </div>
            <div class="logo-item">
                <img src="/store/1/logo/UMM.png" alt="Collaborate logo">
            </div>
            <div class="logo-item">
                <img src="/store/1/logo/DRB.png" alt="Collaborate logo">
            </div>
            <div class="logo-item">
                <img src="/store/1/logo/ACCA.png" alt="Collaborate logo">
            </div>
        </div>
    </div>
</section>


<section class="home-sections stats-container page-has-hero-section-2">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/teacher.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10">19</strong>
                    <h4 class="stat-title">Expert Instructor</h4>
                    <p class="stat-desc mt-10">Start learning from experienced instructors.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/student.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10">0</strong>
                    <h4 class="stat-title">Registered Student</h4>
                    <p class="stat-desc mt-10">Enrolled in our courses and improved their skills.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/video.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10">126</strong>
                    <h4 class="stat-title">Live Classes</h4>
                    <p class="stat-desc mt-10">Improve your skills using live knowledge flow.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/course.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10">0</strong>
                    <h4 class="stat-title">Video Courses</h4>
                    <p class="stat-desc mt-10">Learn without any geographical &amp; time limitations.</p>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('styles')
<style>
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

