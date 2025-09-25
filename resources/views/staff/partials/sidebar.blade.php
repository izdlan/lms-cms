<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="profile-section">
            <div class="profile-picture-container">
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="profile-picture">
                @else
                    <div class="profile-picture-placeholder">
                        <i class="fas fa-user-tie"></i>
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h5 class="profile-name">{{ auth()->user()->name }}</h5>
            </div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            Dashboard
        </a>
        <a href="{{ route('staff.courses') }}" class="nav-link {{ request()->routeIs('staff.courses') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i>
            Manage Courses
        </a>
        <a href="{{ route('staff.announcements') }}" class="nav-link {{ request()->routeIs('staff.announcements') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i>
            Announcements
        </a>
        <a href="{{ route('staff.students') }}" class="nav-link {{ request()->routeIs('staff.students') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            Students
        </a>
        <a href="{{ route('staff.contents') }}" class="nav-link {{ request()->routeIs('staff.contents') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            Course Contents
        </a>
        <a href="{{ route('staff.assignments') }}" class="nav-link {{ request()->routeIs('staff.assignments') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            Assignments
        </a>
        <a href="{{ route('staff.profile') }}" class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            My Profile
        </a>
        <a href="{{ route('staff.password.change') }}" class="nav-link {{ request()->routeIs('staff.password.change') ? 'active' : '' }}">
            <i class="fas fa-key"></i>
            Change Password
        </a>
    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
