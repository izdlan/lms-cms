<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Bootstrap Demo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Professional Header -->
    <header class="professional-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 font-weight-bold mb-3">Professional Bootstrap Design</h1>
                    <p class="lead mb-0">Clean, modern, and corporate-ready styling</p>
                </div>
                <div class="col-md-4 text-right">
                    <button class="btn professional-btn btn-light">
                        <i class="fas fa-download mr-2"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Professional Navigation -->
    <nav class="navbar navbar-expand-lg professional-navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap mr-2"></i>EduPlatform
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="section-professional">
        <div class="container">
            <!-- Professional Cards Section -->
            <div class="row-professional">
                <div class="col-lg-4 col-md-6">
                    <div class="card professional-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line text-primary mr-2"></i>
                                Analytics Dashboard
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-professional">
                                Track student performance with our comprehensive analytics dashboard. 
                                Get insights into learning patterns and engagement metrics.
                            </p>
                            <ul class="list-professional">
                                <li>Real-time data visualization</li>
                                <li>Custom reporting tools</li>
                                <li>Export capabilities</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <button class="btn professional-btn btn-primary btn-block">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card professional-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-users text-success mr-2"></i>
                                Student Management
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-professional">
                                Efficiently manage student records, enrollments, and academic progress 
                                with our streamlined management system.
                            </p>
                            <ul class="list-professional">
                                <li>Student profile management</li>
                                <li>Enrollment tracking</li>
                                <li>Academic records</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <button class="btn professional-btn btn-primary btn-block">
                                Get Started
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card professional-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap text-info mr-2"></i>
                                Course Management
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-professional">
                                Create and manage courses with our intuitive course management system. 
                                Upload materials, track progress, and engage students.
                            </p>
                            <ul class="list-professional">
                                <li>Course creation tools</li>
                                <li>Content management</li>
                                <li>Progress tracking</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <button class="btn professional-btn btn-primary btn-block">
                                Explore
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Table Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="heading-professional">Recent Activities</h2>
                    <div class="table-responsive">
                        <table class="table professional-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Last Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">John Doe</div>
                                                <small class="text-muted">ID: 12345</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Strategic Management</td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: 75%"></div>
                                        </div>
                                        <small class="text-muted">75% Complete</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-professional badge-success">Active</span>
                                    </td>
                                    <td>2 hours ago</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">Jane Smith</div>
                                                <small class="text-muted">ID: 12346</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Business Analytics</td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-info" style="width: 45%"></div>
                                        </div>
                                        <small class="text-muted">45% Complete</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-professional badge-info">In Progress</span>
                                    </td>
                                    <td>1 day ago</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Professional Alerts Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="heading-professional">System Notifications</h2>
                    <div class="alert alert-professional alert-success" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Success!</strong> Student enrollment completed successfully.
                    </div>
                    <div class="alert alert-professional alert-info" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Info:</strong> New course materials have been uploaded.
                    </div>
                    <div class="alert alert-professional alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> System maintenance scheduled for tonight.
                    </div>
                </div>
            </div>

            <!-- Professional Form Section -->
            <div class="row mt-5">
                <div class="col-lg-8">
                    <div class="card professional-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-plus text-primary mr-2"></i>
                                Add New Student
                            </h5>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstName">First Name</label>
                                            <input type="text" class="form-control form-control-professional" id="firstName" placeholder="Enter first name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastName">Last Name</label>
                                            <input type="text" class="form-control form-control-professional" id="lastName" placeholder="Enter last name">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control form-control-professional" id="email" placeholder="Enter email address">
                                </div>
                                <div class="form-group">
                                    <label for="course">Select Course</label>
                                    <select class="form-control form-control-professional" id="course">
                                        <option>Choose a course...</option>
                                        <option>Strategic Management</option>
                                        <option>Business Analytics</option>
                                        <option>Digital Marketing</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms">
                                        <label class="form-check-label" for="terms">
                                            I agree to the terms and conditions
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn professional-btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>Save Student
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card professional-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie text-success mr-2"></i>
                                Quick Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="display-4 font-weight-bold text-primary">1,247</div>
                                <div class="text-muted">Total Students</div>
                            </div>
                            <div class="text-center mb-4">
                                <div class="display-4 font-weight-bold text-success">89%</div>
                                <div class="text-muted">Completion Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="display-4 font-weight-bold text-info">24</div>
                                <div class="text-muted">Active Courses</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Professional Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="font-weight-bold mb-3">EduPlatform</h5>
                    <p class="text-light">Professional education management system designed for modern learning institutions.</p>
                </div>
                <div class="col-md-6 text-right">
                    <p class="mb-0">&copy; 2024 EduPlatform. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
