@extends('layouts.finance-admin')

@section('title', 'Finance Admin Dashboard | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
    <div class="page-header">
        <h1 class="page-title">Finance Admin Dashboard</h1>
        <p class="page-subtitle">Manage student accounts and payment status</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_students'] }}</h3>
                    <p>Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['active_students'] }}</h3>
                    <p>Active Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon blocked">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['blocked_students'] }}</h3>
                    <p>Blocked Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['pending_payments'] }}</h3>
                    <p>Pending Payments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance-admin.students') }}" class="btn btn-primary w-100">
                                <i class="fas fa-users me-2"></i>View All Students
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance-admin.pending-payments') }}" class="btn btn-warning w-100">
                                <i class="fas fa-clock me-2"></i>Pending Payments
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance-admin.reports') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-bar me-2"></i>Generate Reports
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance-admin.password.change') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Students with Pending Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Students with Pending Payments</h5>
                    <a href="{{ route('finance-admin.pending-payments') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($studentsWithPendingPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-graduation-cap me-1"></i> Student ID</th>
                                        <th><i class="fas fa-user me-1"></i> Name</th>
                                        <th><i class="fas fa-envelope me-1"></i> Email</th>
                                        <th><i class="fas fa-phone me-1"></i> Phone</th>
                                        <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                        <th><i class="fas fa-cogs me-1"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentsWithPendingPayments as $data)
                                        @php
                                            $student = $data['student'];
                                        @endphp
                                        <tr>
                                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->phone ?? 'N/A' }}</td>
                                            <td>
                                                @if($student->is_blocked)
                                                    <span class="badge bg-danger">Blocked</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('finance-admin.student.show', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted"><i class="fas fa-smile me-2"></i>No Pending Payments</h5>
                            <p class="text-muted"><i class="fas fa-info-circle me-1"></i>All students are up to date with their payments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.finance-admin-dashboard {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Fix table column widths for dashboard */
.table th:nth-child(1),
.table td:nth-child(1) {
    width: 15% !important; /* Student ID column */
    min-width: 100px !important;
}

.table th:nth-child(2),
.table td:nth-child(2) {
    width: 35% !important; /* Name column */
    min-width: 200px !important;
}

.table th:nth-child(3),
.table td:nth-child(3) {
    width: 30% !important; /* Email column */
    min-width: 200px !important;
    word-break: break-all !important;
}

.table th:nth-child(4),
.table td:nth-child(4) {
    width: 20% !important; /* Actions column */
    min-width: 150px !important;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    background: #e9ecef;
    color: #6c757d;
    font-size: 1.5rem;
}

.stat-icon.active {
    background: #d4edda;
    color: #155724;
}

.stat-icon.blocked {
    background: #f8d7da;
    color: #721c24;
}

.stat-icon.warning {
    background: #fff3cd;
    color: #856404;
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.card-header h5 {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    color: #333;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #666;
    margin: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>
@endpush