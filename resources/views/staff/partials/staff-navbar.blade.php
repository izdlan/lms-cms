<!-- Staff Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light staff-navbar">
    <div class="container-fluid">
        <!-- Left Side -->
        <div class="navbar-nav-left">
            <a class="navbar-brand" href="{{ route('staff.dashboard') }}">
                <img src="/store/1/dark-logo.png" class="img-cover" alt="Olympia Education" height="40">
                <span class="brand-text">
                    <strong>OLYMPIA EDUCATION</strong>
                    <small>Centre for Professional, Executive, Advanced & Continuing Education</small>
                </span>
            </a>
        </div>

        <!-- Right Side -->
        <div class="navbar-nav-right">
            @auth
                <!-- Staff Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle staff-profile-btn" type="button" id="staffDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-info">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile" class="profile-pic-nav">
                            @else
                                <div class="profile-pic-placeholder-nav">
                                    <i class="fas fa-user"></i>
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
