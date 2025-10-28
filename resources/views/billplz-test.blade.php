<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Billplz Sandbox Test - LMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        .status {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: none;
        }

        .status.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            display: block;
        }

        .status.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            display: block;
        }

        .status.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            display: block;
        }

        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin: 5px;
        }

        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            border-left: 4px solid #667eea;
        }

        .info-box h3 {
            margin-bottom: 15px;
            color: #667eea;
        }

        .info-box ul {
            list-style: none;
            padding-left: 0;
        }

        .info-box li {
            padding: 8px 0;
            color: #666;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .payment-url {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            word-break: break-all;
        }

        .payment-url a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .payment-url a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Billplz Sandbox Test</h1>
            <p>Test your Billplz API v4 integration</p>
        </div>

        <div id="status" class="status"></div>

        <div class="button-group">
            <button class="btn" onclick="testConnection()">
                🔌 Test Connection
            </button>
            <button class="btn btn-secondary" onclick="testCollection()">
                📦 Test Collection
            </button>
            <button class="btn btn-secondary" onclick="getPaymentGateways()">
                💳 Get Payment Gateways
            </button>
        </div>

        <div class="info-box">
            <h3>Create Test Payment</h3>
            <form id="paymentForm">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="test@example.com" required>
                </div>

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="Test Student" required>
                </div>

                <div class="form-group">
                    <label>Mobile (Optional)</label>
                    <input type="text" name="mobile" value="0123456789">
                </div>

                <div class="form-group">
                    <label>Amount (MYR)</label>
                    <input type="number" name="amount" value="10.00" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" value="Test Payment from Sandbox">
                </div>

                <button type="submit" class="btn">
                    💰 Create Test Payment
                </button>
            </form>
        </div>

        <div class="info-box">
            <h3>📝 Test Cards (Sandbox)</h3>
            <ul>
                <li>✅ <strong>Successful Payment:</strong> 4000 0000 0000 0002</li>
                <li>❌ <strong>Failed Payment:</strong> 4000 0000 0000 0119</li>
                <li>⏰ <strong>Expired Card:</strong> 4000 0000 0000 0069</li>
            </ul>
            <p style="margin-top: 15px; color: #666; font-size: 0.9rem;">
                Use these cards to test different payment scenarios. Any expiration date and CVV will work.
            </p>
        </div>
    </div>

    <script>
        // Set up CSRF token for all AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function showStatus(message, type = 'info') {
            const statusDiv = document.getElementById('status');
            statusDiv.textContent = message;
            statusDiv.className = `status ${type}`;
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 5000);
        }

        async function testConnection() {
            showStatus('Testing connection...', 'info');
            
            try {
                const response = await fetch('/billplz-test/test-connection', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    showStatus(`✅ Connected! Webhook rank: ${data.data.rank} | API: ${data.data.api_version} | Sandbox: ${data.data.sandbox}`, 'success');
                } else {
                    showStatus(`❌ Connection failed: ${data.message}`, 'error');
                }
            } catch (error) {
                showStatus(`❌ Error: ${error.message}`, 'error');
            }
        }

        async function testCollection() {
            showStatus('Testing collection...', 'info');
            
            try {
                const response = await fetch('/billplz-test/test-collection', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    showStatus(`✅ Collection found: ${data.data.title} | ID: ${data.data.id}`, 'success');
                } else {
                    showStatus(`❌ Collection test failed: ${data.message}`, 'error');
                }
            } catch (error) {
                showStatus(`❌ Error: ${error.message}`, 'error');
            }
        }

        async function getPaymentGateways() {
            showStatus('Fetching payment gateways...', 'info');
            
            try {
                const response = await fetch('/billplz-test/payment-gateways', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    const gateways = data.data.payment_gateways;
                    const activeGateways = gateways.filter(g => g.active);
                    showStatus(`✅ Found ${activeGateways.length} active payment gateways`, 'success');
                } else {
                    showStatus(`❌ Failed to get payment gateways: ${data.error}`, 'error');
                }
            } catch (error) {
                showStatus(`❌ Error: ${error.message}`, 'error');
            }
        }

        // Handle payment form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            showStatus('Creating payment...', 'info');
            
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            try {
                const response = await fetch('/billplz-test/create-payment', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    showStatus(`✅ Payment created successfully!`, 'success');
                    
                    // Show payment URL
                    const urlDiv = document.createElement('div');
                    urlDiv.className = 'payment-url';
                    urlDiv.innerHTML = `<strong>Payment URL:</strong><br><a href="${result.payment_url}" target="_blank">${result.payment_url}</a>`;
                    document.querySelector('.info-box').appendChild(urlDiv);
                    
                    // Open in new window
                    setTimeout(() => {
                        window.open(result.payment_url, '_blank');
                    }, 1000);
                } else {
                    showStatus(`❌ Payment creation failed: ${result.message}`, 'error');
                }
            } catch (error) {
                showStatus(`❌ Error: ${error.message}`, 'error');
            }
        });
    </script>
</body>
</html>

