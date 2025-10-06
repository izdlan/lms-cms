@extends('layouts.finance-admin')

@section('title', 'Invoice Details | Finance Admin | Olympia Education')

@section('content')
<div class="invoice-details-page">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Invoice Details</h1>
                <p class="page-subtitle">Invoice #{{ $invoice->invoice_number }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('finance-admin.invoices') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Invoices
                </a>
                <a href="{{ route('finance-admin.invoice.pdf', $invoice->id) }}" class="btn btn-success">
                    <i class="fas fa-download me-2"></i>Download PDF
                </a>
                <a href="{{ route('finance-admin.invoice.view-pdf', $invoice->id) }}" class="btn btn-warning" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>View PDF
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Invoice Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Invoice Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Invoice Number:</label>
                                <span class="info-value">{{ $invoice->invoice_number }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Invoice Date:</label>
                                <span class="info-value">{{ $invoice->invoice_date->format('d F Y') }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Due Date:</label>
                                <span class="info-value {{ $invoice->isOverdue() ? 'text-danger' : '' }}">
                                    {{ $invoice->due_date->format('d F Y') }}
                                    @if($invoice->isOverdue())
                                        <small class="text-danger">(Overdue)</small>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Bill Type:</label>
                                <span class="info-value">{{ $invoice->bill_type }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Session:</label>
                                <span class="info-value">{{ $invoice->session }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Status:</label>
                                <span class="info-value">
                                    @if($invoice->status === 'pending')
                                        @if($invoice->isOverdue())
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    @elseif($invoice->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($invoice->status === 'cancelled')
                                        <span class="badge bg-secondary">Cancelled</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($invoice->description)
                    <div class="info-group mt-3">
                        <label class="info-label">Description:</label>
                        <p class="info-value">{{ $invoice->description }}</p>
                    </div>
                    @endif
                    
                    @if($invoice->notes)
                    <div class="info-group mt-3">
                        <label class="info-label">Notes:</label>
                        <p class="info-value">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Student Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Name:</label>
                                <span class="info-value">{{ optional($invoice->user)->name ?? '—' }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Email:</label>
                                <span class="info-value">{{ optional($invoice->user)->email ?? '—' }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Phone:</label>
                                <span class="info-value">{{ optional($invoice->user)->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">IC Number:</label>
                                <span class="info-value">{{ optional($invoice->user)->ic ?? 'N/A' }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Student ID:</label>
                                <span class="info-value">{{ optional($invoice->user)->student_id ?? 'N/A' }}</span>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Address:</label>
                                <span class="info-value">{{ optional($invoice->user)->address ?? 'N/A' }}</span>
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
                            <span class="summary-label">Invoice Amount:</span>
                            <span class="summary-value">RM {{ number_format($invoice->amount, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Amount Paid:</span>
                            <span class="summary-value">RM {{ number_format($invoice->total_paid, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Remaining:</span>
                            <span class="summary-value {{ $invoice->amount - $invoice->total_paid > 0 ? 'text-danger' : 'text-success' }}">
                                RM {{ number_format($invoice->amount - $invoice->total_paid, 2) }}
                            </span>
                        </div>
                        <hr>
                        <div class="summary-row total-row">
                            <span class="summary-label">Status:</span>
                            <span class="summary-value">
                                @if($invoice->isFullyPaid())
                                    <span class="badge bg-success">Fully Paid</span>
                                @elseif($invoice->isOverdue())
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-warning">Pending Payment</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Actions -->
            @if($invoice->status === 'pending' && !$invoice->isFullyPaid())
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#markPaymentModal">
                        <i class="fas fa-check me-2"></i>Mark as Paid
                    </button>
                    <a href="{{ route('finance-admin.student.show', $invoice->student_id) }}" class="btn btn-info w-100">
                        <i class="fas fa-user me-2"></i>View Student
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment History -->
    @if($invoice->payments->count() > 0)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Payment History
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : 'N/A' }}</td>
                            <td>RM {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                            <td>
                                @if($payment->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->receipt)
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="View Receipt">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Mark Payment Modal -->
<div class="modal fade" id="markPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('finance-admin.invoice.mark-paid', $invoice->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Payment as Complete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="online_banking">Online Banking</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID (Optional)</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                    </div>
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Payment Notes (Optional)</label>
                        <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.invoice-details-page .info-group {
    margin-bottom: 1rem;
}

.invoice-details-page .info-label {
    font-weight: 600;
    color: #6c757d;
    display: block;
    margin-bottom: 0.25rem;
}

.invoice-details-page .info-value {
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
