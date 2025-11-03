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
                        <li>
                            <a href="/login" class="top-link-item">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="/login" class="top-link-item">
                                <i class="fa-solid fa-clipboard-list me-2"></i>
                                Course Registration
                            </a>
                        </li>
                        <li>
                            <a href="/login" class="top-link-item">
                                <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                                Student Bills
                            </a>
                        </li>
                        <li>
                            <a href="/login" class="top-link-item">
                                <i class="fa-solid fa-graduation-cap me-2"></i>
                                Exam Result
                            </a>
                        </li>
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
                <h2 class="section-title text-center mb-4 title-with-underline">
                    <i class="fas fa-bullhorn me-2"></i>
                    Latest Announcements
                </h2>
                <div class="row">
                    @foreach($announcements as $announcement)
                        <div class="col-md-4 mb-4">
                                <div class="announcement-card">
                                    @if($announcement->image_url)
                                            <div class="announcement-image">
                                    <img src="{{ Str::startsWith($announcement->image_url, ['http://','https://']) ? $announcement->image_url : asset(ltrim($announcement->image_url,'/')) }}" alt="{{ $announcement->title }}" class="img-fluid">
                                        </div>
                                    @endif
                                    <div class="announcement-content">
                                    <div class="announcement-meta">
                                        <span class="badge badge-{{ $announcement->priority === 'high' ? 'danger' : ($announcement->priority === 'medium' ? 'warning' : 'success') }}">
                                                {{ ucfirst($announcement->priority) }}
                                            </span>
                                        <span class="announcement-category"><i class="fas fa-tag"></i> {{ ucfirst($announcement->category) }}</span>
                                        </div>
                                    <h4 class="announcement-title"><i class="fas fa-bullhorn"></i> {{ $announcement->title }}</h4>
                                    <p class="announcement-excerpt"><i class="fas fa-file-alt"></i> {{ Str::limit(strip_tags($announcement->content), 100) }}</p>
                                            <div class="announcement-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $announcement->published_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4 mb-0">
                    <a href="/announcements" class="btn btn-primary">
                        <i class="fa-solid fa-bullhorn me-2"></i>
                        View All Announcements
                    </a>
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
    </div>
    <!-- Full-width marquee -->
    <div class="logo-marquee">
            <div class="logo-track">
                <img src="/store/1/logo/AIU.png" alt="Collaborate logo">
                <img src="/store/1/logo/BESTARI.png" alt="Collaborate logo">
                <img src="/store/1/logo/DERBY.png" alt="Collaborate logo">
                <img src="/store/1/logo/National PHD.png" alt="Collaborate logo">
                <img src="/store/1/logo/UMM.png" alt="Collaborate logo">
                <img src="/store/1/logo/DRB.png" alt="Collaborate logo">
                <img src="/store/1/logo/ACCA.png" alt="Collaborate logo">
                <!-- duplicate for seamless loop -->
                <img src="/store/1/logo/AIU.png" alt="Collaborate logo">
                <img src="/store/1/logo/BESTARI.png" alt="Collaborate logo">
                <img src="/store/1/logo/DERBY.png" alt="Collaborate logo">
                <img src="/store/1/logo/National PHD.png" alt="Collaborate logo">
                <img src="/store/1/logo/UMM.png" alt="Collaborate logo">
                <img src="/store/1/logo/DRB.png" alt="Collaborate logo">
                <img src="/store/1/logo/ACCA.png" alt="Collaborate logo">
        </div>
    </div>
</section>

@push('styles')
<style>
/* Student Portal icons */
.top-links-list .top-link-item:before { content: none !important; }
.top-links-list .top-link-item i { color: #0b2a55 !important; width: 18px !important; text-align: center !important; }
.top-links-list .top-link-item { display: flex !important; align-items: center !important; gap: 8px !important; }
.collaboration-section { 
    padding: 40px 0 20px 0; 
    margin-top: 0 !important; 
    background: #ffffff !important;
}
.logo-marquee { overflow: hidden; position: relative; width: 100vw; left: 50%; transform: translateX(-50%); }
.logo-track {
    display: inline-flex; align-items: center; gap: 160px; /* wider gap between logos */
    white-space: nowrap; will-change: transform;
    animation: marquee-left 30s linear infinite;
}
.logo-track img { height: 56px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.12)); opacity: .98; }
@keyframes marquee-left {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@media (max-width: 768px) {
    .logo-track { gap: 64px; animation-duration: 20s; }
    .logo-track img { height: 36px; }
}
</style>
@endpush

@push('scripts')
<script>
// Animated counters with lightening color and trailing plus sign
document.addEventListener('DOMContentLoaded', function () {
    const counters = document.querySelectorAll('.counter');
    if (!counters.length) return;

    // blue tone (same as before)
    const baseColor = { r: 30, g: 64, b: 175 };      // deep blue
    const lightColor = { r: 158, g: 197, b: 254 };   // light blue

    function lerp(a, b, t) { return a + (b - a) * t; }
    function blend(c1, c2, t) {
        return `rgb(${Math.round(lerp(c1.r, c2.r, t))}, ${Math.round(lerp(c1.g, c2.g, t))}, ${Math.round(lerp(c1.b, c2.b, t))})`;
    }

    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-target') || '0', 10);
        const duration = 1600 + Math.min(2400, target * 12); // bigger number => slightly longer
        const start = performance.now();

        function frame(now) {
            const progress = Math.min(1, (now - start) / duration);
            const current = Math.floor(target * progress);
            el.textContent = `${current}+`;
            // make color lighter as it grows
            el.style.color = blend(baseColor, lightColor, progress);
            if (progress < 1) {
                requestAnimationFrame(frame);
            } else {
                // pause for 4 seconds, then loop again from 0
                setTimeout(() => {
                    el.textContent = '0+';
                    el.style.color = blend(baseColor, lightColor, 0);
                    animateCounter(el);
                }, 4000);
            }
        }
        requestAnimationFrame(frame);
    }

    // Run when the stats block enters the viewport
    const io = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                counters.forEach(animateCounter);
                obs.disconnect();
            }
        });
    }, { threshold: 0.2 });

    const statsSection = document.querySelector('.stats-container');
    if (statsSection) io.observe(statsSection); else counters.forEach(animateCounter);
});
</script>
@endpush

