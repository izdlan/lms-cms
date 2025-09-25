@extends('layouts.staff')

@section('title', 'Manage Courses')

@section('content')
<div class="dashboard-header">
    <h1>Course Management</h1>
    <p class="text-muted">Manage courses, add content, and track student progress</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Course
    </button>
</div>

    <!-- Courses Grid -->
    <div class="row">
        @foreach($courses as $course)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="course-card">
                <div class="course-header">
                    <div class="course-code">{{ $course['code'] }}</div>
                    <div class="course-status {{ $course['status'] }}">
                        {{ ucfirst($course['status']) }}
                    </div>
                </div>
                <div class="course-body">
                    <h5 class="course-title">{{ $course['title'] }}</h5>
                    <p class="course-description">{{ $course['description'] }}</p>
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span>{{ $course['instructor'] }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $course['students_count'] }} students</span>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <a href="/maintenance" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="/maintenance" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="/maintenance" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus"></i> Add Content
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Course Statistics -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Course Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ count($courses) }}</div>
                                <div class="stat-label">Total Courses</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ array_sum(array_column($courses, 'students_count')) }}</div>
                                <div class="stat-label">Total Students</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ count(array_filter($courses, fn($c) => $c['status'] === 'active')) }}</div>
                                <div class="stat-label">Active Courses</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">12</div>
                                <div class="stat-label">Assignments Due</div>
                            </div>
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
.staff-courses {
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-title {
    color: #2d3748;
    font-size: 1.8rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

.course-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.course-header {
    background: #20c997;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.course-code {
    font-weight: bold;
    font-size: 1.1rem;
}

.course-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.course-status.active {
    background: rgba(255,255,255,0.2);
    color: white;
}

.course-body {
    padding: 20px;
}

.course-title {
    color: #2d3748;
    font-size: 1.2rem;
    font-weight: bold;
    margin: 0 0 10px 0;
}

.course-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0 0 15px 0;
    line-height: 1.5;
}

.course-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
    font-size: 0.9rem;
}

.meta-item i {
    width: 16px;
    color: #20c997;
}

.course-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.course-actions .btn {
    flex: 1;
    min-width: 80px;
}

.stat-item {
    text-align: center;
    padding: 20px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #20c997;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

.btn-primary {
    background: #20c997;
    border-color: #20c997;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
}

.btn-primary:hover {
    background: #1a9f7a;
    border-color: #1a9f7a;
}

.btn-outline-primary {
    color: #20c997;
    border-color: #20c997;
}

.btn-outline-primary:hover {
    background: #20c997;
    border-color: #20c997;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
}

.btn-outline-success {
    color: #28a745;
    border-color: #28a745;
}

.btn-outline-success:hover {
    background: #28a745;
    border-color: #28a745;
}

@media (max-width: 768px) {
    .staff-courses {
        padding: 15px;
    }
    
    .page-header .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .course-actions {
        flex-direction: column;
    }
    
    .course-actions .btn {
        flex: none;
    }
}
</style>
@endpush
