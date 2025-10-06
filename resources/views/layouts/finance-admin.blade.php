<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Olympia Education LMS Finance Admin Panel - Manage student accounts and payment status">
    <meta name="keywords" content="LMS, Learning Management System, Finance Admin, Payment Management, Olympia Education">
    <meta name="author" content="Olympia Education">
    
    <!-- Open Graph Meta Tags -->
    <meta property='og:title' content='@yield('title', 'Finance Admin Panel') | Olympia Education'>
    <meta property='og:description' content='Olympia Education LMS Finance Admin Panel - Manage student accounts and payment status'>
    <meta property='og:url' content='{{ url()->current() }}'>
    <meta property='og:type' content='website'>
    <meta property='og:site_name' content='Olympia Education LMS'>
    <meta property='og:image' content='{{ url('/store/1/favicon.png') }}'>
    <meta name='twitter:image' content='{{ url('/store/1/favicon.png') }}'>
    <meta property='og:locale' content='en_US'>
    <meta property='og:type' content='website'>

    <title>@yield('title', 'Finance Admin Panel') | Olympia Education</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- General CSS Files -->
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet" href="/assets/default/css/app.css">
    
    <!-- Custom Optima Font Override -->
    <style>
        /* Override any existing font definitions with Optima */
        * {
            font-family: 'Optima', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }
        
        /* Finance Admin specific styles */
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .finance-admin-app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .finance-admin-main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .finance-admin-dashboard {
            flex: 1;
        }

        /* Finance Admin Navbar Styling */
        .finance-admin-navbar {
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
        
        .finance-admin-profile-btn {
            display: flex !important;
            align-items: center !important;
            padding: 0.5rem 1rem !important;
            border: 1px solid #0056d2 !important;
            background: white !important;
            color: #0056d2 !important;
            border-radius: 6px !important;
            text-decoration: none !important;
        }
        
        .finance-admin-profile-btn:hover {
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
            background: #e9ecef !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #6c757d !important;
            font-size: 14px !important;
        }
        
        .staff-name {
            font-weight: 500 !important;
            color: #212529 !important;
        }

        /* Finance Admin Sidebar */
        .finance-admin-sidebar {
            position: fixed !important;
            top: 80px !important;
            left: 0 !important;
            width: 250px !important;
            height: calc(100vh - 80px) !important;
            background: #212529 !important;
            overflow-y: auto !important;
            z-index: 999 !important;
            transition: transform 0.3s ease !important;
        }
        
        .finance-admin-sidebar.collapsed {
            transform: translateX(-100%) !important;
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
            position: relative !important;
        }
        
        .profile-picture {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 2px solid #495057 !important;
        }
        
        .profile-picture-placeholder {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            background: #495057 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 20px !important;
            border: 2px solid #495057 !important;
        }
        
        .profile-name {
            color: white !important;
            margin: 0 !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
        }
        
        .profile-role {
            color: #adb5bd !important;
            font-size: 0.8rem !important;
            margin: 0 !important;
        }
        
        .sidebar-nav {
            padding: 1rem 0 !important;
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
        
        .sidebar-divider {
            border-color: #495057 !important;
            margin: 1rem 0 !important;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 250px !important;
            margin-top: 80px !important;
            padding: 2rem !important;
            min-height: calc(100vh - 80px) !important;
            background: #f8f9fa !important;
        }
        
        .page-header {
            margin-bottom: 2rem !important;
        }
        
        .page-title {
            color: #333 !important;
            font-weight: bold !important;
            margin-bottom: 0.5rem !important;
        }
        
        .page-subtitle {
            color: #666 !important;
            margin: 0 !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .finance-admin-sidebar {
                transform: translateX(-100%) !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .brand-text {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div id="app" class="finance-admin-app">
        <!-- Finance Admin Header -->
        @include('finance-admin.partials.header')
        
        <!-- Finance Admin Sidebar -->
        @include('finance-admin.partials.sidebar')
        
        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
        
        <!-- Finance Admin Footer -->
        @include('finance-admin.partials.footer')
    </div>
    
    <!-- Template JS Files -->
    <script src="/assets/default/js/app.js"></script>
    <script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>
    <script type="text/javascript" src="/assets/default/vendors/simplebar/simplebar.min.js"></script>

    <!-- Finance Admin Scripts -->
    <script>
        // Safe Feather icons replacement function
        function safeFeatherReplace() {
            try {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            } catch (error) {
                console.warn('Feather icons replacement failed:', error);
            }
        }

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            safeFeatherReplace();
        });

        // Re-initialize after AJAX calls
        document.addEventListener('ajaxComplete', function() {
            safeFeatherReplace();
        });
    </script>

    @stack('scripts')
</body>
</html>

