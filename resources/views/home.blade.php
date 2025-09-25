@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="slider-container  slider-hero-section2" style="background-image: url('/assets/default/img/home/world.png')">
    <div class="container user-select-none">
        <div class="row slider-content align-items-center hero-section2 flex-column-reverse flex-md-row">
            <div class="col-12 col-md-7 col-lg-6">
                <h1 class="text-secondary font-weight-bold">Advance Your Career with Olympia Education</h1>
                <p class="slide-hint text-gray mt-15">We offer industry relevant programs, hands-on learning, and strong career support empowering students with the skills, knowledge, and confidence to thrive in today's fast-paced world.</p>

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
                        <li><a href="/maintenance" class="top-link-item">LMS</a></li>
                        <li><a href="/login" class="top-link-item">Login</a></li>
                        <li><a href="/maintenance" class="top-link-item">Course Registration</a></li>
                        <li><a href="/maintenance" class="top-link-item">Student Bills</a></li>
                        <li><a href="/maintenance" class="top-link-item">Exam Result</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Single Image Gallery with Navigation -->
            <div class="col-md-10 col-lg-10 gallery-container">
                <div class="single-gallery">
                    <div class="gallery-main">
                        <div class="gallery-image-container">
                            <img src="/assets/default/img/home/poster1.jpeg" alt="Olympia Education Poster 1" class="main-gallery-image active" data-index="0" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                            <img src="/assets/default/img/home/poster2.jpeg" alt="Olympia Education Poster 2" class="main-gallery-image" data-index="1" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                            <img src="/assets/default/img/home/poster3.jpeg" alt="Olympia Education Poster 3" class="main-gallery-image" data-index="2" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                            <img src="/assets/default/img/home/poster4.jpeg" alt="Olympia Education Poster 4" class="main-gallery-image" data-index="3" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                            <img src="/assets/default/img/home/poster5.jpeg" alt="Olympia Education Poster 5" class="main-gallery-image" data-index="4" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                            <img src="/assets/default/img/home/poster6.jpg" alt="Olympia Education Poster 6" class="main-gallery-image" data-index="5" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                            <img src="/assets/default/img/home/poster7.jpg" alt="Olympia Education Poster 7" class="main-gallery-image" data-index="6" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
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
                            <span class="dot active" onclick="currentImage(0)"></span>
                            <span class="dot" onclick="currentImage(1)"></span>
                            <span class="dot" onclick="currentImage(2)"></span>
                            <span class="dot" onclick="currentImage(3)"></span>
                            <span class="dot" onclick="currentImage(4)"></span>
                            <span class="dot" onclick="currentImage(5)"></span>
                            <span class="dot" onclick="currentImage(6)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

