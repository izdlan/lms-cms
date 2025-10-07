<!-- Modern Admin Header -->
<header class="admin-header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <div class="admin-logo">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center">
                    <img src="/store/1/dark-logo.png" class="img-cover" alt="Olympia Education Logo">
                </a>
            </div>

            <!-- Admin Title -->
            <div class="admin-title">
                <h4>Admin Panel</h4>
            </div>

            <!-- User Info & Actions -->
            <div class="admin-user-info">
                <div class="dropdown">
                    <button class="dropdown-toggle" type="button" id="adminUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i data-feather="user" width="16" height="16"></i>
                        Welcome back, {{ auth()->user()->name ?? 'Admin' }}!
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i data-feather="home" width="16" height="16"></i>
                                Dashboard
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                <i data-feather="log-out" width="16" height="16"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden logout form -->
    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</header>

