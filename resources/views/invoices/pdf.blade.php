<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $bill->bill_number ?? optional($invoice)->invoice_number ?? 'N/A' }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        .invoice-details {
            padding: 30px;
        }
        
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .invoice-info-left, .invoice-info-right {
            flex: 1;
            min-width: 250px;
        }
        
        .info-section h3 {
            color: #667eea;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-bottom: 2px solid #667eea;
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
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            background: white;
        }
        
        .invoice-table th {
            background: #f8f9fa;
            color: #333;
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        
        .invoice-table td {
            padding: 15px;
            border: 1px solid #ddd;
        }
        
        .invoice-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        
        .total-row.final {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            border-top: 2px solid #667eea;
            margin-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-overdue {
            background: #f8d7da;
            color: #721c24;
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
            color: #667eea;
        }
        
        @media print {
            body { margin: 0; padding: 0; }
            .invoice-container { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <h1>OLYMPIA EDUCATION</h1>
            <p>Centre for Professional, Executive, Advanced & Continuing Education</p>
        </div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <!-- Invoice Information -->
            <div class="invoice-info">
                <div class="invoice-info-left">
                    <div class="info-section">
                        <h3>Invoice Details</h3>
                        <div class="info-row">
                            <span class="info-label">Invoice #:</span>
                            <span class="info-value">{{ $bill->bill_number ?? optional($invoice)->invoice_number ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ optional($bill->bill_date ?? optional($invoice)->invoice_date)->format('d F Y') ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Due Date:</span>
                            <span class="info-value">{{ optional($bill->due_date ?? optional($invoice)->due_date)->format('d F Y') ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $bill->status ?? optional($invoice)->status }}">
                                    {{ ucfirst($bill->status ?? optional($invoice)->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="invoice-info-right">
                    <div class="info-section">
                        <h3>Bill To</h3>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ optional($bill->user ?? optional($invoice)->user)->name ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ optional($bill->user ?? optional($invoice)->user)->email ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ optional($bill->user ?? optional($invoice)->user)->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">IC Number:</span>
                            <span class="info-value">{{ optional($bill->user ?? optional($invoice)->user)->ic ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Student ID:</span>
                            <span class="info-value">{{ optional($bill->user ?? optional($invoice)->user)->student_id ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Invoice Items Table -->
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Session</th>
                        <th>Bill Type</th>
                        <th>Amount (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $bill->description ?? optional($invoice)->description ?? $bill->bill_type ?? optional($invoice)->bill_type }}</td>
                        <td>{{ $bill->session ?? optional($invoice)->session }}</td>
                        <td>{{ $bill->bill_type ?? optional($invoice)->bill_type }}</td>
                        <td>{{ number_format($bill->amount ?? optional($invoice)->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Total Section -->
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>RM {{ number_format($bill->amount ?? optional($invoice)->amount, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Tax (0%):</span>
                    <span>RM 0.00</span>
                </div>
                <div class="total-row final">
                    <span>Total Amount:</span>
                    <span>RM {{ number_format($bill->amount ?? optional($invoice)->amount, 2) }}</span>
                </div>
            </div>
            
            <!-- Notes Section -->
            @if(($bill->notes ?? optional($invoice)->notes))
            <div class="notes-section">
                <h4>Notes</h4>
                <p>{{ $bill->notes ?? optional($invoice)->notes }}</p>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Payment Instructions:</strong></p>
            <p>Please make payment by the due date to avoid late fees.</p>
            <p>For payment inquiries, please contact the finance department.</p>
            <p>Generated on {{ now()->format('d F Y \a\t H:i') }}</p>
        </div>
    </div>
</body>
</html>
