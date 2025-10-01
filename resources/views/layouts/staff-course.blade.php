<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff | Olympia Education')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/store/1/favicon.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            background: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        
        /* Staff Navbar Styling */
        .staff-navbar {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 0.5rem 0 !important;
            margin-top: 0 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1000 !important;
        }
        
        .navbar-brand {
            display: flex !important;
            align-items: center !important;
            text-decoration: none !important;
            color: #212529 !important;
        }
        
        .navbar-brand img {
            margin-right: 1rem !important;
        }
        
        .brand-text {
            display: flex !important;
            flex-direction: column !important;
        }
        
        .brand-text strong {
            font-size: 1.2rem !important;
            color: #0056d2 !important;
            margin: 0 !important;
        }
        
        .brand-text small {
            font-size: 0.8rem !important;
            color: #6c757d !important;
            margin: 0 !important;
        }
        
        .navbar-nav-right {
            display: flex !important;
            align-items: center !important;
        }
        
        .staff-profile-btn {
            display: flex !important;
            align-items: center !important;
            padding: 0.5rem 1rem !important;
            border: 1px solid #0056d2 !important;
            background: white !important;
            color: #0056d2 !important;
            border-radius: 6px !important;
            text-decoration: none !important;
        }
        
        .staff-profile-btn:hover {
            background: #0056d2 !important;
            color: white !important;
        }
        
        .profile-info {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        
        .profile-pic-nav, .profile-pic-placeholder-nav {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
        }
        
        .profile-pic-placeholder-nav {
            background: #0056d2 !important;
            color: white !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.8rem !important;
        }
        
        .staff-name {
            font-weight: 500 !important;
            font-size: 0.9rem !important;
        }
        
        .dropdown-menu {
            border: 1px solid #dee2e6 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            border-radius: 8px !important;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        
        .dropdown-item i {
            width: 16px !important;
            text-align: center !important;
        }
        
        /* Main Layout */
        .main-content { 
            margin-left: 200px !important;
            padding: 2rem !important;
            min-height: 100vh !important;
            margin-top: 80px !important;
        }
        
        /* Course Sidebar */
        .sidebar { 
            background: #212529 !important;
            min-height: 100vh !important;
            padding: 0 !important;
            position: fixed !important;
            top: 80px !important;
            left: 0 !important;
            width: 200px !important;
            z-index: 999 !important;
        }
        
        .sidebar-header {
            background: #212529 !important;
            padding: 1.5rem 1rem !important;
            color: white !important;
            border-bottom: 1px solid #495057 !important;
        }
        
        .profile-section {
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
        }
        
        .profile-picture-container {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
        }
        
        .profile-picture {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        
        .profile-picture-placeholder {
            width: 100% !important;
            height: 100% !important;
            background: #495057 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.5rem !important;
        }
        
        .profile-name {
            color: white !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            margin: 0 !important;
        }
        
        .sidebar-nav {
            padding: 0.5rem 0 !important;
        }
        
        .sidebar .nav-link {
            display: flex !important;
            align-items: center !important;
            padding: 0.875rem 1rem !important;
            color: white !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            margin: 0.125rem 0.5rem !important;
            border-radius: 6px !important;
            font-weight: 500 !important;
            font-size: 0.9rem !important;
        }
        
        .sidebar .nav-link:hover {
            background: #495057 !important;
            color: white !important;
            text-decoration: none !important;
        }
        
        .sidebar .nav-link:focus {
            background: #495057 !important;
            color: white !important;
            text-decoration: none !important;
            outline: none !important;
            box-shadow: none !important;
        }
        
        .sidebar .nav-link.active,
        .sidebar .nav-link.active:focus,
        .sidebar .nav-link.active:hover {
            background: #6610f2 !important;
            color: white !important;
            border: none !important;
            box-shadow: none !important;
            text-decoration: none !important;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.75rem !important;
            width: 20px !important;
            text-align: center !important;
            font-size: 1rem !important;
        }
        
        /* Course Selection */
        .course-selection {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .course-selection h5 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .course-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        
        .course-info h4 {
            margin: 0 0 0.5rem 0;
            font-weight: 600;
        }
        
        .course-info p {
            margin: 0;
            opacity: 0.9;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .action-btn.primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .action-btn.success {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }
        
        .action-btn.warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        .action-btn.info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }
        
        .action-btn.danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .action-btn.secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .content-card h5 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #bdc3c7;
        }
        
        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Staff Navigation Bar -->
    @include('staff.partials.staff-navbar')
    
    <!-- Course Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="profile-section">
                <div class="profile-picture-container">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="profile-picture">
                    @else
                        <div class="profile-picture-placeholder">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    @endif
                </div>
                <div class="profile-info">
                    <h5 class="profile-name">{{ auth()->user()->name }}</h5>
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('staff.dashboard') }}" class="nav-link">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            <a href="{{ route('staff.courses') }}" class="nav-link">
                <i class="fas fa-book"></i>
                My Courses
            </a>
            <a href="{{ route('staff.announcements') }}" class="nav-link">
                <i class="fas fa-bullhorn"></i>
                Announcements
            </a>
            <a href="{{ route('staff.contents') }}" class="nav-link">
                <i class="fas fa-file-text"></i>
                Course Materials
            </a>
            <a href="{{ route('staff.assignments') }}" class="nav-link">
                <i class="fas fa-clipboard"></i>
                Assignments
            </a>
            <a href="{{ route('staff.profile') }}" class="nav-link">
                <i class="fas fa-user"></i>
                My Profile
            </a>
            <a href="{{ route('staff.password.change') }}" class="nav-link">
                <i class="fas fa-key"></i>
                Change Password
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Simple Footer -->
    @include('partials.simple-footer')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>
