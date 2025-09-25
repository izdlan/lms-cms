<!-- Staff Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light staff-navbar">
    <div class="container-fluid">
        <!-- Left Side -->
        <div class="navbar-nav-left">
            <a class="navbar-brand" href="{{ route('staff.dashboard') }}">
                <img src="https://lms.olympia-education.com/store/1/dark-logo.png" alt="Olympia Education" height="40">
                <span class="brand-text">
                    <strong>OLYMPIA EDUCATION</strong>
                    <small>Centre for Professional, Executive, Advanced & Continuing Education</small>
                </span>
            </a>
        </div>

        <!-- Right Side -->
        <div class="navbar-nav-right">
            @auth
                <!-- My Courses Dropdown -->
                <div class="dropdown courses-dropdown-right">
                    <button class="btn btn-outline-success dropdown-toggle courses-dropdown-btn" type="button" id="coursesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-graduation-cap"></i>
                        Manage Courses
                    </button>
                    <ul class="dropdown-menu courses-dropdown-menu" aria-labelledby="coursesDropdown">
                        <li class="dropdown-header">
                            <i class="fas fa-graduation-cap"></i>
                            Course Management
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item course-item" href="{{ route('staff.courses') }}">
                                <div class="course-icon">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <span class="course-code">All Courses</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item course-item" href="{{ route('staff.announcements') }}">
                                <div class="course-icon">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <span class="course-code">Announcements</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item course-item" href="{{ route('staff.contents') }}">
                                <div class="course-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <span class="course-code">Course Contents</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item course-item" href="{{ route('staff.assignments') }}">
                                <div class="course-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <span class="course-code">Assignments</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center" href="{{ route('staff.courses') }}">
                                <i class="fas fa-eye"></i> View All Courses
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Staff Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle staff-profile-btn" type="button" id="staffDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-info">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile" class="profile-pic-nav">
                            @else
                                <div class="profile-pic-placeholder-nav">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            @endif
                            <span class="staff-name">{{ auth()->user()->name }}</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="staffDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('staff.profile') }}">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('staff.password.change') }}">
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
