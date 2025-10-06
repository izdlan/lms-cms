<!-- Admin Sidebar -->
<div class="col-md-3 col-lg-2 sidebar">
    <div class="sidebar-header">
        <h4>Admin Panel</h4>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i data-feather="home" width="20" height="20"></i>
            Dashboard
        </a>
        <a href="{{ route('admin.students') }}" class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
            <i data-feather="users" width="20" height="20"></i>
            Students
        </a>
            <a href="{{ route('admin.lecturers') }}" class="nav-link {{ request()->routeIs('admin.lecturers*') ? 'active' : '' }}">
                <i data-feather="user-check" width="20" height="20"></i>
                Lecturers
            </a>
            <a href="{{ route('admin.ex-students') }}" class="nav-link {{ request()->routeIs('admin.ex-students*') ? 'active' : '' }}">
                <i data-feather="graduation-cap" width="20" height="20"></i>
                Ex-Students
            </a>
        <a href="{{ route('admin.import') }}" class="nav-link {{ request()->routeIs('admin.import*') ? 'active' : '' }}">
            <i data-feather="upload" width="20" height="20"></i>
            Import Students
        </a>
        <a href="{{ route('admin.sync') }}" class="nav-link {{ request()->routeIs('admin.sync*') ? 'active' : '' }}">
            <i data-feather="refresh-cw" width="20" height="20"></i>
            Sync from Excel/CSV
        </a>
        <a href="{{ route('admin.auto-sync') }}" class="nav-link {{ request()->routeIs('admin.auto-sync*') ? 'active' : '' }}">
            <i data-feather="cloud-upload" width="20" height="20"></i>
            Auto Sync
        </a>
        <a href="{{ route('admin.announcements.preview') }}" class="nav-link {{ request()->routeIs('admin.announcements.preview') ? 'active' : '' }}">
            <i data-feather="bell" width="20" height="20"></i>
            Announcements
        </a>
        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.index') || request()->routeIs('admin.announcements.create') || request()->routeIs('admin.announcements.edit') ? 'active' : '' }}">
            <i data-feather="settings" width="20" height="20"></i>
            Announcements Settings
        </a>
        <a href="{{ route('admin.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i data-feather="log-out" width="20" height="20"></i>
            Logout
        </a>
    </nav>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

