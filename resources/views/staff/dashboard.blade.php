@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('content')
<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p class="text-muted">Manage courses, announcements, and content for students.</p>
</div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $totalStudents }}</h3>
                        <p class="stats-label">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $totalStaff }}</h3>
                        <p class="stats-label">Total Staff</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $recentStudents->count() }}</h3>
                        <p class="stats-label">Recent Registrations</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('staff.courses') }}" class="action-btn">
                                    <i class="fas fa-book-open"></i>
                                    <span>Manage Courses</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('staff.announcements') }}" class="action-btn">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Announcements</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('staff.contents') }}" class="action-btn">
                                    <i class="fas fa-file-alt"></i>
                                    <span>Course Contents</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('staff.students') }}" class="action-btn">
                                    <i class="fas fa-users"></i>
                                    <span>View Students</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Students -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Student Registrations</h5>
                    </div>
                    <div class="card-body">
                        @if($recentStudents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Student ID</th>
                                            <th>Program</th>
                                            <th>Registered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentStudents as $student)
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                                            <td>{{ $student->programme_name ?? 'N/A' }}</td>
                                            <td>{{ $student->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent student registrations found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.staff-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
}

.main-content {
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-title {
    color: #2d3748;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    margin: 0;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stats-icon {
    background: #20c997;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 20px;
}

.stats-content {
    flex: 1;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
}

.stats-label {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    text-decoration: none;
    color: #2d3748;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: 100%;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    color: #20c997;
    text-decoration: none;
}

.action-btn i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #20c997;
}

.action-btn span {
    font-weight: 600;
    font-size: 0.9rem;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: #20c997;
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
    border: none;
}

.card-title {
    margin: 0;
    font-weight: 600;
    font-size: 1.2rem;
}

.card-body {
    padding: 25px;
}

.table th {
    border: none;
    color: #6c757d;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .main-content {
        padding: 15px;
    }
    
    .stats-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .action-btn {
        padding: 15px;
    }
}
</style>
@endpush
