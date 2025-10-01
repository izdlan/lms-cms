@extends('layouts.app')

@section('title', 'Student Bills')

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
                        <h1 class="page-title">Student Bills</h1>
                    </div>

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
                                        <div class="info-value">{{ auth('student')->user()->ic_number ?? '-' }}</div>
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
                                    <th>Bill Number</th>
                                    <th>Bill Date</th>
                                    <th>Session</th>
                                    <th>Bill Type</th>
                                    <th>Amount (RM)</th>
                                    <th>Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="{{ route('student.payment') }}?bill_number=2022495772013&bill_date=12/9/2025&session=20254&bill_type=Tuition Fee&amount=590.00" class="bill-link">2022495772013</a></td>
                                    <td>12/9/2025</td>
                                    <td>20254</td>
                                    <td>Tuition Fee</td>
                                    <td class="amount">590.00</td>
                                    <td><span class="status pending">Pending</span></td>
                                </tr>
                                <tr>
                                    <td><a href="{{ route('student.receipt') }}?bill_number=2022495772012&bill_date=10/5/2025&session=20252&bill_type=EET Fee&amount=30.00&payment_method=Online Banking&payment_date=10/5/2025 14:30:00" class="bill-link">2022495772012</a></td>
                                    <td>10/5/2025</td>
                                    <td>20252</td>
                                    <td>EET Fee</td>
                                    <td class="amount">30.00</td>
                                    <td><span class="status paid">Paid</span></td>
                                </tr>
                                <tr>
                                    <td><a href="{{ route('student.receipt') }}?bill_number=2022495772011&bill_date=19/3/2025&session=20252&bill_type=Tuition Fee&amount=590.00&payment_method=Credit Card&payment_date=19/3/2025 09:15:00" class="bill-link">2022495772011</a></td>
                                    <td>19/3/2025</td>
                                    <td>20252</td>
                                    <td>Tuition Fee</td>
                                    <td class="amount">590.00</td>
                                    <td><span class="status paid">Paid</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
