<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta name='robots' content="index, follow, all">

    <meta name="description" content="Home Page Description">
    <meta property="og:description" content="Home Page Description">
    <meta name='twitter:description' content='Home Page Description'>

    <link rel='shortcut icon' type='image/x-icon' href="https://lms.olympia-education.com/store/1/favicon.png">
    <link rel="manifest" href="/mix-manifest.json?v=4">
    <meta name="theme-color" content="#FFF">
    <!-- Windows Phone -->
    <meta name="msapplication-starturl" content="/">
    <meta name="msapplication-TileColor" content="#FFF">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-title" content="Olympia Education">
    <link rel="apple-touch-icon" href="https://lms.olympia-education.com/store/1/favicon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <!-- Android -->
    <link rel='icon' href='https://lms.olympia-education.com/store/1/favicon.png'>
    <meta name="application-name" content="Olympia Education">
    <meta name="mobile-web-app-capable" content="yes">
    <!-- Other -->
    <meta name="layoutmode" content="fitscreen/standard">
    <link rel="home" href="https://lms.olympia-education.com">

    <!-- Open Graph -->
    <meta property='og:title' content='Home'>
    <meta name='twitter:card' content='summary'>
    <meta name='twitter:title' content='Home'>

    <meta property='og:site_name' content='https://lms.olympia-education.com/Olympia Education'>
    <meta property='og:image' content='https://lms.olympia-education.com/store/1/favicon.png'>
    <meta name='twitter:image' content='https://lms.olympia-education.com/store/1/favicon.png'>
    <meta property='og:locale' content='en_US'>
    <meta property='og:type' content='website'>

    <title>@yield('title', 'Home') | Olympia Education</title>

    <!-- General CSS File -->
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet" href="/assets/default/css/app.css">
    <link rel="stylesheet" href="{{ asset('css/student-sidebar.css') }}?v=1.3">
    <link rel="stylesheet" href="{{ asset('css/home-spacing-fix.css') }}?v=1.0">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- FontAwesome Icon Fix -->
    <style>
        /* Ensure FontAwesome icons display properly */
        .fa, .fas, .far, .fal, .fab, .fa-* {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
        }
        
        .fa:before, .fas:before, .far:before, .fal:before, .fab:before {
            content: attr(data-icon) !important;
        }
        
        /* Force icon display */
        i[class*="fa-"]:before {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
        }
    </style>
    
    <!-- Student Sidebar Override Styles -->
    <style>
        .student-dashboard .sidebar {
            background: #212529 !important;
            min-height: 100vh !important;
            padding: 0 !important;
            position: fixed !important;
            top: 80px !important;
            left: 0 !important;
            width: 200px !important;
            z-index: 1000 !important;
            transition: transform 0.3s ease !important;
        }

        /* Mobile sidebar styles */
        @media (max-width: 991.98px) {
            .student-dashboard .sidebar {
                transform: translateX(-100%) !important;
                width: 280px !important;
                top: 55px !important;
            }
            
            .student-dashboard .sidebar.mobile-open {
                transform: translateX(0) !important;
            }
            
            /* Add overlay when sidebar is open on mobile */
            .sidebar-overlay {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                background: rgba(0, 0, 0, 0.5) !important;
                z-index: 999 !important;
                display: none !important;
            }
            
            .sidebar-overlay.show {
                display: block !important;
            }
        }
        
        .student-dashboard .sidebar-header {
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
            flex-shrink: 0 !important;
        }
        
        .profile-picture {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            border: 2px solid #20c997 !important;
            object-fit: cover !important;
        }
        
        .profile-picture-placeholder {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            border: 2px solid #20c997 !important;
            background: #495057 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
        }
        
        .profile-info {
            flex: 1 !important;
            min-width: 0 !important;
        }
        
        .profile-name {
            margin: 0 0 0.25rem 0 !important;
            font-weight: bold !important;
            font-size: 0.9rem !important;
            color: white !important;
            letter-spacing: 0.3px !important;
            line-height: 1.2 !important;
            word-wrap: break-word !important;
        }
        
        .profile-id {
            margin: 0 !important;
            color: #adb5bd !important;
            font-size: 0.8rem !important;
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
            background: transparent !important;
            border: none !important;
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
        
        /* Student Navbar Styling (from course page) */
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
        
        .main-content {
            margin-left: 200px !important;
            padding: 2rem !important;
            min-height: 100vh !important;
            width: calc(100% - 200px) !important;
            margin-top: 80px !important;
        }

        /* Mobile main content styles */
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 1rem !important;
                margin-top: 55px !important;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                margin-top: 50px !important;
                padding: 0.75rem !important;
            }
        }
        
        .student-dashboard .container-fluid {
            padding-left: 0 !important;
            width: 100% !important;
        }
        
        /* Make cards and content fill the available space */
        .card {
            width: 100% !important;
            max-width: none !important;
        }
        
        .password-change-card,
        .dashboard-card,
        .course-card,
        .stats-card {
            width: 100% !important;
            max-width: none !important;
        }
        
        /* Remove max-width constraints from forms */
        .login-card,
        .password-change-card {
            max-width: none !important;
            width: 100% !important;
        }
        
        /* Make form containers wider */
        .form-container,
        .card-body {
            width: 100% !important;
        }
        
        /* Ensure all student page content uses full width */
        .student-dashboard .row,
        .student-dashboard .container-fluid,
        .student-dashboard .main-content {
            width: 100% !important;
            max-width: none !important;
        }
        
        /* Make sure cards don't have artificial width limits */
        .password-change-card,
        .dashboard-card,
        .course-card,
        .stats-card,
        .card {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        /* Student Navigation Bar Styles */
        .student-navbar {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 0.5rem 0 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
        }

        /* Logo responsive styles */
        .navbar-logo {
            height: 40px !important;
            width: auto !important;
            object-fit: contain !important;
        }

        /* Mobile navbar adjustments */
        @media (max-width: 991.98px) {
            .student-navbar {
                padding: 0.25rem 0 !important;
                min-height: 50px !important;
            }
            
            .navbar-logo {
                height: 30px !important;
            }
            
            .navbar-brand {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
        }

        @media (max-width: 576px) {
            .student-navbar {
                padding: 0.2rem 0 !important;
                min-height: 45px !important;
            }
            
            .navbar-logo {
                height: 25px !important;
            }
        }
        
        .student-navbar .container-fluid {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 2rem !important;
        }

        /* Mobile navbar styles */
        @media (max-width: 991.98px) {
            .student-navbar .container-fluid {
                padding: 0 1rem !important;
            }
            
            .mobile-menu-toggle {
                background: none !important;
                border: none !important;
                color: #0056d2 !important;
                font-size: 1.5rem !important;
                padding: 0.5rem !important;
                margin-right: 1rem !important;
                cursor: pointer !important;
            }
            
            .mobile-menu-toggle:hover {
                color: #0041a3 !important;
            }
            
            .navbar-nav-right {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
        }

        /* Navbar Layout */
        .navbar-nav-left {
            flex: 1;
            display: flex !important;
            align-items: center !important;
        }

        .navbar-nav-right {
            flex: 0 0 auto;
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
        }

        /* Courses Dropdown Right Positioning */
        .courses-dropdown-right {
            margin-right: 1rem !important;
        }

        /* Courses Dropdown Styles */
        .courses-dropdown-btn {
            background: #20c997 !important;
            border: 1px solid #20c997 !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .courses-dropdown-btn:hover,
        .courses-dropdown-btn:focus {
            background: #1ba085 !important;
            border-color: #1ba085 !important;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(32, 201, 151, 0.3) !important;
        }

        .courses-dropdown-menu {
            min-width: 280px !important;
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
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
        
        .navbar-nav-left {
            display: flex !important;
            align-items: center !important;
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
        
        .navbar-nav-center {
            flex: 1 !important;
            display: flex !important;
            justify-content: center !important;
        }
        
        .navbar-nav-center .navbar-nav {
            display: flex !important;
            gap: 2rem !important;
            margin: 0 !important;
        }
        
        .navbar-nav-center .nav-link {
            color: #495057 !important;
            font-weight: 500 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 6px !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
        }
        
        .navbar-nav-center .nav-link:hover {
            background: #e9ecef !important;
            color: #0056d2 !important;
        }
        
        .navbar-nav-center .nav-link.active {
            background: #0056d2 !important;
            color: white !important;
        }
        
        .navbar-nav-center .nav-link i {
            margin-right: 0.5rem !important;
        }
        
        .navbar-nav-right {
            display: flex !important;
            align-items: center !important;
        }
        
        .student-profile-btn {
            display: flex !important;
            align-items: center !important;
            padding: 0.5rem 1rem !important;
            border: 1px solid #0056d2 !important;
            background: white !important;
            color: #0056d2 !important;
            border-radius: 6px !important;
            text-decoration: none !important;
        }
        
        .student-profile-btn:hover {
            background: #0056d2 !important;
            color: white !important;
        }
        
        .profile-info {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        
        .profile-pic-nav {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 2px solid #0056d2 !important;
        }
        
        .profile-pic-placeholder-nav {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
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
        
        /* Adjust main content for fixed navbar */
        .student-dashboard .main-content,
        .student-dashboard .container-fluid,
        .main-content {
            margin-top: 1px !important;
        }
        
        /* Ensure all student pages have proper top spacing */
        .student-dashboard {
            padding-top: 60px !important;
        }
        
        /* Fix specific page headers */
        .courses-header,
        .dashboard-header,
        .password-change-header,
        .assignments-header {
            margin-top: 0 !important;
            padding-top: 1rem !important;
        }
        
        /* TOP LINK Section Styles */
        .top-links-section {
            background: #f8f9fa;
            padding: 0;
            min-height: 400px;
        }
        
        /* Sidebar Styles */
        .top-links-sidebar {
            background: white;
            padding: 0;
            border-right: 1px solid #dee2e6;
            min-height: 400px;
        }
        
        .sidebar-content {
            padding: 2rem 1.5rem;
            height: 100%;
        }
        
        .sidebar-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0056d2;
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .top-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .top-links-list li {
            margin-bottom: 0.5rem;
        }
        
        .top-link-item {
            display: block;
            padding: 1rem;
            color: #6c757d;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 4px solid #6c757d;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .top-link-item:hover {
            color: #0056d2;
            border-left-color: #0056d2;
            text-decoration: none;
            transform: translateX(3px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            background: white;
        }
        
        .top-link-item:before {
            content: "■";
            color: #6c757d;
            margin-right: 0.75rem;
            font-size: 0.8rem;
        }
        
        .top-link-item:hover:before {
            color: #0056d2;
        }
        
        /* Single Gallery Styles */
        .gallery-container {
            background: #f8f9fa;
            padding: 20px;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .single-gallery {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        .gallery-main {
            position: relative;
            width: 100%;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .gallery-image-container {
            position: relative !important;
            width: 100% !important;
            height: 100% !important;
            max-width: 900px !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2) !important;
            background: #f8f9fa !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .main-gallery-image {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            opacity: 0 !important;
            transition: opacity 0.5s ease-in-out !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: none !important;
            max-height: none !important;
            min-width: 100% !important;
            min-height: 100% !important;
        }
        
        /* Override Bootstrap img classes */
        .gallery-image-container img,
        .gallery-image-container .main-gallery-image,
        .main-gallery-image[class*="img-"],
        .main-gallery-image.img-fluid {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            max-width: none !important;
            max-height: none !important;
            min-width: 100% !important;
            min-height: 100% !important;
        }
        
        /* Override Bootstrap container and row constraints */
        .gallery-container .container,
        .gallery-container .container-fluid,
        .gallery-container .row,
        .gallery-container .col,
        .gallery-container .col-md-10,
        .gallery-container .col-lg-10 {
            max-width: none !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Ensure gallery takes full available space */
        .top-links-section .row {
            height: auto !important;
            min-height: 600px !important;
        }
        
        .gallery-container {
            flex: 1 !important;
            width: 100% !important;
        }
        
        .main-gallery-image.active {
            opacity: 1 !important;
        }
        
        /* Navigation Arrows */
        .gallery-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            color: #0056d2;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .gallery-nav:hover {
            background: #0056d2;
            color: white;
            transform: translateY(-50%) scale(1.1);
        }
        
        .gallery-prev {
            left: 20px;
        }
        
        .gallery-next {
            right: 20px;
        }
        
        /* Dots Indicator */
        .gallery-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .dot.active {
            background: #0056d2;
            transform: scale(1.2);
        }
        
        .dot:hover {
            background: rgba(255, 255, 255, 0.8);
        }
        
        .dot.active:hover {
            background: #0056d2;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .top-links-sidebar {
                border-right: none;
                border-bottom: 1px solid #dee2e6;
                min-height: auto;
            }
            
            .gallery-container {
                min-height: 400px;
                padding: 10px;
            }
            
            .gallery-main {
                height: 350px;
            }
            
            .gallery-image-container {
                max-width: 100% !important;
            }
            
            .gallery-nav {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
            
            .gallery-prev {
                left: 10px;
            }
            
            .gallery-next {
                right: 10px;
            }
        }
        
        @media (max-width: 576px) {
            .gallery-container {
                min-height: 300px;
                padding: 5px;
            }
            
            .gallery-main {
                height: 250px;
            }
        }
        
    </style>
    
    <!-- Custom Optima Font Override -->
    <style>
        /* Override any existing font definitions with Optima */
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center, dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend, table, caption,
        tbody, tfoot, thead, tr, th, td, article, aside,
        canvas, details, embed, figure, figcaption, footer,
        header, hgroup, menu, nav, output, ruby, section,
        summary, time, mark, audio, video, input, textarea, select, button {
            font-family: 'main-font-family', 'Optima', 'Optima-Regular', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif !important;
        }
    </style>

    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/owl-carousel2/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">

    <style>
        /* System Font Stack - No external font loading */
        @font-face {
            font-family: 'main-font-family';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: local('Optima'), local('Optima-Regular'), 
                 local('-apple-system'), local('BlinkMacSystemFont'),
                 local('Segoe UI'), local('Helvetica Neue'), local('Arial');
        }

        @font-face {
            font-family: 'main-font-family';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: local('Optima Medium'), local('Optima-Medium'),
                 local('-apple-system'), local('BlinkMacSystemFont'),
                 local('Segoe UI'), local('Helvetica Neue'), local('Arial');
        }

        @font-face {
            font-family: 'main-font-family';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: local('Optima Bold'), local('Optima-Bold'),
                 local('-apple-system'), local('BlinkMacSystemFont'),
                 local('Segoe UI'), local('Helvetica Neue'), local('Arial');
        }

        @font-face {
            font-family: 'rtl-font-family';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: local('Optima'), local('Optima-Regular'), 
                 local('-apple-system'), local('BlinkMacSystemFont'),
                 local('Segoe UI'), local('Helvetica Neue'), local('Arial');
        }

        @font-face {
            font-family: 'rtl-font-family';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: local('Optima Medium'), local('Optima-Medium'),
                 local('-apple-system'), local('BlinkMacSystemFont'),
                 local('Segoe UI'), local('Helvetica Neue'), local('Arial');
        }

        @font-face {
            font-family: 'rtl-font-family';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: local('Optima Bold'), local('Optima-Bold'),
                 local('-apple-system'), local('BlinkMacSystemFont'),
                 local('Segoe UI'), local('Helvetica Neue'), local('Arial');
        }

        :root {
            --primary: #0056d2;
            --primary-border: #084aa0;
            --primary-hover: #0069ff;
            --primary-border-hover: #084aa0;
            --primary-btn-shadow: 0 3px 6px rgba(0, 86, 210, 0.3);
            --primary-btn-shadow-hover: 0 3px 6px rgba(0, 86, 210, 0.4);
            --primary-btn-color: #ffffff;
            --primary-btn-color-hover: #ffffff;
        }

        /* Font fallback to prevent loading errors */
        body {
            font-family: 'main-font-family', 'Optima', 'Optima-Regular', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        }

        /* Apply Optima font to all elements */
        * {
            font-family: 'main-font-family', 'Optima', 'Optima-Regular', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif !important;
        }

        /* Hide font loading errors */
        @font-face {
            font-family: 'error-font';
            src: url('data:font/woff2;base64,') format('woff2');
            font-display: block;
        }
    </style>

    <link rel="stylesheet" href="/assets/vendors/nprogress/nprogress.min.css">
    <script src="/assets/vendors/nprogress/nprogress.min.js"></script>
    <script>
        NProgress.configure({
            showSpinner: true, // Hide spinner
            easing: 'ease', // Animation style
            speed: 500 // Animation speed
        });

        document.addEventListener("DOMContentLoaded", function() {
            NProgress.start(); // Start progress bar
        });

        window.addEventListener('load', () => {
            NProgress.done();
        });

        // Suppress font loading errors
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('Failed to decode downloaded font')) {
                e.preventDefault();
                console.warn('Font loading issue suppressed:', e.message);
                return false;
            }
        });

        // Suppress OTS parsing errors
        const originalConsoleWarn = console.warn;
        console.warn = function(...args) {
            if (args[0] && args[0].includes('OTS parsing error')) {
                return; // Suppress OTS parsing errors
            }
            originalConsoleWarn.apply(console, args);
        };
    </script>
    <style>
        /* Blocked student blur/lock styles */
        .blocked-overlay {
            position: fixed !important;
            inset: 0 !important;
            background: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: blur(3px) !important;
            -webkit-backdrop-filter: blur(3px) !important;
            z-index: 2000 !important;
            pointer-events: none !important;
        }
        .blocked-toast {
            position: fixed !important;
            top: 70px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 2050 !important;
            background: #fff3cd !important;
            color: #856404 !important;
            border: 1px solid #ffe69c !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12) !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        .blocked-disabled {
            pointer-events: none !important;
            filter: grayscale(0.2) opacity(0.6);
        }
        .blurred-content {
            filter: blur(5px);
            pointer-events: none;
            user-select: none;
        }
        .overlay-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            z-index: 1050;
            pointer-events: none; /* Allow clicks to pass through to the overlay itself, not the blurred content */
        }
        .overlay-container {
            position: relative;
        }
        .clickable-link {
            pointer-events: auto !important; /* Re-enable clicks for specific elements */
            filter: none !important; /* Remove blur for specific elements */
        }
    </style>
</head>

<body class="">
    <div id="app" class=" {{ (isset($isBlockedStudent) && $isBlockedStudent && isset($blockedCurrentRoute) && isset($blockedAllowedRoutes) && !in_array($blockedCurrentRoute, $blockedAllowedRoutes)) ? 'overlay-container' : '' }}">
        @if((request()->is('student/*') && auth('student')->check()) || (request()->is('staff/*') && auth()->check() && auth()->user()->role === 'staff'))
            @if(request()->is('student/*'))
                @include('student.partials.student-navbar')
                @if(request()->is('student/dashboard'))
                    @include('student.partials.upm-logo-animation')
                @endif
            @else
                @include('staff.partials.staff-navbar')
            @endif
        @else
            @include('partials.navbar')
        @endif
        
        <div class="{{ (isset($isBlockedStudent) && $isBlockedStudent && isset($blockedCurrentRoute) && isset($blockedAllowedRoutes) && !in_array($blockedCurrentRoute, $blockedAllowedRoutes)) ? 'blurred-content' : '' }}">
            @yield('content')
        </div>

        @if(isset($isBlockedStudent) && $isBlockedStudent && isset($blockedCurrentRoute) && isset($blockedAllowedRoutes) && !in_array($blockedCurrentRoute, $blockedAllowedRoutes))
            <div class="overlay-message">
                <h4>Account Blocked</h4>
                <p>Your account has been blocked. You can only access the Student Bills page to make payments.</p>
                <a href="{{ route('student.bills') }}" class="btn btn-primary clickable-link mt-3">Go to Student Bills</a>
                <form action="{{ route('student.logout') }}" method="POST" class="d-inline clickable-link">
                    @csrf
                    <button type="submit" class="btn btn-outline-light clickable-link mt-3 ms-2">Logout</button>
                </form>
            </div>
        @endif
        
        @if(Auth::guard('student')->check() || Auth::guard('staff')->check())
            @include('partials.simple-footer')
        @else
            @include('partials.footer')
        @endif
    </div>
    
    <!-- Template JS File -->
    <script src="/assets/default/js/app.js"></script>
    <script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>
    <script type="text/javascript" src="/assets/default/vendors/simplebar/simplebar.min.js"></script>

    <script>
        var deleteAlertTitle = 'Are you sure?';
        var deleteAlertHint = 'This action cannot be undone!';
        var deleteAlertConfirm = 'Delete';
        var deleteAlertCancel = 'Cancel';
        var deleteAlertSuccess = 'Success';
        var deleteAlertFail = 'Failed';
        var deleteAlertFailHint = 'Error while deleting item!';
        var deleteAlertSuccessHint = 'Item successfully deleted.';
        var forbiddenRequestToastTitleLang = "FORBIDDEN Request";
        var forbiddenRequestToastMsgLang = 'You not access to this content.';
        var priceInvalidHintLang = 'update.price_invalid_hint';
    </script>

    <script src="/assets/default/vendors/lottie/lottie-player.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/swiper/swiper-bundle.min.js"></script>
    <script src="/assets/default/vendors/owl-carousel2/owl.carousel.min.js"></script>
    <script src="/assets/default/vendors/parallax/parallax.min.js"></script>
    <script>
        // Override Parallax constructor to add safety checks
        if (typeof Parallax !== 'undefined') {
            var OriginalParallax = Parallax;
            Parallax = function(element) {
                if (!element || !element.getAttribute) {
                    console.warn('Parallax: Invalid element provided');
                    return;
                }
                try {
                    return new OriginalParallax(element);
                } catch (e) {
                    console.warn('Parallax initialization failed:', e);
                    return null;
                }
            };
        }
        
        // Fix parallax error by adding comprehensive safety check
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Check if parallax elements exist before initializing
                var parallaxElements = document.querySelectorAll('[data-parallax]');
                if (parallaxElements.length > 0) {
                    // Initialize parallax only if elements exist and Parallax is available
                    if (typeof Parallax !== 'undefined') {
                        parallaxElements.forEach(function(element) {
                            if (element && element.getAttribute) {
                                try {
                                    new Parallax(element);
                                } catch (e) {
                                    console.warn('Parallax initialization failed for element:', e);
                                }
                            }
                        });
                    }
                }
            } catch (e) {
                console.warn('Parallax initialization failed:', e);
            }
        });
    </script>
    <script>
        // Add safety wrapper for all JavaScript execution
        window.addEventListener('error', function(e) {
            console.warn('JavaScript error caught:', e.message, e.filename, e.lineno);
        });
    </script>
    <!-- Disabled problematic scripts for student dashboard -->
    @if(!request()->is('student/*'))
        <script src="/assets/default/js/parts/home.min.js"></script>
        <script src="/assets/default/js/parts/categories.min.js"></script>
        <link href="/assets/default/vendors/flagstrap/css/flags.css" rel="stylesheet">
        <script src="/assets/default/vendors/flagstrap/js/jquery.flagstrap.min.js"></script>
        <script src="/assets/default/js/parts/top_nav_flags.min.js"></script>
    @endif
    <!-- Prevent Bootstrap navbar conflicts -->
    <script>
    // Override Bootstrap navbar before it loads
    (function() {
        'use strict';
        
        // Store original console.error to prevent navbar errors
        const originalError = console.error;
        console.error = function(...args) {
            // Filter out navbar offsetTop errors
            if (args[0] && typeof args[0] === 'string' && args[0].includes('offsetTop')) {
                return;
            }
            originalError.apply(console, args);
        };
        
        // Override Bootstrap navbar initialization
        if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Navbar) {
            const originalNavbar = window.bootstrap.Navbar;
            window.bootstrap.Navbar = function(element, config) {
                // Skip initialization for our custom navbar
                if (element && element.classList && element.classList.contains('student-navbar')) {
                    return;
                }
                // Allow normal Bootstrap behavior for other navbars
                return new originalNavbar(element, config);
            };
        }
        
        // Disable Bootstrap navbar auto-initialization for our navbar
        function disableBootstrapNavbar() {
            const navbar = document.querySelector('.student-navbar');
            if (navbar) {
                // Remove all Bootstrap classes that might trigger initialization
                navbar.classList.remove('navbar-expand-lg', 'navbar-light');
                
                // Set attributes to prevent Bootstrap initialization
                navbar.setAttribute('data-bs-auto-close', 'false');
                navbar.setAttribute('data-bs-toggle', 'false');
                navbar.setAttribute('data-bs-target', 'false');
                navbar.setAttribute('data-bs-collapse', 'false');
                
                // Remove any Bootstrap data attributes
                navbar.removeAttribute('data-bs-toggle');
                navbar.removeAttribute('data-bs-target');
                navbar.removeAttribute('data-bs-collapse');
            }
        }
        
        // Run immediately
        disableBootstrapNavbar();
        
        // Run when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                disableBootstrapNavbar();
            });
        } else {
            disableBootstrapNavbar();
        }
        
        // Prevent Bootstrap from initializing on our navbar
        if (typeof bootstrap !== 'undefined' && bootstrap.Navbar) {
            const navbarElements = document.querySelectorAll('.student-navbar');
            navbarElements.forEach(element => {
                if (element._bsNavbar) {
                    element._bsNavbar.dispose();
                    delete element._bsNavbar;
                }
            });
        }
    })();
    </script>
    <!-- Disabled navbar.min.js for student pages to prevent errors -->
    @if(!request()->is('student/*'))
        <script src="/assets/default/js/parts/navbar.min.js"></script>
    @endif
    <script src="/assets/default/js/parts/main.min.js"></script>

    <script>
        // Gallery Navigation JavaScript
        let currentImageIndex = 0;
        let totalImages = 0;
        
        function showImage(index) {
            // Hide all images
            const images = document.querySelectorAll('.main-gallery-image');
            const dots = document.querySelectorAll('.dot');
            
            images.forEach(img => img.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            // Show current image
            if (images[index]) {
                images[index].classList.add('active');
            }
            if (dots[index]) {
                dots[index].classList.add('active');
            }
        }
        
        function changeImage(direction) {
            // Update total images count dynamically
            totalImages = document.querySelectorAll('.main-gallery-image').length;
            
            if (totalImages === 0) return;
            
            currentImageIndex += direction;
            
            // Loop around
            if (currentImageIndex >= totalImages) {
                currentImageIndex = 0;
            } else if (currentImageIndex < 0) {
                currentImageIndex = totalImages - 1;
            }
            
            showImage(currentImageIndex);
        }
        
        function currentImage(index) {
            currentImageIndex = index;
            showImage(currentImageIndex);
        }
        
        // Function to redirect to announcement page
        function redirectToAnnouncement(announcementId) {
            window.location.href = '/announcements/' + announcementId;
        }
        
        // Auto-play functionality (optional)
        let autoPlayInterval;
        
        function startAutoPlay() {
            autoPlayInterval = setInterval(() => {
                changeImage(1);
            }, 4000); // Change image every 4 seconds
        }
        
        function stopAutoPlay() {
            clearInterval(autoPlayInterval);
        }
        
        // Initialize gallery
        document.addEventListener('DOMContentLoaded', function() {
            // Update total images count
            totalImages = document.querySelectorAll('.main-gallery-image').length;
            
            if (totalImages > 0) {
                showImage(0);
                startAutoPlay();
            }
            
            // Pause auto-play on hover
            const gallery = document.querySelector('.single-gallery');
            if (gallery) {
                gallery.addEventListener('mouseenter', stopAutoPlay);
                gallery.addEventListener('mouseleave', startAutoPlay);
            }
        });
    </script>

    @stack('styles')
    @stack('scripts')
</body>

</html>