<section class="degree-programs-section">
    <div class="container">
        <div class="header-content mb-5">
            <p class="eyebrow-text">Degree Programs</p>
            <h2 class="section-title">Find a top degree that fits your life</h2>
            <p class="section-subtitle">Breakthrough pricing on 100% online degrees from top universities.</p>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="degree-card">
                    <div class="card-image-wrapper">
                        <img src="/store/1/content/image-1.jpg" alt="Bachelor of Information Technology" class="card-img-top">
                    </div>
                    <div class="card-body">
                        <div class="card-institution">
                            <img src="/store/1/logo/UCB.png" alt="University College Bestari Logo" class="institution-logo">
                            <span>University College Bestari</span>
                        </div>
                        <h5 class="card-title"><a href="/maintenance">Bachelor of Information Technology</a></h5>
                        <div class="card-footer-info">
                            <a href="/maintenance" class="footer-link">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M3.03 11.88q.17.3.45.43l3.25 1.64a1 1 0 0 0 .27.09c.17.03.36.03.53 0q.13-.02.27-.1l3.25-1.62q.3-.15.45-.45.16-.29.16-.62V8.02l1.6-.8v4q0 .23.18.41a.56.56 0 0 0 .42.19.58.58 0 0 0 .42-.19.57.57 0 0 0 .18-.41V6.96a.56.56 0 0 0-.08-.3.6.6 0 0 0-.23-.21L7.8 3.28a1.12 1.12 0 0 0-1.07 0l-5.6 2.8a.6.6 0 0 0-.25.22.6.6 0 0 0-.08.31q0 .18.08.32t.25.22l1.73.87v3.23q0 .33.17.63m7.75-4.76L7.26 8.87 2.75 6.62l4.51-2.25 3.52 1.75H7.2a.5.5 0 0 0 0 1zm-.32 4.15-3.2 1.58-3.2-1.59V8.63l2.62 1.3q.13.09.28.12c.2.04.4.04.6 0q.14-.04.29-.12l2.61-1.3z" clip-rule="evenodd"/></svg>
                                <span>Earn a degree</span>
                            </a>
                            <small class="footer-note">Degree</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="degree-card">
                    <div class="card-image-wrapper">
                        <img src="/store/1/content/image-2.jpg" alt="Bachelor of Mechanical Engineering Technology" class="card-img-top">
                    </div>
                    <div class="card-body">
                        <div class="card-institution">
                            <img src="/store/1/logo/UCB.png" alt="University College Bestari Logo" class="institution-logo">
                            <span>University College Bestari</span>
                        </div>
                        <h5 class="card-title"><a href="/maintenance">Bachelor of Mechanical Engineering Technology</a></h5>
                        <div class="card-footer-info">
                            <a href="/maintenance" class="footer-link">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M3.03 11.88q.17.3.45.43l3.25 1.64a1 1 0 0 0 .27.09c.17.03.36.03.53 0q.13-.02.27-.1l3.25-1.62q.3-.15.45-.45.16-.29.16-.62V8.02l1.6-.8v4q0 .23.18.41a.56.56 0 0 0 .42.19.58.58 0 0 0 .42-.19.57.57 0 0 0 .18-.41V6.96a.56.56 0 0 0-.08-.3.6.6 0 0 0-.23-.21L7.8 3.28a1.12 1.12 0 0 0-1.07 0l-5.6 2.8a.6.6 0 0 0-.25.22.6.6 0 0 0-.08.31q0 .18.08.32t.25.22l1.73.87v3.23q0 .33.17.63m7.75-4.76L7.26 8.87 2.75 6.62l4.51-2.25 3.52 1.75H7.2a.5.5 0 0 0 0 1zm-.32 4.15-3.2 1.58-3.2-1.59V8.63l2.62 1.3q.13.09.28.12c.2.04.4.04.6 0q.14-.04.29-.12l2.61-1.3z" clip-rule="evenodd"/></svg>
                                <span>Earn a degree</span>
                            </a>
                            <small class="footer-note">Degree</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="degree-card">
                    <div class="card-image-wrapper">
                        <img src="/store/1/content/image-3.jpg" alt="Diploma in Procurement Management" class="card-img-top">
                    </div>
                    <div class="card-body">
                        <div class="card-institution">
                            <img src="/store/1/logo/DHU.png" alt="DRB-Hicom University Logo" class="institution-logo">
                            <span>DRB-Hicom University</span>
                        </div>
                        <h5 class="card-title"><a href="/maintenance">Diploma in Procurement Management</a></h5>
                        <div class="card-footer-info">
                            <a href="/maintenance" class="footer-link">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M3.03 11.88q.17.3.45.43l3.25 1.64a1 1 0 0 0 .27.09c.17.03.36.03.53 0q.13-.02.27-.1l3.25-1.62q.3-.15.45-.45.16-.29.16-.62V8.02l1.6-.8v4q0 .23.18.41a.56.56 0 0 0 .42.19.58.58 0 0 0 .42-.19.57.57 0 0 0 .18-.41V6.96a.56.56 0 0 0-.08-.3.6.6 0 0 0-.23-.21L7.8 3.28a1.12 1.12 0 0 0-1.07 0l-5.6 2.8a.6.6 0 0 0-.25.22.6.6 0 0 0-.08.31q0 .18.08.32t.25.22l1.73.87v3.23q0 .33.17.63m7.75-4.76L7.26 8.87 2.75 6.62l4.51-2.25 3.52 1.75H7.2a.5.5 0 0 0 0 1zm-.32 4.15-3.2 1.58-3.2-1.59V8.63l2.62 1.3q.13.09.28.12c.2.04.4.04.6 0q.14-.04.29-.12l2.61-1.3z" clip-rule="evenodd"/></svg>
                                <span>Earn a degree</span>
                            </a>
                            <small class="footer-note">Degree</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="degree-card">
                    <div class="card-image-wrapper">
                        <img src="/store/1/content/image-4.jpg" alt="Diploma in Procurement Management" class="card-img-top">
                    </div>
                    <div class="card-body">
                        <div class="card-institution">
                            <img src="/store/1/logo/DHU.png" alt="DRB-Hicom University Logo" class="institution-logo">
                            <span>DRB-Hicom University</span>
                        </div>
                        <h5 class="card-title"><a href="/maintenance">Diploma in Procurement Management</a></h5>
                        <div class="card-footer-info">
                            <a href="/maintenance" class="footer-link">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M3.03 11.88q.17.3.45.43l3.25 1.64a1 1 0 0 0 .27.09c.17.03.36.03.53 0q.13-.02.27-.1l3.25-1.62q.3-.15.45-.45.16-.29.16-.62V8.02l1.6-.8v4q0 .23.18.41a.56.56 0 0 0 .42.19.58.58 0 0 0 .42-.19.57.57 0 0 0 .18-.41V6.96a.56.56 0 0 0-.08-.3.6.6 0 0 0-.23-.21L7.8 3.28a1.12 1.12 0 0 0-1.07 0l-5.6 2.8a.6.6 0 0 0-.25.22.6.6 0 0 0-.08.31q0 .18.08.32t.25.22l1.73.87v3.23q0 .33.17.63m7.75-4.76L7.26 8.87 2.75 6.62l4.51-2.25 3.52 1.75H7.2a.5.5 0 0 0 0 1zm-.32 4.15-3.2 1.58-3.2-1.59V8.63l2.62 1.3q.13.09.28.12c.2.04.4.04.6 0q.14-.04.29-.12l2.61-1.3z" clip-rule="evenodd"/></svg>
                                <span>Earn a Professional Certificate</span>
                            </a>
                            <small class="footer-note">Certificate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-actions mt-5">
            <a href="/maintenance" class="btn btn-primary">Show 8 more</a>
            <a href="/maintenance" class="btn btn-outline-primary ms-3">View all →</a>
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

