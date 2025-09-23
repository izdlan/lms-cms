@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')
<div class="student-assignments">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4>Student Portal</h4>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('student.dashboard') }}" class="nav-link">
                        <i data-feather="home" width="20" height="20"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('student.courses') }}" class="nav-link">
                        <i data-feather="book-open" width="20" height="20"></i>
                        My Courses
                    </a>
                    <a href="{{ route('student.assignments') }}" class="nav-link active">
                        <i data-feather="file-text" width="20" height="20"></i>
                        Assignments
                    </a>
                    <a href="{{ route('student.profile') }}" class="nav-link">
                        <i data-feather="user" width="20" height="20"></i>
                        Profile
                    </a>
                    <a href="{{ route('student.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-feather="log-out" width="20" height="20"></i>
                        Logout
                    </a>
                </nav>
                <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="assignments-header">
                    <h1>My Assignments</h1>
                    <p>Track your assignments and submissions</p>
                </div>

                <!-- Filter Tabs -->
                <div class="assignment-filters mb-4">
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">All Assignments</button>
                        <button class="filter-tab" data-filter="pending">Pending</button>
                        <button class="filter-tab" data-filter="submitted">Submitted</button>
                        <button class="filter-tab" data-filter="graded">Graded</button>
                    </div>
                </div>

                <!-- Assignment Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon pending">
                                <i data-feather="clock" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>3</h3>
                                <p>Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon submitted">
                                <i data-feather="upload" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>5</h3>
                                <p>Submitted</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon graded">
                                <i data-feather="check-circle" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>2</h3>
                                <p>Graded</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-icon overdue">
                                <i data-feather="alert-triangle" width="24" height="24"></i>
                            </div>
                            <div class="stats-content">
                                <h3>1</h3>
                                <p>Overdue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignments List -->
                <div class="assignments-list">
                    <!-- Sample Assignment 1 - Pending -->
                    <div class="assignment-card pending" data-status="pending">
                        <div class="assignment-header">
                            <div class="assignment-info">
                                <h5 class="assignment-title">Research Proposal</h5>
                                <p class="assignment-course">PHILOSOPHY OF DOCTOR IN MANAGEMENT BY RESEARCH</p>
                            </div>
                            <div class="assignment-status">
                                <span class="badge badge-pending">Pending</span>
                            </div>
                        </div>
                        <div class="assignment-body">
                            <p class="assignment-description">
                                Submit a comprehensive research proposal outlining your research objectives, methodology, and expected outcomes.
                            </p>
                            <div class="assignment-meta">
                                <div class="meta-item">
                                    <i data-feather="calendar" width="16" height="16"></i>
                                    <span>Due: Dec 15, 2025</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="file" width="16" height="16"></i>
                                    <span>PDF, DOC, DOCX</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="clock" width="16" height="16"></i>
                                    <span>5 days left</span>
                                </div>
                            </div>
                        </div>
                        <div class="assignment-footer">
                            <button class="btn btn-primary btn-sm">Submit Assignment</button>
                            <button class="btn btn-outline-secondary btn-sm">View Details</button>
                        </div>
                    </div>

                    <!-- Sample Assignment 2 - Submitted -->
                    <div class="assignment-card submitted" data-status="submitted">
                        <div class="assignment-header">
                            <div class="assignment-info">
                                <h5 class="assignment-title">Literature Review</h5>
                                <p class="assignment-course">PHILOSOPHY OF DOCTOR IN MANAGEMENT BY RESEARCH</p>
                            </div>
                            <div class="assignment-status">
                                <span class="badge badge-submitted">Submitted</span>
                            </div>
                        </div>
                        <div class="assignment-body">
                            <p class="assignment-description">
                                Comprehensive literature review covering the last 5 years of research in your field.
                            </p>
                            <div class="assignment-meta">
                                <div class="meta-item">
                                    <i data-feather="calendar" width="16" height="16"></i>
                                    <span>Submitted: Nov 20, 2025</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="file" width="16" height="16"></i>
                                    <span>literature_review.pdf</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="clock" width="16" height="16"></i>
                                    <span>Under Review</span>
                                </div>
                            </div>
                        </div>
                        <div class="assignment-footer">
                            <button class="btn btn-outline-primary btn-sm">View Submission</button>
                            <button class="btn btn-outline-secondary btn-sm">Download</button>
                        </div>
                    </div>

                    <!-- Sample Assignment 3 - Graded -->
                    <div class="assignment-card graded" data-status="graded">
                        <div class="assignment-header">
                            <div class="assignment-info">
                                <h5 class="assignment-title">Research Methodology</h5>
                                <p class="assignment-course">PHILOSOPHY OF DOCTOR IN MANAGEMENT BY RESEARCH</p>
                            </div>
                            <div class="assignment-status">
                                <span class="badge badge-graded">Graded</span>
                            </div>
                        </div>
                        <div class="assignment-body">
                            <p class="assignment-description">
                                Detailed methodology section for your research proposal.
                            </p>
                            <div class="assignment-meta">
                                <div class="meta-item">
                                    <i data-feather="calendar" width="16" height="16"></i>
                                    <span>Graded: Nov 15, 2025</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="award" width="16" height="16"></i>
                                    <span>Grade: A- (85%)</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="message-circle" width="16" height="16"></i>
                                    <span>Feedback Available</span>
                                </div>
                            </div>
                        </div>
                        <div class="assignment-footer">
                            <button class="btn btn-success btn-sm">View Grade</button>
                            <button class="btn btn-outline-primary btn-sm">View Feedback</button>
                        </div>
                    </div>

                    <!-- Sample Assignment 4 - Overdue -->
                    <div class="assignment-card overdue" data-status="overdue">
                        <div class="assignment-header">
                            <div class="assignment-info">
                                <h5 class="assignment-title">Data Collection Plan</h5>
                                <p class="assignment-course">PHILOSOPHY OF DOCTOR IN MANAGEMENT BY RESEARCH</p>
                            </div>
                            <div class="assignment-status">
                                <span class="badge badge-overdue">Overdue</span>
                            </div>
                        </div>
                        <div class="assignment-body">
                            <p class="assignment-description">
                                Detailed plan for data collection including sampling methods and tools.
                            </p>
                            <div class="assignment-meta">
                                <div class="meta-item">
                                    <i data-feather="calendar" width="16" height="16"></i>
                                    <span>Due: Nov 10, 2025</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="alert-triangle" width="16" height="16"></i>
                                    <span>3 days overdue</span>
                                </div>
                                <div class="meta-item">
                                    <i data-feather="file" width="16" height="16"></i>
                                    <span>PDF, DOC, DOCX</span>
                                </div>
                            </div>
                        </div>
                        <div class="assignment-footer">
                            <button class="btn btn-warning btn-sm">Submit Now</button>
                            <button class="btn btn-outline-secondary btn-sm">Request Extension</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.student-assignments {
    min-height: 100vh;
    background-color: #f8f9fa;
}

