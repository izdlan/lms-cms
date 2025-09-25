<!-- Student Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light student-navbar">
    <div class="container-fluid">
        <!-- Left Side -->
        <div class="navbar-nav-left">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <img src="https://lms.olympia-education.com/store/1/dark-logo.png" alt="Olympia Education" height="40">
                <span class="brand-text">
                    <strong>OLYMPIA EDUCATION</strong>
                    <small>Centre for Professional, Executive, Advanced & Continuing Education</small>
                </span>
            </a>
        </div>

        <!-- Right Side -->
        <div class="navbar-nav-right">
            @auth('student')
                <!-- My Courses Dropdown -->
                <div class="dropdown courses-dropdown-right">
                    <button class="btn btn-outline-success dropdown-toggle courses-dropdown-btn" type="button" id="coursesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-graduation-cap"></i>
                        My Courses
                    </button>
                    <ul class="dropdown-menu courses-dropdown-menu" aria-labelledby="coursesDropdown">
                        <li class="dropdown-header">
                            <i class="fas fa-graduation-cap"></i>
                            My Courses
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @php
                            // Get actual student courses from database
                            $studentCourses = auth('student')->user()->courses ?? [];
                        @endphp
                        @if(count($studentCourses) > 0)
                            @foreach($studentCourses as $course)
                                <li>
                                    <a class="dropdown-item course-item" href="{{ route('course.summary', strtolower($course)) }}">
                                        <div class="course-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <span class="course-code">{{ $course }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <a class="dropdown-item text-muted" href="/maintenance">
                                    <i class="fas fa-info-circle"></i>
                                    No courses registered
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center" href="{{ route('student.courses') }}">
                                <i class="fas fa-eye"></i> View All Courses
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Student Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle student-profile-btn" type="button" id="studentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-info">
                            @if(auth('student')->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth('student')->user()->profile_picture) }}" alt="Profile" class="profile-pic-nav">
                            @else
                                <div class="profile-pic-placeholder-nav">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <span class="student-name">{{ auth('student')->user()->name }}</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="studentDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('student.profile') }}">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('student.password.change') }}">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <!-- Login Button for non-authenticated users -->
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            @endauth
        </div>
    </div>
</nav>
