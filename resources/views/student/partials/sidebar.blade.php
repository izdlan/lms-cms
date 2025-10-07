<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="profile-section">
            <div class="profile-picture-container">
                @if(auth('student')->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth('student')->user()->profile_picture) }}" alt="Profile Picture" class="profile-picture">
                @else
                    <div class="profile-picture-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h5 class="profile-name">{{ auth('student')->user()->name }}</h5>
            </div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            Dashboard
        </a>
        <a href="{{ route('student.courses') }}" class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
            <i class="fas fa-graduation-cap"></i>
            My Program
        </a>
        <a href="{{ route('student.profile') }}" class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            My Profile
        </a>
        <a href="{{ route('student.password.change') }}" class="nav-link {{ request()->routeIs('student.password.change') ? 'active' : '' }}">
            <i class="fas fa-key"></i>
            Change Password
        </a>
        <a href="/maintenance" class="nav-link">
            <i class="fas fa-clipboard-list"></i>
            Course Registration
        </a>
        <a href="{{ route('student.bills') }}" class="nav-link {{ request()->routeIs('student.bills') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar"></i>
            Student Bills
        </a>
        <a href="{{ route('student.exam-results') }}" class="nav-link {{ request()->routeIs('student.exam-results') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            Exam Results
        </a>

    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
