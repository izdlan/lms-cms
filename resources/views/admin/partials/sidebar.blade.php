<!-- Modern Admin Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4>Admin Panel</h4>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i data-feather="home"></i>
            Dashboard
        </a>
        <a href="{{ route('admin.students') }}" class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
            <i data-feather="users"></i>
            Students
        </a>
        <a href="{{ route('admin.lecturers') }}" class="nav-link {{ request()->routeIs('admin.lecturers*') ? 'active' : '' }}">
            <i data-feather="user-check"></i>
            Lecturers
        </a>
        <a href="{{ route('admin.ex-students') }}" class="nav-link {{ request()->routeIs('admin.ex-students*') ? 'active' : '' }}">
            <i data-feather="graduation-cap"></i>
            Ex-Students
        </a>
        <a href="{{ route('admin.import') }}" class="nav-link {{ request()->routeIs('admin.import*') ? 'active' : '' }}">
            <i data-feather="upload"></i>
            Import Students
        </a>
        <a href="{{ route('admin.sync') }}" class="nav-link {{ request()->routeIs('admin.sync*') ? 'active' : '' }}">
            <i data-feather="refresh-cw"></i>
            Sync from Excel/CSV
        </a>
        <a href="{{ route('admin.auto-sync') }}" class="nav-link {{ request()->routeIs('admin.auto-sync*') ? 'active' : '' }}">
            <i data-feather="cloud-upload"></i>
            Auto Sync
        </a>
        <a href="{{ route('admin.announcements.preview') }}" class="nav-link {{ request()->routeIs('admin.announcements.preview') ? 'active' : '' }}">
            <i data-feather="bell"></i>
            Announcements
        </a>
        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.index') || request()->routeIs('admin.announcements.create') || request()->routeIs('admin.announcements.edit') ? 'active' : '' }}">
            <i data-feather="settings"></i>
            Announcements Settings
        </a>
        <a href="{{ route('admin.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i data-feather="log-out"></i>
            Logout
        </a>
    </nav>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

