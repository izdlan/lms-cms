@extends('layouts.app')

@section('title', 'Payment | Student | Olympia Education')

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

                    @if($bill)
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
                                            <span class="detail-value">{{ $bill->bill_number }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Bill Date:</span>
                                            <span class="detail-value">{{ $bill->bill_date->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Due Date:</span>
                                            <span class="detail-value">{{ $bill->due_date->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Session:</span>
                                            <span class="detail-value">{{ $bill->session }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Bill Type:</span>
                                            <span class="detail-value">{{ $bill->bill_type }}</span>
                                        </div>
                                        @if($bill->description)
                                        <div class="detail-row">
                                            <span class="detail-label">Description:</span>
                                            <span class="detail-value">{{ $bill->description }}</span>
                                        </div>
                                        @endif
                                        <div class="detail-row total-row">
                                            <span class="detail-label">Amount Due:</span>
                                            <span class="detail-value amount">{{ $bill->formatted_amount }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h5>No Bill Selected</h5>
                            <p>Please select a bill from your bills page to make a payment.</p>
                            <a href="{{ route('student.bills') }}" class="btn btn-primary">View My Bills</a>
                        </div>
                    @endif

                    @if($bill)
                        <!-- Payment Form -->
                        <div class="payment-form-section">
                            <div class="payment-card">
                                <div class="payment-header">
                                    <h3>Payment Information</h3>
                                </div>
                                <div class="payment-content">
                                    <div class="payment-methods">
                                        <h5>Available Payment Methods</h5>
                                        <div class="payment-options">
                                            <div class="payment-option">
                                                <i class="fas fa-university"></i>
                                                <span>Online Banking (FPX)</span>
                                            </div>
                                            <div class="payment-option">
                                                <i class="fas fa-credit-card"></i>
                                                <span>Credit/Debit Card</span>
                                            </div>
                                            <div class="payment-option">
                                                <i class="fas fa-mobile-alt"></i>
                                                <span>E-Wallet</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="payment-actions">
                                        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="payNowBtn" onclick="processPayment()">
                                            <i class="fas fa-credit-card"></i>
                                            Pay Now with Billplz
                                        </button>
                                    </div>
                                    
                                    <div id="paymentStatus" class="mt-3" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="fas fa-spinner fa-spin"></i>
                                            <span id="paymentStatusText">Processing payment...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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

.payment-options {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
    flex: 1;
    min-width: 150px;
    text-align: center;
}

.payment-option i {
    font-size: 1.5rem;
    color: #6c757d;
}

.payment-option span {
    font-weight: 600;
    color: #495057;
}
</style>

@if($bill)
<script>
function processPayment() {
    const payBtn = document.getElementById('payNowBtn');
    const statusDiv = document.getElementById('paymentStatus');
    const statusText = document.getElementById('paymentStatusText');
    
    // Disable button and show status
    payBtn.disabled = true;
    payBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    statusDiv.style.display = 'block';
    statusText.textContent = 'Creating payment with Billplz...';
    
    // Make AJAX request to process payment
    fetch('{{ route("student.payment.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            bill_id: {{ $bill->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusText.textContent = 'Redirecting to payment gateway...';
            // Redirect to Billplz payment page
            window.location.href = data.payment_url;
        } else {
            statusText.textContent = 'Error: ' + data.message;
            statusDiv.className = 'mt-3';
            statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</div>';
            
            // Re-enable button
            payBtn.disabled = false;
            payBtn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now with Billplz';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusText.textContent = 'An error occurred. Please try again.';
        statusDiv.className = 'mt-3';
        statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> An error occurred. Please try again.</div>';
        
        // Re-enable button
        payBtn.disabled = false;
        payBtn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now with Billplz';
    });
}
</script>
@endif
@endsection
