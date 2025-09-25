@extends('layouts.app')

@section('title', 'Payment - Student Bills')

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
                        <h1 class="page-title">Payment</h1>
                        <p class="page-subtitle">Complete your bill payment securely</p>
                    </div>

                    <!-- Bill Information -->
                    <div class="bill-info-section mb-4">
                        <div class="bill-card">
                            <div class="bill-header">
                                <h3>Bill Details</h3>
                            </div>
                            <div class="bill-content">
                                <div class="bill-details">
                                    <div class="detail-row">
                                        <span class="detail-label">Bill Number:</span>
                                        <span class="detail-value">{{ request('bill_number', '2022495772013') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Bill Date:</span>
                                        <span class="detail-value">{{ request('bill_date', '12/9/2025') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Session:</span>
                                        <span class="detail-value">{{ request('session', '20254') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Bill Type:</span>
                                        <span class="detail-value">{{ request('bill_type', 'Tuition Fee') }}</span>
                                    </div>
                                    <div class="detail-row total-row">
                                        <span class="detail-label">Amount Due:</span>
                                        <span class="detail-value amount">RM {{ request('amount', '590.00') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div class="payment-form-section">
                        <div class="payment-card">
                            <div class="payment-header">
                                <h3>Payment Information</h3>
                            </div>
                            <div class="payment-content">
                                <form class="payment-form">
                                    <div class="form-group">
                                        <label for="payment-method">Payment Method</label>
                                        <select class="form-control" id="payment-method" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="online-banking">Online Banking</option>
                                            <option value="credit-card">Credit Card</option>
                                            <option value="debit-card">Debit Card</option>
                                            <option value="e-wallet">E-Wallet</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="card-number">Card Number / Account Number</label>
                                        <input type="text" class="form-control" id="card-number" placeholder="Enter card or account number" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="expiry-date">Expiry Date</label>
                                                <input type="text" class="form-control" id="expiry-date" placeholder="MM/YY" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cvv">CVV</label>
                                                <input type="text" class="form-control" id="cvv" placeholder="123" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="cardholder-name">Cardholder Name</label>
                                        <input type="text" class="form-control" id="cardholder-name" placeholder="Enter cardholder name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="billing-address">Billing Address</label>
                                        <textarea class="form-control" id="billing-address" rows="3" placeholder="Enter billing address" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email for Receipt</label>
                                        <input type="email" class="form-control" id="email" value="{{ auth('student')->user()->email ?? 'student@example.com' }}" required>
                                    </div>

                                    <div class="payment-actions">
                                        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Pay Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="security-notice mt-4">
                        <div class="notice-box">
                            <i class="fas fa-shield-alt"></i>
                            <div class="notice-content">
                                <h5>Secure Payment</h5>
                                <p>Your payment information is encrypted and secure. We use industry-standard SSL encryption to protect your data.</p>
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

.bill-info-section,
.payment-form-section {
    margin-bottom: 2rem !important;
}

.bill-card,
.payment-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    border: none;
    margin-bottom: 1.5rem;
}

.bill-header,
.payment-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
    font-weight: 600;
    font-size: 1.2rem;
    color: #2d3748;
    display: flex;
    align-items: center;
}

.bill-header h3,
.payment-header h3 {
    margin: 0;
    color: #2d3748;
    font-weight: bold;
}

.bill-content,
.payment-content {
    padding: 2rem;
}

.bill-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row.total-row {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 2px solid #6c757d;
    margin-top: 0.5rem;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    font-size: 1rem;
}

.detail-value {
    color: #212529;
    font-weight: 500;
}

.detail-value.amount {
    font-size: 1.3rem;
    font-weight: 700;
    color: #6c757d;
}

.payment-form {
    max-width: 600px;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #6c757d;
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
}

.payment-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
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
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-primary:hover {
    background: #5a6268;
    border-color: #5a6268;
    color: white;
}

.security-notice {
    margin-top: 2rem;
}

.notice-box {
    background: white;
    border: 2px solid #20c997;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.notice-box i {
    font-size: 2rem;
    color: #20c997;
}

.notice-content h5 {
    color: #20c997;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
}

.notice-content p {
    color: #495057;
    margin: 0;
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .main-content {
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .payment-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
