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
        <a href="{{ route('admin.import') }}" class="nav-link {{ request()->routeIs('admin.import*') ? 'active' : '' }}">
            <i data-feather="upload" width="20" height="20"></i>
            Import Students
        </a>
        <a href="{{ route('admin.sync') }}" class="nav-link {{ request()->routeIs('admin.sync*') ? 'active' : '' }}">
            <i data-feather="refresh-cw" width="20" height="20"></i>
            Sync from Excel/CSV
        </a>
        <a href="{{ route('admin.automation.onedrive') }}" class="nav-link {{ request()->routeIs('admin.automation.onedrive*') ? 'active' : '' }}">
            <i data-feather="cloud-upload" width="20" height="20"></i>
            OneDrive Auto Import
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

