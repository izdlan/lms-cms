<!-- Course Sidebar -->
<div class="course-sidebar">
    <div class="sidebar-header">
        <div class="course-logo">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="course-title">
                <h4>{{ $courseInfo['name'] ?? strtoupper($courseId ?? 'COURSE') }}</h4>
                <small>{{ $courseInfo['title'] ?? 'Course Title' }}</small>
            </div>
        </div>
    </div>

    <div class="sidebar-content">
        <div class="navigation-section">
            <h6 class="nav-title">NAVIGATION</h6>
            <ul class="nav-list">
                <li class="nav-item {{ request()->routeIs('course.summary') ? 'active' : '' }}">
                    <a href="{{ route('course.summary', $courseId) }}" class="nav-link">
                        <i class="fas fa-tv nav-icon"></i>
                        <span>Course Summary</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('course.announcements') ? 'active' : '' }}">
                    <a href="{{ route('course.announcements', $courseId) }}" class="nav-link">
                        <i class="fas fa-bullhorn nav-icon"></i>
                        <span>Announcement</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('course.contents') ? 'active' : '' }}">
                    <a href="{{ route('course.contents', $courseId) }}" class="nav-link">
                        <i class="fas fa-edit nav-icon"></i>
                        <span>Course Content</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/maintenance" class="nav-link">
                        <i class="fas fa-edit nav-icon"></i>
                        <span>Cont. Assessment</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/maintenance" class="nav-link">
                        <i class="fas fa-desktop nav-icon"></i>
                        <span>Learning Activities</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/maintenance" class="nav-link">
                        <i class="fas fa-comments nav-icon"></i>
                        <span>Course Forum</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/maintenance" class="nav-link">
                        <i class="fas fa-graduation-cap nav-icon"></i>
                        <span>Online Class</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/maintenance" class="nav-link">
                        <i class="fas fa-users nav-icon"></i>
                        <span>Group</span>
                        <i class="fas fa-chevron-right nav-arrow"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar-footer">
        <p class="copyright">© 2014 ~ 2025</p>
    </div>
</div>

<style>
.course-sidebar {
    width: 280px;
    height: 100vh;
    background: #2c3e50;
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #34495e;
    background: linear-gradient(135deg, #2c3e50, #34495e);
}

.course-logo {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #9b59b6, #f39c12);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    box-shadow: 0 4px 15px rgba(155, 89, 182, 0.3);
}

.course-title h4 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
    color: white;
    line-height: 1.2;
}

.course-title small {
    color: #bdc3c7;
    font-size: 12px;
    display: block;
    margin-top: 2px;
}

.sidebar-content {
    flex: 1;
    padding: 20px 0;
    overflow-y: auto;
}

.navigation-section {
    padding: 0 20px;
}

.nav-title {
    color: #95a5a6;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
    padding: 0 10px;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 5px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    color: #bdc3c7;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link:hover {
    background: #34495e;
    color: white;
    transform: translateX(5px);
}

.nav-item.active .nav-link {
    background: #3498db;
    color: white;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}

.nav-icon {
    width: 20px;
    margin-right: 12px;
    font-size: 16px;
    text-align: center;
}

.nav-arrow {
    margin-left: auto;
    font-size: 12px;
    opacity: 0.7;
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid #34495e;
    text-align: center;
}

.copyright {
    color: #7f8c8d;
    font-size: 12px;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .course-sidebar {
        width: 100%;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .course-sidebar.open {
        transform: translateX(0);
    }
}

/* Scrollbar styling */
.sidebar-content::-webkit-scrollbar {
    width: 4px;
}

.sidebar-content::-webkit-scrollbar-track {
    background: #2c3e50;
}

.sidebar-content::-webkit-scrollbar-thumb {
    background: #34495e;
    border-radius: 2px;
}

.sidebar-content::-webkit-scrollbar-thumb:hover {
    background: #3498db;
}
</style>