<section class="home-sections home-sections-swiper container">
    <div class="d-flex justify-content-between ">
        <div>
            <h2 class="section-title">Newest Courses</h2>
            <p class="section-hint">#Recently published courses</p>
        </div>

        <a href="{{ url('/classes') }}?sort=newest" class="btn btn-border-white">View All</a>
    </div>

    <div class="mt-10 position-relative">
        <div class="swiper-container latest-webinars-swiper px-12">
            <div class="swiper-wrapper py-20">
                <div class="swiper-slide">
                    <div class="webinar-card">
                        <figure>
                            <div class="image-box">
                                <div class="badges-lists">
                                    <span class="badge badge-secondary">Finished</span>
                                </div>

                                <a href="https://lms.olympia-education.com/course/Industrial-Bachelor-of-Education-in-Leadership-Management">
                                    <img src="/store/1/Courses/DOCTORATE'S PROGRAMMES/Industrial Bachelor of Education in Leadership Management-1.jpg" class="img-cover" alt="Industrial Bachelor of Education in Leadership Management">
                                </a>

                                <div class="progress">
                                    <span class="progress-bar" style="width: 0%"></span>
                                </div>

                            </div>

                            <figcaption class="webinar-card-body">
                                <div class="user-inline-avatar d-flex align-items-center">
                                    <div class="avatar bg-gray200">
                                        <img src="/store/1/Instructor/image 19766.png" class="img-cover" alt="Prof Madya Dr. Norreha Binti Othman">
                                    </div>
                                    <a href="/users/1047/profile" target="_blank" class="user-name ml-5 font-14">Prof Madya Dr. Norreha Binti Othman</a>
                                </div>

                                <a href="https://lms.olympia-education.com/course/Industrial-Bachelor-of-Education-in-Leadership-Management">
                                    <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">Industrial Bachelor of Education in Leadership Management</h3>
                                </a>

                                <span class="d-block font-14 mt-10">in <a href="/categories/DOCTORATE-S-PROGRAMMES" target="_blank" class="text-decoration-underline">DOCTORATE'S PROGRAMMES</a></span>

                                <div class="stars-card d-flex align-items-center  mt-15">
                                </div>

                                <div class="d-flex justify-content-between mt-20">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                        <span class="duration font-14 ml-5">60:00 Hours</span>
                                    </div>

                                    <div class="vertical-line mx-15"></div>

                                    <div class="d-flex align-items-center">
                                        <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                        <span class="date-published font-14 ml-5">10 Jul 2025</span>
                                    </div>
                                </div>

                                <div class="webinar-price-box mt-25">
                                    <span class="real font-14">Free</span>
                                </div>
                            </figcaption>
                        </figure>
                    </div>
                </div>
                <!-- Additional course slides would go here -->
            </div>

            <div class="d-flex justify-content-center">
                <div class="swiper-pagination latest-webinars-swiper-pagination"></div>
            </div>
        </div>
    </div>
