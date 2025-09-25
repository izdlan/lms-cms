<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Olympia Education LMS Admin Panel - Manage students, courses, and system administration">
    <meta name="keywords" content="LMS, Learning Management System, Admin Panel, Olympia Education">
    <meta name="author" content="Olympia Education">
    
    <!-- Open Graph Meta Tags -->
    <meta property='og:title' content='@yield('title', 'Admin Panel') | Olympia Education'>
    <meta property='og:description' content='Olympia Education LMS Admin Panel - Manage students, courses, and system administration'>
    <meta property='og:url' content='{{ url()->current() }}'>
    <meta property='og:type' content='website'>
    <meta property='og:site_name' content='Olympia Education LMS'>
    <meta property='og:image' content='{{ url('/store/1/favicon.png') }}'>
    <meta name='twitter:image' content='{{ url('/store/1/favicon.png') }}'>
    <meta property='og:locale' content='en_US'>
    <meta property='og:type' content='website'>

    <title>@yield('title', 'Admin Panel') | Olympia Education</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- General CSS Files -->
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet" href="/assets/default/css/app.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="/assets/default/css/admin.css?v={{ time() }}">
    
    <!-- Custom Optima Font Override -->
    <style>
        /* Override any existing font definitions with Optima */
        * {
            font-family: 'Optima', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }
        
        /* Admin specific styles */
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .admin-app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .admin-main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .admin-dashboard {
            flex: 1;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div id="app" class="admin-app">
        <!-- Admin Header -->
        @include('admin.partials.header')
        
        <!-- Main Content -->
        <main class="admin-main-content">
            @yield('content')
        </main>
        
        <!-- Admin Footer -->
        @include('admin.partials.footer')
    </div>
    
    <!-- Template JS Files -->
    <script src="/assets/default/js/app.js"></script>
    <script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>
    <script type="text/javascript" src="/assets/default/vendors/simplebar/simplebar.min.js"></script>

    <!-- Admin Scripts -->
    <script>
        // Initialize Feather icons
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });

        // Admin specific JavaScript
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

    @stack('scripts')
</body>
</html>
