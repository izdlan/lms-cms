@extends('layouts.staff')

@section('title', 'Assignments')

@section('content')
<div class="dashboard-header">
    <h1>Assignments</h1>
    <p class="text-muted">Create and manage assignments for students</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Assignment
    </button>
</div>

    <!-- Assignments List -->
    <div class="row">
        @foreach($assignments as $assignment)
        <div class="col-md-6 mb-4">
            <div class="assignment-card">
                <div class="assignment-header">
                    <div class="assignment-title">
                        <h5>{{ $assignment['title'] }}</h5>
                        <span class="course-name">{{ $assignment['course'] }}</span>
                    </div>
                    <div class="assignment-status">
                        <span class="status-badge active">Active</span>
                    </div>
                </div>
                <div class="assignment-body">
                    <p class="assignment-description">{{ $assignment['description'] }}</p>
                    <div class="assignment-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Due: {{ $assignment['due_date'] }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $assignment['submissions'] }}/{{ $assignment['total_students'] }} submitted</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-percentage"></i>
                            <span>{{ round(($assignment['submissions'] / $assignment['total_students']) * 100) }}% completion</span>
                        </div>
                    </div>
                </div>
                <div class="assignment-footer">
                    <div class="progress-container">
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($assignment['submissions'] / $assignment['total_students']) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="assignment-actions">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count($assignments) }}</div>
                    <div class="stat-label">Total Assignments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ array_sum(array_column($assignments, 'submissions')) }}</div>
                    <div class="stat-label">Total Submissions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count(array_filter($assignments, fn($a) => strtotime($a['due_date']) > time())) }}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ round(array_sum(array_column($assignments, 'submissions')) / array_sum(array_column($assignments, 'total_students')) * 100) }}%</div>
                    <div class="stat-label">Avg. Completion</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.staff-assignments {
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

.assignment-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.assignment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.assignment-header {
    background: #20c997;
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.assignment-title h5 {
    margin: 0 0 5px 0;
    font-weight: bold;
    font-size: 1.2rem;
}

.course-name {
    font-size: 0.9rem;
    opacity: 0.9;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: rgba(255,255,255,0.2);
    color: white;
}

.assignment-body {
    padding: 20px;
}

.assignment-description {
    color: #6c757d;
    margin: 0 0 20px 0;
    line-height: 1.6;
}

.assignment-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #6c757d;
    font-size: 0.9rem;
}

.meta-item i {
    width: 16px;
    color: #20c997;
}

.assignment-footer {
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.progress-container {
    margin-bottom: 15px;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background: #e9ecef;
}

.progress-bar {
    background: #20c997;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.assignment-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.assignment-actions .btn {
    flex: 1;
    min-width: 80px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-icon {
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

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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
    .staff-assignments {
        padding: 15px;
    }
    
    .page-header .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .assignment-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .assignment-actions {
        flex-direction: column;
    }
    
    .assignment-actions .btn {
        flex: none;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
}
</style>
@endpush