</section>

<section class="home-sections business-talent-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-6 text-center text-lg-left mb-5 mb-lg-0">
                <div class="content-wrapper">
                    <h2 class="font-36 font-weight-bold text-dark">
                        Drive your business forward by empowering your talent
                    </h2>
                    <p class="font-16 font-weight-normal mt-10">
                        Train teams with industry-leading experts and universities, enhanced by AI tools and recognized credentials.
                    </p>

                    <a href="/maintenance" class="btn btn-primary btn-lg px-4 mt-3">Discover Olympia for Business</a>

                    <div class="up-skill-link mt-3">
                        <p>
                            Upskill a small team?
                            <a href="/maintenance" class="text-primary font-weight-bold">Check out Olympia for Teams</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="logo-grid">
                    <div class="logo-item">
                        <img src="/store/1/business_talent/Capgemini Logo.png" alt="Drive your business forward by empowering your talent Image 0">
                    </div>
                    <div class="logo-item">
                        <img src="/store/1/business_talent/Danone Logo.png" alt="Drive your business forward by empowering your talent Image 1">
                    </div>
                    <div class="logo-item">
                        <img src="/store/1/business_talent/Emirates NBD Logo.png" alt="Drive your business forward by empowering your talent Image 2">
                    </div>
                    <div class="logo-item">
                        <img src="/store/1/business_talent/General Electric Logo.png" alt="Drive your business forward by empowering your talent Image 3">
                    </div>
                    <div class="logo-item">
                        <img src="https://lms.olympia-education.com/store/1/business_talent/L'OREAL%20Logo.png" alt="Drive your business forward by empowering your talent Image 4">
                    </div>
                    <div class="logo-item">
                        <img src="/store/1/business_talent/Procter &amp; Gamble Logo.png" alt="Drive your business forward by empowering your talent Image 5">
                    </div>
                    <div class="logo-item">
                        <img src="/store/1/business_talent/Reliance Industries Limited Logo.png" alt="Drive your business forward by empowering your talent Image 6">
                    </div>
                    <div class="logo-item">
                        <img src="/store/1/business_talent/Tata Communications Logo.png" alt="Drive your business forward by empowering your talent Image 7">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="home-sections next-steps-section">
    <div class="container">
        <h2 class="section-heading">NEXT STEPS</h2>
        <div class="cta-card">
            <h3 class="card-title">APPLY TO OLYMPIA</h3>
            <p class="card-text">
                You have a good sense of what you want to achieve and the impact you want to make on the world. Are you ready for your bigger goals? Apply to begin your journey as a #FutureOlympia.
            </p>
            <a href="/maintenance" class="btn btn-cta">START YOUR APPLICATION</a>
        </div>
    </div>
</section>
@endsection