@push('styles')
<style>
/* Keep page background neutral; section provides its own background */
body { background: #ffffff !important; }
/* Stats section styling to match reference */
.stats-container { padding: 0 !important; }
.stats-container > .container { padding-top: 0 !important; padding-bottom: 0 !important; margin-bottom: -10px !important; }
.stats-container .row { margin-top: 0 !important; margin-bottom: 0 !important; }
.stats-container .row > [class^="col-"] { padding-bottom: 0 !important; margin-bottom: 0 !important; }
.home-sections.stats-container { margin-bottom: 0 !important; padding-bottom: 0 !important; padding-top: 0 !important; min-height: 0 !important; }
/* Background image for stats section */
.home-sections.stats-container {
    background: url('/assets/default/img/home/annoucement.jpg') center center / cover no-repeat !important;
    min-height: 480px; /* slightly shorter while keeping image visible */
    margin-top: 0 !important; /* remove gap above */
    margin-bottom: 0 !important; /* remove gap below */
    padding: 40px 0 !important; /* moderate breathing room */
    overflow: hidden !important;
    position: relative !important; /* enable overlay */
}
/* dark overlay to improve text contrast */
.home-sections.stats-container:before {
    content: "";
    position: absolute !important;
    inset: 0 !important;
    background: linear-gradient(180deg, rgba(0,0,0,.25), rgba(0,0,0,.35)) !important;
    pointer-events: none !important;
    z-index: 0 !important;
}
.home-sections.stats-container > .container { position: relative !important; z-index: 1 !important; }
/* Remove top margin on footer band to eliminate white gap below section */
/* Remove top gap before footer caused by global footer margin */
.footer { margin-top: 0 !important; }
.footer .mt-40 { margin-top: 0 !important; }
/* Remove any top margin from the next section to prevent white gap */
.home-sections.stats-container + section { margin-top: 0 !important; }
/* Comfortable vertical spacing inside stats section */
.stats-container .mt-25 { margin-top: 12px !important; }
.stats-container .row > [class^="col-"] { padding-top: 6px !important; padding-bottom: 6px !important; }
.stats-container .stats-item { padding-top: 12px !important; padding-bottom: 12px !important; margin-top: 6px !important; margin-bottom: 6px !important; }
.stats-container .stat-icon-box { margin-bottom: 12px !important; }
.stats-container .stat-number { margin: 8px 0 !important; }
.stats-container .stat-title { margin: 4px 0 0 0 !important; }
.stats-container .stat-desc { margin-top: 8px !important; margin-bottom: 0 !important; color: #f1f5f9 !important; text-shadow: 0 1px 6px rgba(0,0,0,.35); }
/* Minimize paragraph spacing inside stats */
.stats-container p { margin-top: 4px !important; margin-bottom: 0 !important; }

/* Safety: neutralize any bootstrap spacing utilities that add bottom gap here */
.stats-container [class*="pb-"],
.stats-container [class*="mb-"] {
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}
.stats-item { background: transparent !important; border: none !important; box-shadow: none !important; }
.stats-item .stat-icon-box { 
    width: 84px; height: 84px; border-radius: 50%; background: rgba(255,255,255,0.9);
    display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0,0,0,.12);
}
.stats-item .stat-icon-box img { width: 48px; height: 48px; object-fit: contain; }
.stats-item .stat-number { font-size: 42px; font-weight: 700; letter-spacing: .5px; text-shadow: 0 2px 8px rgba(0,0,0,.35); }
.stats-item .stat-title { color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,.35); }

/* Title underline (blue gradient) */
.title-with-underline {
    position: relative;
    display: flex; /* full-width flex container */
    width: 100%;
    align-items: center;
    justify-content: center; /* center content horizontally */
    gap: 10px; /* space between icon and text */
    padding-bottom: 8px;
    color: #0b2a55; /* deep blue */
}
.title-with-underline i { color: #0b2a55; margin: 0; }
.title-with-underline:after {
    content: "";
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: -6px;
    width: 180px;
    height: 4px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(30,64,175,0), rgba(59,130,246,0.8), rgba(30,64,175,0));
    box-shadow: 0 3px 10px rgba(59,130,246,.35);
}
/* Remove blue gap between sections */
.top-links-section { margin-bottom: 0 !important; padding-bottom: 0 !important; }
.announcements-section { margin-top: 0 !important; }
/* Remove blue divider between hero and top-links */
.slider-hero-section2 { margin-bottom: 0 !important; padding-bottom: 0 !important; border-bottom: 0 !important; }
.home-sections.top-links-section { margin-top: 0 !important; padding-top: 0 !important; border-top: 0 !important; }
/* Announcements CTA icon alignment */
.announcements-section .btn i { width: 16px !important; text-align: center !important; }
/* Remove gap between collaboration and stats sections */
.home-sections.collaboration-section { margin-bottom: 0 !important; padding-bottom: 32px !important; }
.home-sections.collaboration-section .container { margin-bottom: 0 !important; padding-bottom: 16px !important; }
.logo-marquee { margin-bottom: 0 !important; padding-bottom: 16px !important; }

/* Announcements Section Styling - Light Theme */
.announcement-card {
    background: #ffffff !important;
    border-radius: 14px;
    border: 1px solid rgba(30, 64, 175, 0.15);
    box-shadow: none !important;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, background 0.25s ease;
    height: 100%;
}

