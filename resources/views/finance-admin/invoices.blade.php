@extends('layouts.finance-admin')

@section('title', 'Invoices | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Invoices Management</h1>
                <p class="page-subtitle">View and manage all invoices</p>
            </div>
            <div>
                <a href="{{ route('finance-admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('finance-admin.invoices') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Invoice number, student name, or email">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Invoices</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <a href="{{ route('finance-admin.invoices') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-file-invoice me-2"></i>Invoices List</h5>
            <span class="badge bg-primary fs-6">{{ $invoices->total() }} Total Invoices</span>
        </div>
        <div class="card-body">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i> Invoice #</th>
                                <th><i class="fas fa-user me-1"></i> Student</th>
                                <th><i class="fas fa-tag me-1"></i> Bill Type</th>
                                <th><i class="fas fa-calendar me-1"></i> Session</th>
                                <th><i class="fas fa-dollar-sign me-1"></i> Amount</th>
                                <th><i class="fas fa-calendar-alt me-1"></i> Due Date</th>
                                <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                <th><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                                        <small class="text-muted">{{ optional($invoice->invoice_date)->format('M d, Y') ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(optional($invoice->user)->profile_picture)
                                                <img src="{{ asset('storage/' . $invoice->user->profile_picture) }}" 
                                                     alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ optional($invoice->user)->name ?? 'Unknown' }}</div>
                                                <small class="text-muted">{{ optional($invoice->user)->email ?? '—' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $invoice->bill_type }}</td>
                                    <td>{{ $invoice->session }}</td>
                                    <td class="fw-bold">RM {{ number_format($invoice->amount, 2) }}</td>
                                    <td>
                                        <div class="{{ $invoice->isOverdue() ? 'text-danger' : '' }}">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                        </div>
                                        @if($invoice->isOverdue())
                                            <small class="text-danger">
                                                {{ now()->diffInDays($invoice->due_date) }} days overdue
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->status === 'pending')
                                            @if($invoice->isOverdue())
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        @elseif($invoice->status === 'paid')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Paid
                                            </span>
                                        @elseif($invoice->status === 'cancelled')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>Cancelled
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('finance-admin.invoice.show', $invoice->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('finance-admin.invoice.pdf', $invoice->id) }}" 
                                               class="btn btn-sm btn-outline-success" title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown" title="More Actions">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('finance-admin.student.show', $invoice->student_id) }}">
                                                        <i class="fas fa-user me-2"></i>View Student
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('finance-admin.invoice.view-pdf', $invoice->id) }}" target="_blank">
                                                        <i class="fas fa-file-pdf me-2"></i>View PDF
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($invoices->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    <nav aria-label="Invoice pagination">
                        {{ $invoices->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5>No Invoices Found</h5>
                    <p class="text-muted">
                        @if(request('search') || request('status'))
                            No invoices match your search criteria.
                        @else
                            No invoices have been created yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
