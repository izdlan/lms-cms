<!-- Finance Admin Header -->
<nav class="navbar navbar-expand-lg navbar-light finance-admin-navbar">
    <div class="container-fluid">
        <!-- Left Side -->
        <div class="navbar-nav-left">
            <a class="navbar-brand" href="{{ route('finance-admin.dashboard') }}">
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
                <!-- Finance Admin Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle finance-admin-profile-btn" type="button" id="financeAdminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-info">
                            @if(auth()->user() && auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile" class="profile-pic-nav">
                            @else
                                <div class="profile-pic-placeholder-nav">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            @endif
                            <span class="staff-name">{{ auth()->user()->name ?? 'Finance Admin' }}</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="financeAdminDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('finance-admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('finance-admin.students') }}">
                                <i class="fas fa-users"></i> Students
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('finance-admin.pending-payments') }}">
                                <i class="fas fa-clock"></i> Pending Payments
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('finance-admin.password.change') }}">
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

