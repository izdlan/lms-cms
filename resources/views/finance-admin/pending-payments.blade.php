@extends('layouts.finance-admin')

@section('title', 'Pending Payments | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
                    <div class="page-header">
                        <h1 class="page-title">Pending Payments</h1>
                        <p class="page-subtitle">Students with outstanding payments</p>
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

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $studentsWithPendingPayments->count() }}</h3>
                                    <p>Students with Pending Payments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon danger">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $studentsWithPendingPayments->where('overdue_days', '>', 30)->count() }}</h3>
                                    <p>Overdue 30+ Days</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon info">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>RM {{ number_format($studentsWithPendingPayments->sum('pending_amount'), 2) }}</h3>
                                    <p>Total Pending Amount</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon success">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $studentsWithPendingPayments->where('overdue_days', '<=', 7)->count() }}</h3>
                                    <p>Due Within 7 Days</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Students with Pending Payments</h5>
                        </div>
                        <div class="card-body">
                            @if($studentsWithPendingPayments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>IC Number</th>
                                                <th>Email</th>
                                                <th>Student ID</th>
                                                <th>Pending Amount</th>
                                                <th>Overdue Days</th>
                                                <th>Last Payment</th>
                                                <th>Priority</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($studentsWithPendingPayments as $data)
                                                @php
                                                    $student = $data['student'];
                                                    $pendingAmount = $data['pending_amount'];
                                                    $overdueDays = $data['overdue_days'];
                                                    $lastPayment = $data['last_payment'];
                                                    
                                                    $priority = 'low';
                                                    if ($overdueDays > 30) $priority = 'high';
                                                    elseif ($overdueDays > 14) $priority = 'medium';
                                                @endphp
                                                <tr class="{{ $priority === 'high' ? 'table-danger' : ($priority === 'medium' ? 'table-warning' : '') }}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($student->profile_picture)
                                                                <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                                                                     alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                                            @else
                                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                                     style="width: 32px; height: 32px;">
                                                                    <i class="fas fa-user text-white"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div class="fw-bold">{{ $student->name }}</div>
                                                                @if($student->is_blocked)
                                                                    <small class="text-danger">
                                                                        <i class="fas fa-ban me-1"></i>Blocked
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $student->ic }}</td>
                                                    <td>{{ $student->email }}</td>
                                                    <td>{{ $student->student_id ?? '-' }}</td>
                                                    <td class="text-end">
                                                        <strong class="text-danger">RM {{ number_format($pendingAmount, 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($overdueDays > 0)
                                                            <span class="badge bg-{{ $overdueDays > 30 ? 'danger' : ($overdueDays > 14 ? 'warning' : 'info') }}">
                                                                {{ $overdueDays }} days
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">Not overdue</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ optional($lastPayment)->format('M d, Y') ?? '—' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if($priority === 'high')
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>High
                                                            </span>
                                                        @elseif($priority === 'medium')
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Medium
                                                            </span>
                                                        @else
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-info-circle me-1"></i>Low
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('finance-admin.student.show', $student->id) }}" 
                                                               class="btn btn-sm btn-primary" title="View Student">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('finance-admin.payment-history', $student->id) }}" 
                                                               class="btn btn-sm btn-info" title="Payment History">
                                                                <i class="fas fa-receipt"></i>
                                                            </a>
                                                            @if(!$student->is_blocked)
                                                                <button type="button" class="btn btn-sm btn-warning" 
                                                                        onclick="blockStudent({{ $student->id }})" title="Block Student">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            @endif
                                                            <a href="mailto:{{ $student->email }}" class="btn btn-sm btn-outline-primary" title="Send Email">
                                                                <i class="fas fa-envelope"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5>No Pending Payments</h5>
                                    <p class="text-muted">All students are up to date with their payments.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    @if($studentsWithPendingPayments->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-tasks me-2"></i>Bulk Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-warning w-100" onclick="bulkBlockOverdueStudents()">
                                        <i class="fas fa-ban me-2"></i>Block Overdue Students (30+ days)
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-info w-100" onclick="sendReminderEmails()">
                                        <i class="fas fa-envelope me-2"></i>Send Reminder Emails
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-success w-100" onclick="generatePaymentReport()">
                                        <i class="fas fa-file-excel me-2"></i>Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
                    <p>Are you sure you want to block this student for non-payment?</p>
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

function bulkBlockOverdueStudents() {
    if (confirm('Are you sure you want to block all students with payments overdue for 30+ days?')) {
        // This would typically make an AJAX call to block multiple students
        alert('Bulk block functionality would be implemented here.');
    }
}

function sendReminderEmails() {
    if (confirm('Send reminder emails to all students with pending payments?')) {
        // This would typically make an AJAX call to send reminder emails
        alert('Reminder email functionality would be implemented here.');
    }
}

function generatePaymentReport() {
    // This would typically generate and download a report
    alert('Payment report generation would be implemented here.');
}
</script>
@endpush

@push('styles')
<style>
.finance-admin-dashboard {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Fix table column widths for pending payments */
.table th:nth-child(1),
.table td:nth-child(1) {
    width: 30% !important; /* Student column */
    min-width: 200px !important;
}

.table th:nth-child(2),
.table td:nth-child(2) {
    width: 15% !important; /* IC Number column */
    min-width: 120px !important;
}

.table th:nth-child(3),
.table td:nth-child(3) {
    width: 25% !important; /* Email column */
    min-width: 200px !important;
    word-break: break-all !important;
}

.table th:nth-child(4),
.table td:nth-child(4) {
    width: 15% !important; /* Amount column */
    min-width: 100px !important;
}

.table th:nth-child(5),
.table td:nth-child(5) {
    width: 15% !important; /* Actions column */
    min-width: 120px !important;
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
    background: #e3f2fd;
    color: #1976d2;
    font-size: 1.5rem;
}

.stat-icon.warning {
    background: #fff3e0;
    color: #f57c00;
}

.stat-icon.danger {
    background: #ffebee;
    color: #c62828;
}

.stat-icon.info {
    background: #e3f2fd;
    color: #1976d2;
}

.stat-icon.success {
    background: #e8f5e8;
    color: #2e7d32;
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

.table-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}
</style>
@endpush
