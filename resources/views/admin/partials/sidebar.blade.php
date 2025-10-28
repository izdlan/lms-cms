<!-- Modern Admin Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4>Admin Panel</h4>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            Dashboard
        </a>
        <a href="{{ route('admin.students') }}" class="nav-link {{ request()->routeIs('admin.students') && !request()->routeIs('admin.students.status') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            Students
        </a>
        <a href="{{ route('admin.students.status') }}" class="nav-link {{ request()->routeIs('admin.students.status') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data-fill"></i>
            Student Status
        </a>
        <a href="{{ route('admin.user-activity-logs') }}" class="nav-link {{ request()->routeIs('admin.user-activity-logs') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            Activity Logs
        </a>
        <a href="{{ route('admin.lecturers') }}" class="nav-link {{ request()->routeIs('admin.lecturers*') ? 'active' : '' }}">
            <i class="bi bi-person-check-fill"></i>
            Lecturers
        </a>
        <a href="{{ route('admin.ex-students') }}" class="nav-link {{ request()->routeIs('admin.ex-students*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard-fill"></i>
            Ex-Students
        </a>
        <a href="{{ route('admin.programs.index') }}" class="nav-link {{ request()->routeIs('admin.programs*') ? 'active' : '' }}">
            <i class="bi bi-book-fill"></i>
            Program Management
        </a>
        <a href="{{ route('admin.student-certificates.index') }}" class="nav-link {{ request()->routeIs('admin.student-certificates*') ? 'active' : '' }}">
            <i class="bi bi-award-fill"></i>
            Student Certificates
        </a>
        <a href="{{ route('admin.student-info.index') }}" class="nav-link {{ request()->routeIs('admin.student-info*') ? 'active' : '' }}">
            <i class="bi bi-file-pdf-fill"></i>
            Student Info PDFs
        </a>
        <a href="{{ route('admin.import') }}" class="nav-link {{ request()->routeIs('admin.import*') ? 'active' : '' }}">
            <i class="bi bi-upload"></i>
            Import Students
        </a>
        <a href="{{ route('admin.sync') }}" class="nav-link {{ request()->routeIs('admin.sync*') ? 'active' : '' }}">
            <i class="bi bi-arrow-repeat"></i>
            Sync from Excel/CSV
        </a>
        <a href="{{ route('admin.auto-sync') }}" class="nav-link {{ request()->routeIs('admin.auto-sync*') ? 'active' : '' }}">
            <i class="bi bi-cloud-upload-fill"></i>
            Auto Sync
        </a>
        <a href="{{ route('admin.announcements.preview') }}" class="nav-link {{ request()->routeIs('admin.announcements.preview') ? 'active' : '' }}">
            <i class="bi bi-bell-fill"></i>
            Announcements
        </a>
        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.index') || request()->routeIs('admin.announcements.create') || request()->routeIs('admin.announcements.edit') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i>
            Announcements Settings
        </a>
        <a href="{{ route('admin.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </nav>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

