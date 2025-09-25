<!-- Admin Header -->
<header class="admin-header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <div class="admin-logo">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center">
                    <img src="/store/1/dark-logo.png" class="img-cover" alt="Olympia Education Logo" style="height: 50px;">
                </a>
            </div>

            <!-- Admin Title -->
            <div class="admin-title">
                <h4 class="mb-0 text-dark">Admin Panel</h4>
            </div>

            <!-- User Info & Actions -->
            <div class="admin-user-info d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="adminUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i data-feather="user" width="16" height="16" class="me-2"></i>
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i data-feather="home" width="16" height="16" class="me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                <i data-feather="log-out" width="16" height="16" class="me-2"></i>
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

<style>
.admin-header {
    background: #fff;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.admin-logo img {
    max-height: 50px;
    width: auto;
}

.admin-title h4 {
    color: #495057;
    font-weight: 600;
}

.admin-user-info .dropdown-toggle {
    border: 1px solid #dee2e6;
    color: #495057;
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

.admin-user-info .dropdown-toggle:hover {
    background-color: #f8f9fa;
    border-color: #adb5bd;
}

.admin-user-info .dropdown-menu {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}

.admin-user-info .dropdown-item {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

.admin-user-info .dropdown-item:hover {
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .admin-header .container-fluid {
        padding: 0 1rem;
    }
    
    .admin-title {
        display: none;
    }
    
    .admin-logo img {
        height: 40px;
    }
}
</style>
