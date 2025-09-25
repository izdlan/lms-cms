@extends('layouts.staff')

@section('title', 'Announcements')

@section('content')
<div class="dashboard-header">
    <h1>Announcements</h1>
    <p class="text-muted">Create and manage announcements for students</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Announcement
    </button>
</div>

    <!-- Announcements List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Announcements</h5>
                </div>
                <div class="card-body">
                    @if(count($announcements) > 0)
                        <div class="announcements-list">
                            @foreach($announcements as $announcement)
                            <div class="announcement-item">
                                <div class="announcement-header">
                                    <div class="announcement-title">
                                        <h6>{{ $announcement['title'] }}</h6>
                                        <span class="announcement-date">{{ $announcement['created_at'] }}</span>
                                    </div>
                                    <div class="announcement-actions">
                                        <span class="status-badge {{ $announcement['status'] }}">
                                            {{ ucfirst($announcement['status']) }}
                                        </span>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="announcement-content">
                                    <p>{{ $announcement['content'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No announcements yet</h5>
                            <p class="text-muted">Create your first announcement to communicate with students</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count($announcements) }}</div>
                    <div class="stat-label">Total Announcements</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count(array_filter($announcements, fn($a) => $a['status'] === 'published')) }}</div>
                    <div class="stat-label">Published</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ count(array_filter($announcements, fn($a) => $a['status'] === 'draft')) }}</div>
                    <div class="stat-label">Drafts</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">1,234</div>
                    <div class="stat-label">Total Views</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.staff-announcements {
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

.announcements-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.announcement-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #20c997;
}

.announcement-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.announcement-title h6 {
    color: #2d3748;
    font-weight: bold;
    margin: 0 0 5px 0;
    font-size: 1.1rem;
}

.announcement-date {
    color: #6c757d;
    font-size: 0.9rem;
}

.announcement-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.published {
    background: #d1edff;
    color: #0c5460;
}

.status-badge.draft {
    background: #fff3cd;
    color: #856404;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.announcement-content p {
    color: #6c757d;
    margin: 0;
    line-height: 1.6;
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

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
}

@media (max-width: 768px) {
    .staff-announcements {
        padding: 15px;
    }
    
    .page-header .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .announcement-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .announcement-actions {
        align-self: flex-start;
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
