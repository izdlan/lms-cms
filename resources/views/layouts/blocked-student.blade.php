<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Bills | Olympia Education')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .blocked-student-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
        }
        
        .logo-section img {
            width: 40px;
            height: 40px;
            margin-right: 15px;
            border-radius: 50%;
        }
        
        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        
        .logo-text p {
            font-size: 0.8rem;
            margin: 0;
            opacity: 0.9;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .blocked-notice {
            background: #dc3545;
            color: white;
            padding: 0.75rem 1rem;
            text-align: center;
            font-weight: 500;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }
        
        .bills-only-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .bills-only-notice i {
            margin-right: 0.5rem;
        }
        
        .btn-logout {
            background: #dc3545;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }
        
        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
        
        .footer {
            background: #343a40;
            color: white;
            padding: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="blocked-student-container">
        <!-- Header -->
        <div class="header">
            <div class="container">
                <div class="logo-section">
                    <img src="{{ asset('assets/logo.png') }}" alt="Olympia Education Logo">
                    <div class="logo-text">
                        <h1>OLYMPIA EDUCATION</h1>
                        <p>Centre for Professional, Executive, Advanced & Continuing Education</p>
                    </div>
                </div>
                <div class="user-section">
                    <div class="user-info">
                        <div class="user-name">{{ Auth::guard('student')->user()->name }}</div>
                        <div class="user-role">Student (Blocked)</div>
                    </div>
                    <a href="{{ route('student.logout') }}" class="btn-logout">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Blocked Notice -->
        <div class="blocked-notice">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Your account has been blocked. You can only access the Student Bills page to make payments.
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="container">
                <!-- Bills Only Notice -->
                <div class="bills-only-notice">
                    <i class="fas fa-info-circle"></i>
                    <strong>Limited Access:</strong> You can only view and pay your bills. All other features are restricted.
                </div>
                
                @yield('content')
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="container">
                <p>&copy; {{ date('Y') }} Olympia Education. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
