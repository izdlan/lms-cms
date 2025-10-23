@extends('layouts.app')

@section('title', 'Bills | Student | Olympia Education')

@section('content')
<style>
/* Make action button icons more visible */
.btn-group .btn i {
    font-size: 16px !important;
    font-weight: bold !important;
    color: white !important;
}

.btn-group .btn {
    min-width: 40px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.btn-group .btn-success {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.btn-group .btn-info {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
}

.btn-group .btn-warning {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-group .btn-warning i {
    color: #212529 !important;
}
</style>
<div class="student-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                @include('student.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="page-header">
                        <h1 class="page-title">Student Bills</h1>
                    </div>

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                <!-- Student Information Section -->
                <div class="student-info-section mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">Personal Information</div>
                                <div class="info-content">
                                    <div class="info-row">
                                        <div class="info-label">Full Name:</div>
                                        <div class="info-value">{{ auth('student')->user()->name ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Email:</div>
                                        <div class="info-value">{{ auth('student')->user()->email ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Phone Number:</div>
                                        <div class="info-value">{{ auth('student')->user()->phone ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">IC Number:</div>
                                        <div class="info-value">{{ auth('student')->user()->ic ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Student ID:</div>
                                        <div class="info-value">{{ auth('student')->user()->student_id ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">Academic Information</div>
                                <div class="info-content">
                                    <div class="info-row">
                                        <div class="info-label">Program:</div>
                                        <div class="info-value">{{ auth('student')->user()->program ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Study Mode:</div>
                                        <div class="info-value">{{ auth('student')->user()->study_mode ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Semester:</div>
                                        <div class="info-value">{{ auth('student')->user()->semester ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Sponsor Code:</div>
                                        <div class="info-value">{{ auth('student')->user()->sponsor_code ?? '-' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Sponsor Description:</div>
                                        <div class="info-value">{{ auth('student')->user()->sponsor_description ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bills Table Section -->
                <div class="bills-section">
                    <h2 class="section-title">List of Bills</h2>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered bills-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i> Bill Number</th>
                                    <th><i class="fas fa-calendar me-1"></i> Bill Date</th>
                                    <th><i class="fas fa-graduation-cap me-1"></i> Session</th>
                                    <th><i class="fas fa-tag me-1"></i> Bill Type</th>
                                    <th><i class="fas fa-dollar-sign me-1"></i> Amount (RM)</th>
                                    <th><i class="fas fa-info-circle me-1"></i> Payment Status</th>
                                    <th><i class="fas fa-cogs me-1"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $bill)
                                    <tr>
                                        <td>
                                            @if($bill->isPaid())
                                                <a href="{{ route('student.receipt') }}?payment_id={{ $bill->payment_id }}" class="bill-link">{{ $bill->bill_number }}</a>
                                            @else
                                                <a href="{{ route('student.payment') }}?bill_id={{ $bill->id }}" class="bill-link">{{ $bill->bill_number }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                                        <td>{{ $bill->session }}</td>
                                        <td>{{ $bill->bill_type }}</td>
                                        <td class="amount">{{ $bill->formatted_amount }}</td>
                                        <td>
                                            <span class="status {{ $bill->status }}">{{ ucfirst($bill->status) }}</span>
                                            @if($bill->isOverdue())
                                                <small class="text-danger d-block">Overdue</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('student.invoice.view', $bill->id) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View Invoice">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('student.invoice.download', $bill->id) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Download PDF">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @if($bill->isPaid())
                                                    <a href="{{ route('student.receipt') }}?payment_id={{ $bill->payment_id }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View Receipt">
                                                        <i class="bi bi-receipt"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('student.payment') }}?bill_id={{ $bill->id }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Make Payment">
                                                        <i class="bi bi-credit-card"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="file-text" width="48" height="48" class="mb-3"></i>
                                                <p>No bills found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($bills->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $bills->links() }}
                        </div>
                    @endif
                </div>

                <!-- Payment Instructions Section -->
                <div class="payment-instructions mt-4">
                    <div class="instructions-box">
                        <h4 class="instructions-title">HOW TO MAKE PAYMENT:</h4>
                        <div class="instructions-content">
                            <ol class="steps-list">
                                <li><strong>Click on the Bill Number</strong> - Click on any unpaid bill number in the table above</li>
                                <li><strong>Select Payment Method</strong> - Choose from available payment options (Online Banking, Credit Card, etc.)</li>
                                <li><strong>Enter Payment Details</strong> - Fill in your payment information and billing address</li>
                                <li><strong>Review & Confirm</strong> - Double-check all details before proceeding with payment</li>
                                <li><strong>Complete Payment</strong> - Follow the secure payment process to complete your transaction</li>
                                <li><strong>Receive Confirmation</strong> - You will receive an email confirmation once payment is processed</li>
                            </ol>
                            <p class="instructions-note">
                                <strong>Note:</strong> Payment processing may take 1-2 business days to reflect in your account.
                            </p>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Override Bootstrap and layout conflicts */
.student-dashboard .main-content {
    padding: 15px !important;
    background: #f8f9fa !important;
    min-height: calc(100vh - 80px) !important;
    margin-left: 0 !important;
    width: 100% !important;
    max-width: none !important;
}

.student-dashboard .container-fluid {
    padding: 0 !important;
    width: 100% !important;
    max-width: none !important;
}

.student-dashboard .row {
    margin: 0 !important;
    width: 100% !important;
}

.student-dashboard .col-md-3,
.student-dashboard .col-md-9,
.student-dashboard .col-lg-2,
.student-dashboard .col-lg-10 {
    padding: 0 !important;
}

/* Fix spacing issues */
.page-header {
    margin-bottom: 20px !important;
    padding: 0 !important;
}

.page-title {
    color: #2c3e50;
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
}

.student-info-section {
    margin-bottom: 20px !important;
}

.bills-section {
    margin-bottom: 20px !important;
}

.info-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.info-header {
    background: #dc3545;
    color: white;
    padding: 15px 20px;
    font-weight: 600;
    font-size: 1.1rem;
}

.info-content {
    padding: 20px;
}

.info-row {
    display: flex;
    margin-bottom: 12px;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 8px;
}

.info-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #495057;
    width: 40%;
    min-width: 150px;
}

.info-value {
    color: #212529;
    flex: 1;
}

.bills-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 30px;
}

.section-title {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.bills-table {
    margin: 0;
    font-size: 0.9rem;
}

.bills-table thead th {
    background: #6c757d;
    color: white;
    border: none;
    padding: 15px 12px;
    font-weight: 600;
    text-align: center;
}

.bills-table tbody td {
    padding: 12px;
    vertical-align: middle;
    border-color: #dee2e6;
}

.bill-link {
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
}

.bill-link:hover {
    color: #495057;
    text-decoration: underline;
}

.amount {
    text-align: right;
    font-weight: 600;
    color: #20c997;
}

.status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    display: inline-block;
    min-width: 100px;
}

.status.paid {
    background: #d1edff;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status.overdue {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.payment-instructions {
    margin-top: 30px;
}

.instructions-box {
    background: white;
    border: 2px solid #20c997;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.instructions-title {
    color: #20c997;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
}

.instructions-content {
    color: #495057;
}

.steps-list {
    margin: 0 0 20px 0;
    padding-left: 20px;
    font-size: 1rem;
    line-height: 1.8;
}

.steps-list li {
    margin-bottom: 12px;
    color: #212529;
}

.steps-list li strong {
    color: #20c997;
    font-weight: 700;
}

.instructions-note {
    background: #f8f9fa;
    border-left: 4px solid #20c997;
    padding: 1rem 1.5rem;
    margin: 0;
    font-size: 0.95rem;
    color: #495057;
    border-radius: 0 4px 4px 0;
}

.instructions-note strong {
    color: #20c997;
    font-weight: 700;
}

@media (max-width: 768px) {
    .main-content {
        padding: 15px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .info-row {
        flex-direction: column;
    }
    
    .info-label {
        width: 100%;
        margin-bottom: 5px;
    }
    
    .bills-table {
        font-size: 0.8rem;
    }
    
    .bills-table thead th,
    .bills-table tbody td {
        padding: 8px 6px;
    }
}
</style>
@endsection
