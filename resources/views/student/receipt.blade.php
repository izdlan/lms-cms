@extends('layouts.app')

@section('title', 'Payment Receipt | Student | Olympia Education')

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
                        <p class="page-subtitle">Your payment has been successfully processed</p>
                    </div>

                    <!-- Receipt Card -->
                    <div class="receipt-section">
                        <div class="receipt-card">
                            <div class="receipt-header">
                                <div class="receipt-title">
                                    <h2>Payment Receipt</h2>
                                    <div class="receipt-status">
                                        <i class="fas fa-check-circle"></i>
                                        <span>PAID</span>
                                    </div>
                                </div>
                                <div class="receipt-number">
                                    Receipt #: {{ request('receipt_number', 'RCP-' . date('Ymd') . '-' . rand(1000, 9999)) }}
                                </div>
                            </div>

                            <div class="receipt-content">
                                <!-- Student Information -->
                                <div class="receipt-section-item">
                                    <h4>Student Information</h4>
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <span class="info-label">Student Name:</span>
                                            <span class="info-value">{{ auth('student')->user()->name ?? '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Student ID:</span>
                                            <span class="info-value">{{ auth('student')->user()->student_id ?? '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Email:</span>
                                            <span class="info-value">{{ auth('student')->user()->email ?? '-' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Program:</span>
                                            <span class="info-value">{{ auth('student')->user()->program ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details -->
                                <div class="receipt-section-item">
                                    <h4>Payment Details</h4>
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <span class="info-label">Bill Number:</span>
                                            <span class="info-value">{{ request('bill_number', '2022495772012') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Bill Date:</span>
                                            <span class="info-value">{{ request('bill_date', '10/5/2025') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Session:</span>
                                            <span class="info-value">{{ request('session', '20252') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Bill Type:</span>
                                            <span class="info-value">{{ request('bill_type', 'EET Fee') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Payment Method:</span>
                                            <span class="info-value">{{ request('payment_method', 'Online Banking') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Transaction ID:</span>
                                            <span class="info-value">{{ request('transaction_id', 'TXN-' . date('Ymd') . '-' . rand(100000, 999999)) }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Payment Date:</span>
                                            <span class="info-value">{{ request('payment_date', date('d/m/Y H:i:s')) }}</span>
                                        </div>
                                        <div class="info-item total-item">
                                            <span class="info-label">Amount Paid:</span>
                                            <span class="info-value amount">RM {{ request('amount', '30.00') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Summary -->
                                <div class="payment-summary">
                                    <div class="summary-row">
                                        <span>Subtotal:</span>
                                        <span>RM {{ request('amount', '30.00') }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Processing Fee:</span>
                                        <span>RM 0.00</span>
                                    </div>
                                    <div class="summary-row total-row">
                                        <span>Total Paid:</span>
                                        <span>RM {{ request('amount', '30.00') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="receipt-footer">
                                <div class="footer-note">
                                    <p><strong>Thank you for your payment!</strong></p>
                                    <p>This receipt serves as proof of payment. Please keep this for your records.</p>
                                    <p>If you have any questions, please contact our support team.</p>
                                </div>
                                <div class="receipt-actions">
                                    <button class="btn btn-secondary" onclick="window.print()">
                                        <i class="fas fa-print"></i> Print Receipt
                                    </button>
                                    <button class="btn btn-primary" onclick="location.href='{{ route('student.bills') }}'">
                                        <i class="fas fa-arrow-left"></i> Back to Bills
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-info mt-4">
                        <div class="contact-card">
                            <h5>Need Help?</h5>
                            <div class="contact-details">
                                <p><i class="fas fa-envelope"></i> support@olympia-education.com</p>
                                <p><i class="fas fa-phone"></i> +60 12-345 6789</p>
                                <p><i class="fas fa-clock"></i> Mon-Fri: 9:00 AM - 5:00 PM</p>
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
    padding: 2rem !important;
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

.page-header {
    margin-bottom: 2rem !important;
    padding: 0 !important;
}

.page-title {
    color: #2d3748;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    color: #718096;
    font-size: 1.1rem;
    margin: 0 0 2rem 0;
}

.receipt-section {
    margin-bottom: 2rem !important;
}

.receipt-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    max-width: 800px;
    margin: 0 auto;
    border: none;
}

.receipt-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
    color: #2d3748;
}

.receipt-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.receipt-title h2 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: bold;
    color: #2d3748;
}

.receipt-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #20c997;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.receipt-status i {
    font-size: 1rem;
}

.receipt-number {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 500;
}

.receipt-content {
    padding: 2rem;
}

.receipt-section-item {
    margin-bottom: 2rem;
}

.receipt-section-item h4 {
    color: #2d3748;
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e9ecef;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item.total-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 2px solid #20c997;
    margin-top: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #495057;
}

.info-value {
    color: #212529;
    font-weight: 500;
}

.info-value.amount {
    font-size: 1.3rem;
    font-weight: 700;
    color: #20c997;
}

.payment-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    font-size: 1rem;
}

.summary-row.total-row {
    font-size: 1.2rem;
    font-weight: 700;
    color: #20c997;
    border-top: 2px solid #20c997;
    padding-top: 1rem;
    margin-top: 0.5rem;
}

.receipt-footer {
    background: #f8f9fa;
    padding: 2rem;
    border-top: 1px solid #e9ecef;
}

.footer-note {
    margin-bottom: 1.5rem;
    text-align: center;
}

.footer-note p {
    margin: 0.5rem 0;
    color: #495057;
}

.footer-note p:first-child {
    color: #20c997;
    font-size: 1.1rem;
    font-weight: 600;
}

.receipt-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #5a6268;
    border-color: #5a6268;
    color: white;
}

.btn-primary {
    background: #20c997;
    color: white;
    border-color: #20c997;
}

.btn-primary:hover {
    background: #1ba085;
    border-color: #1ba085;
    color: white;
}

.contact-info {
    margin-top: 2rem;
}

.contact-card {
    background: white;
    border: 2px solid #6c757d;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.contact-card h5 {
    color: #6c757d;
    font-weight: 700;
    margin-bottom: 1rem;
}

.contact-details p {
    margin: 0.5rem 0;
    color: #495057;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.contact-details i {
    color: #6c757d;
    width: 20px;
}

@media (max-width: 768px) {
    .main-content {
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .receipt-title {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .receipt-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media print {
    .student-dashboard .col-md-3 {
        display: none;
    }
    
    .receipt-actions {
        display: none;
    }
    
    .contact-info {
        display: none;
    }
    
    .main-content {
        background: white !important;
    }
}
</style>
@endsection
