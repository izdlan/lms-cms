@extends('layouts.admin')

@section('title', 'User Activity Logs')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>User Activity Logs</h1>
                            <p class="text-muted mb-0">Monitor user login and logout activities</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn-modern btn-modern-secondary">
                                <i data-feather="arrow-left"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card-modern">
                            <div class="card-body text-center">
                                <div class="text-primary mb-2">
                                    <i data-feather="log-in" width="32" height="32"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['total_logins'] }}</h4>
                                <p class="text-muted mb-0">Total Logins</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-modern">
                            <div class="card-body text-center">
                                <div class="text-success mb-2">
                                    <i data-feather="log-out" width="32" height="32"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['total_logouts'] }}</h4>
                                <p class="text-muted mb-0">Total Logouts</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-modern">
                            <div class="card-body text-center">
                                <div class="text-danger mb-2">
                                    <i data-feather="x-circle" width="32" height="32"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['failed_logins'] }}</h4>
                                <p class="text-muted mb-0">Failed Logins</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-modern">
                            <div class="card-body text-center">
                                <div class="text-info mb-2">
                                    <i data-feather="users" width="32" height="32"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['unique_users'] }}</h4>
                                <p class="text-muted mb-0">Unique Users</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.user-activity-logs') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="days" class="form-label">Time Period</label>
                                <select class="form-control" id="days" name="days">
                                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 days</option>
                                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
                                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="activity_type" class="form-label">Activity Type</label>
                                <select class="form-control" id="activity_type" name="activity_type">
                                    <option value="all" {{ $activityType == 'all' ? 'selected' : '' }}>All Activities</option>
                                    <option value="login" {{ $activityType == 'login' ? 'selected' : '' }}>Logins</option>
                                    <option value="logout" {{ $activityType == 'logout' ? 'selected' : '' }}>Logouts</option>
                                    <option value="failed_login" {{ $activityType == 'failed_login' ? 'selected' : '' }}>Failed Logins</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="user_role" class="form-label">User Role</label>
                                <select class="form-control" id="user_role" name="user_role">
                                    <option value="all" {{ $userRole == 'all' ? 'selected' : '' }}>All Roles</option>
                                    <option value="student" {{ $userRole == 'student' ? 'selected' : '' }}>Students</option>
                                    <option value="admin" {{ $userRole == 'admin' ? 'selected' : '' }}>Admins</option>
                                    <option value="lecturer" {{ $userRole == 'lecturer' ? 'selected' : '' }}>Lecturers</option>
                                    <option value="finance_admin" {{ $userRole == 'finance_admin' ? 'selected' : '' }}>Finance Admins</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn-modern btn-modern-primary">
                                        <i data-feather="filter"></i>
                                        Apply Filters
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Activity Logs Table -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Activity Logs ({{ $activities->total() }} records)</h5>
                            <div class="d-flex gap-2">
                                <div class="search-box">
                                    <input type="text" class="form-control-modern" placeholder="Search activities..." id="searchInput">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($activities->count() > 0)
                            <div class="table-responsive">
                                <table class="table-modern" id="activitiesTable">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Activity</th>
                                            <th>Role</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>IP Address</th>
                                            <th>Date & Time</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activities as $activity)
                                            <tr>
                                                <td>
                                                    @if($activity->user)
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <div class="bg-primary-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                    <i data-feather="user" width="16" height="16" class="text-primary"></i>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold">{{ $activity->user->name }}</div>
                                                                <small class="text-muted">{{ $activity->user->email }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Unknown User</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($activity->activity_type)
                                                        @case('login')
                                                            <span class="badge-modern badge-modern-success">Login</span>
                                                            @break
                                                        @case('logout')
                                                            <span class="badge-modern badge-modern-info">Logout</span>
                                                            @break
                                                        @case('failed_login')
                                                            <span class="badge-modern badge-modern-danger">Failed Login</span>
                                                            @break
                                                        @default
                                                            <span class="badge-modern badge-modern-secondary">{{ ucfirst($activity->activity_type) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="badge-modern badge-modern-secondary">{{ ucfirst($activity->user_role) }}</span>
                                                </td>
                                                <td>
                                                    @if($activity->login_method)
                                                        <span class="text-muted">{{ strtoupper($activity->login_method) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($activity->status)
                                                        @case('success')
                                                            <span class="badge-modern badge-modern-success">Success</span>
                                                            @break
                                                        @case('failed')
                                                            <span class="badge-modern badge-modern-danger">Failed</span>
                                                            @break
                                                        @case('blocked')
                                                            <span class="badge-modern badge-modern-warning">Blocked</span>
                                                            @break
                                                        @default
                                                            <span class="badge-modern badge-modern-secondary">{{ ucfirst($activity->status) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $activity->ip_address ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-semibold">{{ $activity->created_at->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($activity->notes)
                                                        <span class="text-muted">{{ $activity->notes }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Enhanced Pagination -->
                            <div class="pagination-container">
                                <div class="pagination-info">
                                    <div class="pagination-text">
                                        Showing {{ $activities->firstItem() ?? 0 }} to {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} activities
                                    </div>
                                    <div class="pagination-controls">
                                        <span class="text-muted">Page {{ $activities->currentPage() }} of {{ $activities->lastPage() }}</span>
                                    </div>
                                </div>
                                {{ $activities->links('pagination.admin-pagination') }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="mb-4">
                                    <div class="bg-gray-100 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i data-feather="activity" width="32" height="32" class="text-muted"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted mb-2">No activity logs found</h5>
                                <p class="text-muted mb-4">No user activities match your current filters.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Safely replace feather icons
    if (typeof safeFeatherReplace === 'function') {
        safeFeatherReplace();
    } else {
        try {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.warn('Feather icons error:', error);
        }
    }
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('activitiesTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }
});
</script>
@endpush
