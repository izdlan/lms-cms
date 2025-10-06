@extends('layouts.finance-admin')

@section('title', 'Reports | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                @include('finance-admin.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="page-header">
                        <h1 class="page-title">Reports</h1>
                        <p class="page-subtitle">Generate financial and student reports</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-file-excel me-2"></i>Student Reports</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary" onclick="generateReport('all-students')">
                                            <i class="fas fa-users me-2"></i>All Students Report
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="generateReport('pending-payments')">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Pending Payments Report
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="generateReport('blocked-students')">
                                            <i class="fas fa-user-times me-2"></i>Blocked Students Report
                                        </button>
                                        <button class="btn btn-outline-info" onclick="generateReport('payment-history')">
                                            <i class="fas fa-receipt me-2"></i>Payment History Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar me-2"></i>Financial Reports</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-success" onclick="generateReport('revenue-summary')">
                                            <i class="fas fa-dollar-sign me-2"></i>Revenue Summary
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="generateReport('monthly-payments')">
                                            <i class="fas fa-calendar-alt me-2"></i>Monthly Payments
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="generateReport('overdue-analysis')">
                                            <i class="fas fa-clock me-2"></i>Overdue Analysis
                                        </button>
                                        <button class="btn btn-outline-info" onclick="generateReport('student-statistics')">
                                            <i class="fas fa-chart-pie me-2"></i>Student Statistics
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Reports -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-history me-2"></i>Recent Reports</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Report Name</th>
                                            <th>Generated Date</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>All Students Report</td>
                                            <td>2025-10-06 09:30:00</td>
                                            <td><span class="badge bg-primary">Student</span></td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pending Payments Report</td>
                                            <td>2025-10-06 09:15:00</td>
                                            <td><span class="badge bg-warning">Financial</span></td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Monthly Revenue Report</td>
                                            <td>2025-10-05 16:45:00</td>
                                            <td><span class="badge bg-info">Financial</span></td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateReport(reportType) {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
    button.disabled = true;
    
    // Simulate report generation
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Show success message
        alert('Report generated successfully! This would typically download the report file.');
    }, 2000);
}
</script>
@endpush

@push('styles')
<style>
.finance-admin-dashboard {
    background-color: #f8f9fa;
    min-height: 100vh;
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
    background: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}
</style>
@endpush
