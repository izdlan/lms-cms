@extends('layouts.finance-admin')

@section('title', 'Payment History | Finance Admin | Olympia Education')

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
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="page-title">Payment History</h1>
                                <p class="page-subtitle">{{ $student->name }} ({{ $student->ic }})</p>
                            </div>
                            <div>
                                <a href="{{ route('finance-admin.student.show', $student->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Student
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Student Summary -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-muted">Total Bills</h6>
                                        <h4 class="text-primary">{{ count($payments) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-muted">Paid Bills</h6>
                                        <h4 class="text-success">{{ collect($payments)->where('status', 'Paid')->count() }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-muted">Pending Bills</h6>
                                        <h4 class="text-warning">{{ collect($payments)->where('status', 'Pending')->count() }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-muted">Total Amount</h6>
                                        <h4 class="text-info">RM {{ number_format(collect($payments)->sum('amount'), 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-receipt me-2"></i>Payment History</h5>
                        </div>
                        <div class="card-body">
                            @if(count($payments) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Bill Number</th>
                                                <th>Bill Date</th>
                                                <th>Session</th>
                                                <th>Bill Type</th>
                                                <th>Amount (RM)</th>
                                                <th>Status</th>
                                                <th>Due/Paid Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $payment['bill_number'] }}</strong>
                                                    </td>
                                                    <td>{{ $payment['bill_date'] }}</td>
                                                    <td>{{ $payment['session'] }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $payment['bill_type'] === 'Tuition Fee' ? 'primary' : 'info' }}">
                                                            {{ $payment['bill_type'] }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>RM {{ number_format($payment['amount'], 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($payment['status'] === 'Paid')
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>Paid
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-clock me-1"></i>Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($payment['status'] === 'Paid')
                                                            <span class="text-success">
                                                                <i class="fas fa-calendar-check me-1"></i>
                                                                {{ $payment['paid_date'] ?? 'N/A' }}
                                                            </span>
                                                        @else
                                                            <span class="text-danger">
                                                                <i class="fas fa-calendar-times me-1"></i>
                                                                {{ $payment['due_date'] ?? 'N/A' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @if($payment['status'] === 'Paid')
                                                                <a href="{{ route('student.receipt') }}?bill_number={{ $payment['bill_number'] }}&bill_date={{ $payment['bill_date'] }}&session={{ $payment['session'] }}&bill_type={{ $payment['bill_type'] }}&amount={{ $payment['amount'] }}&payment_method=Online Banking&payment_date={{ $payment['paid_date'] }}" 
                                                                   class="btn btn-sm btn-success" title="View Receipt">
                                                                    <i class="fas fa-receipt"></i>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('student.payment') }}?bill_number={{ $payment['bill_number'] }}&bill_date={{ $payment['bill_date'] }}&session={{ $payment['session'] }}&bill_type={{ $payment['bill_type'] }}&amount={{ $payment['amount'] }}" 
                                                                   class="btn btn-sm btn-primary" title="View Payment">
                                                                    <i class="fas fa-credit-card"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <h5>No Payment History</h5>
                                    <p class="text-muted">This student has no payment records.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    @if(count($payments) > 0)
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie me-2"></i>Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="h4 text-success">{{ collect($payments)->where('status', 'Paid')->count() }}</div>
                                                <small class="text-muted">Paid Bills</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="h4 text-warning">{{ collect($payments)->where('status', 'Pending')->count() }}</div>
                                                <small class="text-muted">Pending Bills</small>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="h5 text-success">RM {{ number_format(collect($payments)->where('status', 'Paid')->sum('amount'), 2) }}</div>
                                                <small class="text-muted">Total Paid</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="h5 text-warning">RM {{ number_format(collect($payments)->where('status', 'Pending')->sum('amount'), 2) }}</div>
                                                <small class="text-muted">Total Pending</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Overdue Bills</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $overdueBills = collect($payments)->where('status', 'Pending')->filter(function($payment) {
                                            return isset($payment['due_date']) && strtotime($payment['due_date']) < time();
                                        });
                                    @endphp
                                    
                                    @if($overdueBills->count() > 0)
                                        <div class="alert alert-danger">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>{{ $overdueBills->count() }} Overdue Bill(s)</h6>
                                            <p class="mb-0">Total overdue amount: <strong>RM {{ number_format($overdueBills->sum('amount'), 2) }}</strong></p>
                                        </div>
                                        
                                        @if($overdueBills->count() > 0)
                                            <button type="button" class="btn btn-warning btn-sm" onclick="blockStudent({{ $student->id }})">
                                                <i class="fas fa-ban me-2"></i>Block Student for Non-Payment
                                            </button>
                                        @endif
                                    @else
                                        <div class="text-center text-success">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <p class="mb-0">No overdue bills</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Block Student Modal -->
<div class="modal fade" id="blockStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block Student for Non-Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blockStudentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to block <strong>{{ $student->name }}</strong> for non-payment?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This will prevent the student from accessing the system until payments are made.
                    </div>
                    <div class="mb-3">
                        <label for="block_reason" class="form-label">Reason for blocking:</label>
                        <textarea class="form-control" id="block_reason" name="block_reason" rows="3" 
                                  placeholder="Enter the reason for blocking this student..." required>Non-payment of outstanding bills</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function blockStudent(studentId) {
    document.getElementById('blockStudentForm').action = `/finance-admin/students/${studentId}/block`;
    new bootstrap.Modal(document.getElementById('blockStudentModal')).show();
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

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush
