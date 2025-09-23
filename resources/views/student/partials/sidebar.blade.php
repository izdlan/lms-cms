<!-- Sidebar -->
<div class="col-md-3 col-lg-2 sidebar">
    <div class="sidebar-header">
        <h4>Student Portal</h4>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i data-feather="home" width="20" height="20"></i>
            Dashboard
        </a>
        <a href="{{ route('student.courses') }}" class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
            <i data-feather="book-open" width="20" height="20"></i>
            My Courses
        </a>
        <a href="{{ route('student.password.change') }}" class="nav-link {{ request()->routeIs('student.password.change') ? 'active' : '' }}">
            <i data-feather="key" width="20" height="20"></i>
            Change Password
        </a>
        <a href="{{ route('student.assignments') }}" class="nav-link {{ request()->routeIs('student.assignments') ? 'active' : '' }}">
            <i data-feather="file-text" width="20" height="20"></i>
            Assignments
        </a>
        <a href="{{ route('student.profile') }}" class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
            <i data-feather="user" width="20" height="20"></i>
            Profile
        </a>
        <a href="{{ route('student.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i data-feather="log-out" width="20" height="20"></i>
            Logout
        </a>
    </nav>
    <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