.sidebar {
    background: #2d3748;
    min-height: 100vh;
    padding: 0;
}

.sidebar-header {
    background: #1a202c;
    padding: 1.5rem;
    color: white;
    border-bottom: 1px solid #4a5568;
}

.sidebar-header h4 {
    margin: 0;
    font-weight: bold;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #a0aec0;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: #4a5568;
    color: white;
}

.nav-link.active {
    background: #0056d2;
    color: white;
    border-left-color: #0041a3;
}

.nav-link i {
    margin-right: 0.75rem;
}

.main-content {
    padding: 2rem;
}

.assignments-header h1 {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.assignments-header p {
    color: #718096;
    margin-bottom: 2rem;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    background: white;
    padding: 0.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.filter-tab {
    padding: 0.75rem 1.5rem;
    border: none;
    background: transparent;
    color: #718096;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-tab.active,
.filter-tab:hover {
    background: #0056d2;
    color: white;
}

.stats-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
}

.stats-icon.pending {
    background: #f59e0b;
}

.stats-icon.submitted {
    background: #3b82f6;
}

.stats-icon.graded {
    background: #10b981;
}

.stats-icon.overdue {
    background: #ef4444;
}

.stats-content h3 {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2d3748;
    margin: 0;
}

.stats-content p {
    color: #718096;
    margin: 0;
    font-size: 0.9rem;
}

.assignment-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.assignment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.assignment-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.assignment-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.assignment-course {
    color: #718096;
    font-size: 0.9rem;
    margin: 0;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-pending {
    background: #fef3c7;
    color: #92400e;
}

.badge-submitted {
    background: #dbeafe;
    color: #1e40af;
}

.badge-graded {
    background: #d1fae5;
    color: #065f46;
}

.badge-overdue {
    background: #fee2e2;
    color: #991b1b;
}

.assignment-body {
    padding: 1.5rem;
}

.assignment-description {
    color: #4a5568;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.assignment-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    color: #718096;
    font-size: 0.85rem;
}

.meta-item i {
    margin-right: 0.5rem;
}

.assignment-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .filter-tabs {
        flex-wrap: wrap;
    }
    
    .assignment-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .assignment-footer {
        flex-direction: column;
    }
    
    .assignment-footer .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const assignmentCards = document.querySelectorAll('.assignment-card');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            filterTabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Show/hide assignment cards based on filter
            assignmentCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-status') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush

