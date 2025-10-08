<!-- Finance Admin Sidebar -->
<div class="finance-admin-sidebar">
    <div class="sidebar-header">
        <div class="profile-section">
            <div class="profile-picture-container">
                @if(auth()->user() && auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="profile-picture">
                @else
                    <div class="profile-picture-placeholder">
                        <i class="bi bi-person-badge"></i>
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h5 class="profile-name">{{ auth()->user()->name ?? 'User' }}</h5>
                <span class="profile-role">Finance Admin</span>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a href="{{ route('finance-admin.dashboard') }}" class="nav-link {{ request()->routeIs('finance-admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>
        
        <a href="{{ route('finance-admin.students') }}" class="nav-link {{ request()->routeIs('finance-admin.students') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            All Students
        </a>
        
        <a href="{{ route('finance-admin.students', ['status' => 'active']) }}" class="nav-link {{ request()->routeIs('finance-admin.students') && request('status') == 'active' ? 'active' : '' }}">
            <i class="bi bi-person-check"></i>
            Active Students
        </a>
        
        <a href="{{ route('finance-admin.students', ['status' => 'blocked']) }}" class="nav-link {{ request()->routeIs('finance-admin.students') && request('status') == 'blocked' ? 'active' : '' }}">
            <i class="bi bi-person-x"></i>
            Blocked Students
        </a>
        
        <a href="{{ route('finance-admin.pending-payments') }}" class="nav-link {{ request()->routeIs('finance-admin.pending-payments') ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle"></i>
            Pending Payments
        </a>
        
        <a href="{{ route('finance-admin.invoices') }}" class="nav-link {{ request()->routeIs('finance-admin.invoices') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            Invoices
        </a>
        
        <a href="{{ route('finance-admin.reports') }}" class="nav-link {{ request()->routeIs('finance-admin.reports') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            Reports
        </a>
        
        <hr class="sidebar-divider">
        
        <a href="#" class="nav-link">
            <i class="bi bi-person"></i>
            My Profile
        </a>
        
        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </nav>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<style>
.sidebar {
    background: #2c3e50;
    min-height: 100vh;
    width: 250px;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    overflow-y: auto;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid #34495e;
}

.profile-section {
    text-align: center;
}

.profile-picture-container {
    margin-bottom: 1rem;
}

.profile-picture {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #3498db;
}

.profile-picture-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #34495e;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: #bdc3c7;
    font-size: 2rem;
}

.profile-name {
    color: white;
    margin: 0 0 0.25rem 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.profile-role {
    color: #bdc3c7;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #bdc3c7;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: #34495e;
    color: white;
    text-decoration: none;
}

.nav-link.active {
    background: #3498db;
    color: white;
    border-left-color: #2980b9;
}

.nav-link i {
    margin-right: 0.75rem;
    width: 20px;
    text-align: center;
}

.sidebar-divider {
    border: none;
    border-top: 1px solid #34495e;
    margin: 1rem 0;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}
</style>
