<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Course')</title>
    
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
        
        /* Student Navbar Styling */
        .student-navbar {
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
        
        .courses-dropdown-btn, .student-profile-btn {
            display: flex !important;
            align-items: center !important;
            padding: 0.5rem 1rem !important;
            border: 1px solid #0056d2 !important;
            background: white !important;
            color: #0056d2 !important;
            border-radius: 6px !important;
            text-decoration: none !important;
        }
        
        .courses-dropdown-btn:hover, .student-profile-btn:hover {
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
        
        .student-name {
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
        
        .courses-dropdown-menu {
            min-width: 250px !important;
            padding: 0.5rem 0 !important;
            margin-top: 0.5rem !important;
        }

        .courses-dropdown-menu .dropdown-header {
            background: #20c997 !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 0.75rem 1rem !important;
            margin: 0 !important;
            border-radius: 12px 12px 0 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .course-item {
            display: flex !important;
            align-items: center !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.2s ease !important;
            border: none !important;
            background: none !important;
        }

        .course-item:hover {
            background: #f8f9fa !important;
            transform: translateX(4px) !important;
        }

        .course-icon {
            width: 32px !important;
            height: 32px !important;
            background: #20c997 !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-right: 0.75rem !important;
            flex-shrink: 0 !important;
        }

        .course-icon i {
            color: white !important;
            font-size: 14px !important;
        }

        .course-code {
            font-weight: 600 !important;
            color: #333 !important;
            font-size: 14px !important;
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
        
        /* Dashboard Header */
        .dashboard-header {
            margin-bottom: 2rem;
        }
        
        .dashboard-header h1 {
            color: #333;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 0 0.5rem 0;
        }
        
        .dashboard-header p {
            color: #6c757d;
            font-size: 1.1rem;
            margin: 0;
        }
        
        /* Course Card Styling */
        .course-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
        }
        
        .course-card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
        }
        
        .course-card .card-header h5 {
            margin: 0;
            color: #333;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .course-card .card-body {
            padding: 2rem;
        }
        
        .course-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 0 0 15px 15px;
        }
        
        .course-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .course-card-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 20px;
        }
        
        .course-card-body {
            padding: 25px;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: white;
        }
        
        .stats-icon.resource-person { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .stats-icon.instructors { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .stats-icon.students { background: linear-gradient(135deg, #27ae60, #229954); }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .nav-tabs {
            border-bottom: 2px solid #ecf0f1;
            margin-bottom: 25px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #7f8c8d;
            font-weight: 600;
            padding: 15px 25px;
        }
        
        .nav-tabs .nav-link:hover {
            border: none;
            color: #3498db;
        }
        
        .nav-tabs .nav-link.active {
            color: #3498db;
            background: none;
            border: none;
            border-bottom: 3px solid #3498db;
        }
        
        .content-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .announcement-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .announcement-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .announcement-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid #e74c3c;
        }
        
        .content-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .content-table table { margin: 0; }
        .content-table th { background: #f8f9fa; color: #2c3e50; font-weight: 600; border: none; padding: 20px; }
        .content-table td { border: none; padding: 20px; vertical-align: middle; border-bottom: 1px solid #ecf0f1; }
        .content-table tbody tr:hover { background: #f8f9fa; }
        .content-table tbody tr.highlighted { background: #fff3cd; }
        
        .search-bar {
            background: white;
            border-radius: 25px;
            padding: 10px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 2px solid transparent;
            margin-bottom: 20px;
        }
        
        .search-bar:focus {
            border-color: #3498db;
            box-shadow: 0 4px 20px rgba(52, 152, 219, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bdc3c7;
        }
        
        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Student Navigation Bar -->
    @include('student.partials.student-navbar')
    
    <!-- Course Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="profile-section">
                <div class="profile-picture-container">
                    <div class="profile-picture-placeholder">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div class="profile-info">
                    <h5 class="profile-name">{{ $courseInfo['name'] ?? strtoupper($courseId ?? 'COURSE') }}</h5>
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('course.summary', $courseId ?? 'test') }}" class="nav-link {{ request()->routeIs('course.summary') ? 'active' : '' }}">
                <i class="fas fa-tv"></i>
                Course Summary
            </a>
            <a href="{{ route('course.announcements', $courseId ?? 'test') }}" class="nav-link {{ request()->routeIs('course.announcements') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i>
                Announcement
            </a>
            <a href="{{ route('course.contents', $courseId ?? 'test') }}" class="nav-link {{ request()->routeIs('course.contents') ? 'active' : '' }}">
                <i class="fas fa-edit"></i>
                Course Content
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>
