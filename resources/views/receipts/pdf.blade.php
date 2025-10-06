<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .receipt-details {
            padding: 30px;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .receipt-info-left, .receipt-info-right {
            flex: 1;
            min-width: 250px;
        }
        
        .info-section h3 {
            color: #28a745;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #666;
        }
        
        .info-value {
            flex: 1;
        }
        
        .payment-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-row.final {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            border-top: 2px solid #28a745;
            margin-top: 10px;
            border-bottom: none;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            background: #d4edda;
            color: #155724;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        
        .notes-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .notes-section h4 {
            margin: 0 0 10px 0;
            color: #28a745;
        }
        
        @media print {
            body { margin: 0; padding: 0; }
            .receipt-container { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>OLYMPIA EDUCATION</h1>
            <p>Centre for Professional, Executive, Advanced & Continuing Education</p>
        </div>
        
        <!-- Receipt Details -->
        <div class="receipt-details">
            <!-- Receipt Information -->
            <div class="receipt-info">
                <div class="receipt-info-left">
                    <div class="info-section">
                        <h3>Receipt Details</h3>
                        <div class="info-row">
                            <span class="info-label">Receipt #:</span>
                            <span class="info-value">{{ $receipt->receipt_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Date:</span>
                            <span class="info-value">{{ $receipt->payment_date->format('d F Y H:i') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Method:</span>
                            <span class="info-value">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</span>
                        </div>
                        @if($receipt->payment->transaction_id)
                        <div class="info-row">
                            <span class="info-label">Transaction ID:</span>
                            <span class="info-value">{{ $receipt->payment->transaction_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="receipt-info-right">
                    <div class="info-section">
                        <h3>Student Information</h3>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $receipt->student->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $receipt->student->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $receipt->student->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">IC Number:</span>
                            <span class="info-value">{{ $receipt->student->ic ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Invoice Information -->
            <div class="info-section">
                <h3>Invoice Information</h3>
                <div class="info-row">
                    <span class="info-label">Invoice Number:</span>
                    <span class="info-value">{{ $receipt->invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Bill Type:</span>
                    <span class="info-value">{{ $receipt->invoice->bill_type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Session:</span>
                    <span class="info-value">{{ $receipt->invoice->session }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Invoice Date:</span>
                    <span class="info-value">{{ $receipt->invoice->invoice_date->format('d F Y') }}</span>
                </div>
                @if($receipt->invoice->description)
                <div class="info-row">
                    <span class="info-label">Description:</span>
                    <span class="info-value">{{ $receipt->invoice->description }}</span>
                </div>
                @endif
            </div>
            
            <!-- Payment Summary -->
            <div class="payment-summary">
                <h4 style="margin-top: 0; color: #28a745;">Payment Summary</h4>
                <div class="summary-row">
                    <span>Amount Paid:</span>
                    <span>RM {{ number_format($receipt->amount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Payment Method:</span>
                    <span>{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</span>
                </div>
                <div class="summary-row">
                    <span>Payment Date:</span>
                    <span>{{ $receipt->payment_date->format('d F Y H:i') }}</span>
                </div>
                <div class="summary-row final">
                    <span>Status:</span>
                    <span class="status-badge">Payment Completed</span>
                </div>
            </div>
            
            <!-- Notes Section -->
            @if($receipt->receipt_notes)
            <div class="notes-section">
                <h4>Payment Notes</h4>
                <p>{{ $receipt->receipt_notes }}</p>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your payment!</strong></p>
            <p>This receipt serves as proof of payment for the above invoice.</p>
            <p>For any inquiries, please contact the finance department.</p>
            <p>Generated on {{ now()->format('d F Y \a\t H:i') }}</p>
        </div>
    </div>
</body>
</html>