.announcement-card:hover {
    transform: translateY(-4px);
    background: rgba(248, 249, 250, 1) !important;
    border-color: rgba(30, 64, 175, 0.35);
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.1) !important;
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
    padding: 18px 18px 20px 18px;
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
    font-weight: 600;
    letter-spacing: 0.03em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.announcement-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    line-height: 1.35;
    display: flex;
    align-items: center;
    gap: 8px;
}

.announcement-excerpt {
    color: #495057;
    font-size: 14px;
    line-height: 1.55;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.announcement-date {
    color: #6c757d;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.announcement-date i {
    margin-right: 5px;
}

/* Section styling */
.announcements-section {
    background: #f8f9fa !important;
    padding-top: 40px !important;
    padding-bottom: 0 !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    position: relative !important;
    overflow: hidden !important;
}

.announcements-section::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: url('{{ asset('assets/default/img/annoucement.jpg') }}'), url('{{ asset('assets/default/img/announcement.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(6px);
    transform: scale(1.05);
    z-index: 0;
    opacity: 0.15;
}

.announcements-section::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(248, 249, 250, 0.7);
    z-index: 0;
}

.announcements-section .container,
.announcements-section .container-fluid,
.announcements-section .row {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    position: relative !important;
    z-index: 1 !important;
}

.announcements-section .section-title {
    color: #0b2a55;
    font-weight: 800;
    letter-spacing: -0.01em;
}

.section-header {
    position: relative;
    padding-bottom: 14px;
    display: inline-block;
}

.section-header:after {
    content: "";
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 0;
    width: 220px;
    height: 3px;
    background: linear-gradient(90deg, rgba(30, 64, 175, 0) 0%, #1e40af 50%, rgba(30, 64, 175, 0) 100%);
    border-radius: 3px;
}

.announcements-section .badge-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.announcements-section .badge-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.announcements-section .badge-success {
    background-color: #28a745 !important;
    color: white !important;
}

.announcements-section .btn-primary {
    background: linear-gradient(90deg, #3b82f6 0%, #1e40af 100%);
    border: 1px solid rgba(30, 64, 175, 0.65);
    color: #ffffff;
    border-radius: 12px;
    padding: 12px 24px;
    box-shadow: 0 10px 24px rgba(30, 64, 175, 0.22);
    transition: transform 0.15s ease, filter 0.15s ease;
}

.announcements-section .btn-primary:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
    color: #ffffff;
}

.announcements-section .btn-primary i {
    margin-right: 8px;
}

.announcements-section .text-center {
    margin-bottom: 30px !important;
    padding-bottom: 0 !important;
}

/* Icon styling */
.announcement-category i,
.announcement-title i,
.announcement-excerpt i,
.announcement-date i {
    font-size: inherit;
}
</style>
@endpush

<section class="home-sections stats-container page-has-hero-section-2">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/teacher.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10 counter" data-target="19">0+</strong>
                    <h4 class="stat-title">Expert Instructor</h4>
                    <p class="stat-desc mt-10">Start learning from experienced instructors.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/student.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10 counter" data-target="0">0+</strong>
                    <h4 class="stat-title">Registered Student</h4>
                    <p class="stat-desc mt-10">Enrolled in our courses and improved their skills.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/video.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10 counter" data-target="126">0+</strong>
                    <h4 class="stat-title">Live Classes</h4>
                    <p class="stat-desc mt-10">Improve your skills using live knowledge flow.</p>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                    <div class="stat-icon-box">
                        <img src="/assets/default/img/stats/course.svg" alt="" class="img-fluid" />
                    </div>
                    <strong class="stat-number mt-10 counter" data-target="0">0+</strong>
                    <h4 class="stat-title">Video Courses</h4>
                    <p class="stat-desc mt-10">Learn without any geographical &amp; time limitations.</p>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection


