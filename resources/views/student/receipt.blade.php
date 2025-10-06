@extends('layouts.app')

@section('title', 'Receipt | Student | Olympia Education')

@section('content')
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
                        <h1 class="page-title">Payment Receipt</h1>
                        <p class="page-subtitle">Receipt #{{ $receipt->receipt_number }}</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Receipt Details -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-receipt me-2"></i>Receipt Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label class="info-label">Receipt Number:</label>
                                                <span class="info-value">{{ $receipt->receipt_number }}</span>
                                            </div>
                                            <div class="info-group">
                                                <label class="info-label">Payment Date:</label>
                                                <span class="info-value">{{ $receipt->payment_date->format('d F Y H:i') }}</span>
                                            </div>
                                            <div class="info-group">
                                                <label class="info-label">Payment Method:</label>
                                                <span class="info-value">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label class="info-label">Invoice Number:</label>
                                                <span class="info-value">{{ $receipt->invoice->invoice_number }}</span>
                                            </div>
                                            <div class="info-group">
                                                <label class="info-label">Bill Type:</label>
                                                <span class="info-value">{{ $receipt->invoice->bill_type }}</span>
                                            </div>
                                            <div class="info-group">
                                                <label class="info-label">Session:</label>
                                                <span class="info-value">{{ $receipt->invoice->session }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($receipt->receipt_notes)
                                    <div class="info-group mt-3">
                                        <label class="info-label">Payment Notes:</label>
                                        <p class="info-value">{{ $receipt->receipt_notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Invoice Details -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-invoice me-2"></i>Original Invoice Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label class="info-label">Invoice Date:</label>
                                                <span class="info-value">{{ $receipt->invoice->invoice_date->format('d F Y') }}</span>
                                            </div>
                                            <div class="info-group">
                                                <label class="info-label">Due Date:</label>
                                                <span class="info-value">{{ $receipt->invoice->due_date->format('d F Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label class="info-label">Description:</label>
                                                <span class="info-value">{{ $receipt->invoice->description ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-money-bill-wave me-2"></i>Payment Summary
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="payment-summary">
                                        <div class="summary-row">
                                            <span class="summary-label">Amount Paid:</span>
                                            <span class="summary-value">RM {{ number_format($receipt->amount, 2) }}</span>
                                        </div>
                                        <div class="summary-row">
                                            <span class="summary-label">Payment Method:</span>
                                            <span class="summary-value">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</span>
                                        </div>
                                        @if($receipt->payment->transaction_id)
                                        <div class="summary-row">
                                            <span class="summary-label">Transaction ID:</span>
                                            <span class="summary-value">{{ $receipt->payment->transaction_id }}</span>
                                        </div>
                                        @endif
                                        <hr>
                                        <div class="summary-row total-row">
                                            <span class="summary-label">Status:</span>
                                            <span class="summary-value">
                                                <span class="badge bg-success">Payment Completed</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('student.receipt.pdf', $receipt->id) }}" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-download me-2"></i>Download PDF
                                    </a>
                                    <a href="{{ route('student.receipt.view-pdf', $receipt->id) }}" class="btn btn-warning w-100 mb-2" target="_blank">
                                        <i class="fas fa-file-pdf me-2"></i>View PDF
                                    </a>
                                    <a href="{{ route('student.bills') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Bills
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.student-dashboard .info-group {
    margin-bottom: 1rem;
}

.student-dashboard .info-label {
    font-weight: 600;
    color: #6c757d;
    display: block;
    margin-bottom: 0.25rem;
}

.student-dashboard .info-value {
    color: #212529;
    font-size: 1rem;
}

.payment-summary .summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0;
}

.payment-summary .summary-row.total-row {
    font-weight: bold;
    font-size: 1.1rem;
    border-top: 2px solid #dee2e6;
    margin-top: 1rem;
    padding-top: 1rem;
}

.payment-summary .summary-label {
    color: #6c757d;
}

.payment-summary .summary-value {
    font-weight: 600;
    color: #212529;
}
</style>
@endsection