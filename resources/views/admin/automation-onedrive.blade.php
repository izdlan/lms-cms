@extends('layouts.admin')

@section('title', 'OneDrive Automation')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>OneDrive Auto Import</h1>
                    <p>Automatically import students from OneDrive Excel files</p>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cloud-upload-alt"></i> OneDrive Auto Import
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Status Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="status-card">
                                <div class="status-icon">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="status-content">
                                    <h6 class="status-label">Automation Status</h6>
                                    <h4 class="status-value" id="autoStatus">Not Set Up</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="status-card">
                                <div class="status-icon success">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="status-content">
                                    <h6 class="status-label">Last Import</h6>
                                    <h4 class="status-value" id="lastImport">Loading...</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Control Panel -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="control-panel">
                                <h5 class="panel-title">
                                    <i class="fas fa-cogs"></i> Control Panel
                                </h5>
                                <div class="control-buttons">
                                    <button type="button" class="btn-modern btn-primary" onclick="runManualImport()">
                                        <i class="fas fa-play"></i>
                                        <span>Run Import Now</span>
                                    </button>
                                    <button type="button" class="btn-modern btn-info" onclick="checkStatus()">
                                        <i class="fas fa-refresh"></i>
                                        <span>Check Status</span>
                                    </button>
                                    <button type="button" class="btn-modern btn-warning" onclick="viewLogs()">
                                        <i class="fas fa-file-alt"></i>
                                        <span>View Logs</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Import Results</h4>
                                </div>
                                <div class="card-body">
                                    <div id="importResults" class="alert alert-info">
                                        Click "Run Import Now" to see results
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Automation Setup -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-card automation-card">
                                <div class="card-header-modern">
                                    <div class="header-icon">
                                        <i class="fas fa-magic"></i>
                                    </div>
                                    <div class="header-content">
                                        <h5>Automation Setup</h5>
                                        <p>Enable automatic imports every 5 minutes</p>
                                    </div>
                                </div>
                                <div class="card-body-modern">
                                    <div class="action-grid">
                                        <button type="button" class="action-btn primary" onclick="testImport()">
                                            <i class="fas fa-play"></i>
                                            <span>Test Import</span>
                                        </button>
                                        <button type="button" class="action-btn success" onclick="createAutomationTask()">
                                            <i class="fas fa-cog"></i>
                                            <span>Enable Auto Import</span>
                                        </button>
                                        <button type="button" class="action-btn info" onclick="checkAutomationStatus()">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Check Status</span>
                                        </button>
                                        <button type="button" class="action-btn danger" onclick="deleteAutomationTask()">
                                            <i class="fas fa-trash"></i>
                                            <span>Disable Auto Import</span>
                                        </button>
                                        <button type="button" class="action-btn warning" onclick="createSimpleAutomation()">
                                            <i class="fas fa-file-code"></i>
                                            <span>No Admin Setup</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card manual-card">
                                <div class="card-header-modern">
                                    <div class="header-icon">
                                        <i class="fas fa-hand-paper"></i>
                                    </div>
                                    <div class="header-content">
                                        <h5>Manual Controls</h5>
                                        <p>Run imports manually when needed</p>
                                    </div>
                                </div>
                                <div class="card-body-modern">
                                    <div class="action-grid">
                                        <button type="button" class="action-btn primary" onclick="runManualImport()">
                                            <i class="fas fa-play"></i>
                                            <span>Run Import Now</span>
                                        </button>
                                        <button type="button" class="action-btn info" onclick="checkStatus()">
                                            <i class="fas fa-refresh"></i>
                                            <span>Check Status</span>
                                        </button>
                                        <button type="button" class="action-btn warning" onclick="viewLogs()">
                                            <i class="fas fa-file-alt"></i>
                                            <span>View Logs</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Panel -->
                    <div id="setupResults" class="results-panel" style="display: none;">
                        <div class="results-content">
                            <div class="results-header">
                                <i class="fas fa-info-circle"></i>
                                <h6>Setup Results</h6>
                            </div>
                            <div id="setupOutput"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function runManualImport() {
    const button = event && event.target ? event.target : null;
    const originalText = button ? button.innerHTML : '';
    
    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
        button.disabled = true;
    }
    
    fetch('/admin/students/onedrive-import', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('importResults').innerHTML = `
                <div class="alert alert-success">
                    <h5>✅ Import Completed Successfully!</h5>
                    <p><strong>Created:</strong> ${data.created} students</p>
                    <p><strong>Updated:</strong> ${data.updated} students</p>
                    <p><strong>Errors:</strong> ${data.errors}</p>
                    <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                </div>
            `;
            
            // Update last import time
            document.getElementById('lastImport').textContent = new Date().toLocaleString();
        } else {
            document.getElementById('importResults').innerHTML = `
                <div class="alert alert-danger">
                    <h5>❌ Import Failed</h5>
                    <p>${data.error || 'Unknown error occurred'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('importResults').innerHTML = `
            <div class="alert alert-danger">
                <h5>❌ Error</h5>
                <p>${error.message}</p>
            </div>
        `;
    })
    .finally(() => {
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function checkStatus() {
    document.getElementById('autoStatus').textContent = 'Checking...';
    
    // Simulate status check (you can implement a real status check endpoint)
    setTimeout(() => {
        document.getElementById('autoStatus').textContent = 'Active (Every 5 minutes)';
    }, 1000);
}

function viewLogs() {
    // Open logs in a new window or modal
    window.open('/admin/logs/auto-import', '_blank');
}

// Web-based automation setup functions
function testImport() {
    const button = event && event.target ? event.target : null;
    const originalText = button ? button.innerHTML : '';
    
    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
        button.disabled = true;
    }
    
    fetch('/admin/automation-setup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ action: 'test_import' })
    })
    .then(response => response.json())
    .then(data => {
        showSetupResults(data);
    })
    .catch(error => {
        showSetupResults({
            success: false,
            message: 'Error: ' + error.message
        });
    })
    .finally(() => {
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function createAutomationTask() {
    const button = event && event.target ? event.target : null;
    const originalText = button ? button.innerHTML : '';
    
    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        button.disabled = true;
    }
    
    fetch('/admin/automation-setup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ action: 'create_task' })
    })
    .then(response => response.json())
    .then(data => {
        showSetupResults(data);
        if (data.success) {
            // Update status after successful creation
            setTimeout(checkAutomationStatus, 1000);
        }
    })
    .catch(error => {
        showSetupResults({
            success: false,
            message: 'Error: ' + error.message
        });
    })
    .finally(() => {
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function checkAutomationStatus() {
    const button = event && event.target ? event.target : null;
    const originalText = button ? button.innerHTML : '';
    
    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
        button.disabled = true;
    }
    
    fetch('/admin/automation-setup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ action: 'check_status' })
    })
    .then(response => response.json())
    .then(data => {
        showSetupResults(data);
        // Update the status display
        const statusElement = document.getElementById('autoStatus');
        if (statusElement) {
            if (data.is_active) {
                statusElement.textContent = 'Active (Every 5 minutes)';
                statusElement.style.color = '#4caf50';
            } else {
                statusElement.textContent = 'Not Set Up';
                statusElement.style.color = '#ff9800';
            }
        }
    })
    .catch(error => {
        showSetupResults({
            success: false,
            message: 'Error: ' + error.message
        });
    })
    .finally(() => {
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function deleteAutomationTask() {
    if (!confirm('Are you sure you want to disable automatic imports?')) {
        return;
    }
    
    const button = event && event.target ? event.target : null;
    const originalText = button ? button.innerHTML : '';
    
    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        button.disabled = true;
    }
    
    fetch('/admin/automation-setup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ action: 'delete_task' })
    })
    .then(response => response.json())
    .then(data => {
        showSetupResults(data);
        if (data.success) {
            // Update status after successful deletion
            document.getElementById('autoStatus').textContent = 'Inactive';
        }
    })
    .catch(error => {
        showSetupResults({
            success: false,
            message: 'Error: ' + error.message
        });
    })
    .finally(() => {
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function showSetupResults(data) {
    const resultsDiv = document.getElementById('setupResults');
    const outputDiv = document.getElementById('setupOutput');
    
    let alertClass = data.success ? 'alert-success' : 'alert-danger';
    let icon = data.success ? '✅' : '❌';
    
    let suggestionHtml = '';
    if (data.suggestion) {
        suggestionHtml = `
            <div class="alert-suggestion">
                <strong>💡 Suggestion:</strong> ${data.suggestion}
            </div>
        `;
    }
    
    outputDiv.innerHTML = `
        <div class="alert ${alertClass} modern-alert">
            <div class="alert-icon">
                <i class="fas ${data.success ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
            </div>
            <div class="alert-content">
                <h6 class="alert-title">${data.message}</h6>
                ${data.output ? '<pre class="alert-output">' + data.output + '</pre>' : ''}
                ${suggestionHtml}
            </div>
        </div>
    `;
    
    resultsDiv.style.display = 'block';
    
    // Auto-hide after 15 seconds for error messages with suggestions
    const hideDelay = data.suggestion ? 15000 : 10000;
    setTimeout(() => {
        resultsDiv.style.display = 'none';
    }, hideDelay);
}

function createSimpleAutomation() {
    const button = event && event.target ? event.target : null;
    const originalText = button ? button.innerHTML : '';
    
    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Creating...</span>';
        button.disabled = true;
    }
    
    fetch('/admin/automation-setup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ action: 'create_simple_automation' })
    })
    .then(response => response.json())
    .then(data => {
        showSetupResults(data);
    })
    .catch(error => {
        showSetupResults({
            success: false,
            message: 'Error: ' + error.message
        });
    })
    .finally(() => {
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

// Initialize Feather icons safely - DISABLED to prevent errors
document.addEventListener('DOMContentLoaded', function() {
    // Skip Feather icons initialization to prevent errors
    console.log('Feather icons initialization disabled to prevent errors');
});

// Auto-refresh status every 30 seconds
setInterval(() => {
    checkAutomationStatus();
}, 30000);

// Initial status check
document.addEventListener('DOMContentLoaded', function() {
    // Set initial status
    const statusElement = document.getElementById('autoStatus');
    if (statusElement) {
        statusElement.textContent = 'Not Set Up';
        statusElement.style.color = '#ff9800';
    }
    
    // Check status after a short delay (but don't show error if not set up)
    setTimeout(() => {
        checkAutomationStatusQuietly();
    }, 500);
});

// Quiet status check (doesn't show error messages)
function checkAutomationStatusQuietly() {
    fetch('/admin/automation-setup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ action: 'check_status' })
    })
    .then(response => response.json())
    .then(data => {
        // Update status display without showing error message
        const statusElement = document.getElementById('autoStatus');
        if (statusElement) {
            if (data.is_active) {
                statusElement.textContent = 'Active (Every 5 minutes)';
                statusElement.style.color = '#4caf50';
            } else {
                statusElement.textContent = 'Not Set Up';
                statusElement.style.color = '#ff9800';
            }
        }
    })
    .catch(error => {
        // Silently handle errors for initial check
        console.log('Status check failed:', error.message);
    });
}
</script>
@endsection

@push('styles')
<style>
/* Modern OneDrive Import Interface Styles */

/* Status Cards */
.status-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 24px;
    color: white;
    display: flex;
    align-items: center;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.status-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
}

.status-card .status-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    font-size: 24px;
}

.status-card .status-icon.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.status-content {
    flex: 1;
}

.status-label {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.9;
    margin: 0 0 8px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-value {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

/* Control Panel */
.control-panel {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e8ecf0;
}

.panel-title {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.panel-title i {
    margin-right: 12px;
    color: #667eea;
}

.control-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Modern Buttons */
.btn-modern {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-modern i {
    margin-right: 8px;
    font-size: 16px;
}

.btn-modern.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-modern.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-modern.btn-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
}

.btn-modern.btn-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
}

.btn-modern.btn-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
}

.btn-modern.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
}

/* Feature Cards */
.feature-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    border: 1px solid #e8ecf0;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
}

.automation-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.manual-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.card-header-modern {
    padding: 24px;
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    font-size: 20px;
}

.header-content h5 {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 4px 0;
}

.header-content p {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
}

.card-body-modern {
    padding: 24px;
}

.action-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    min-height: 200px;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 12px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    color: white;
    text-decoration: none;
}

.action-btn i {
    font-size: 20px;
    margin-bottom: 8px;
}

.action-btn span {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
    line-height: 1.2;
}

/* Ensure all buttons are visible */
.action-btn {
    min-height: 80px;
    justify-content: center;
}

/* Fix for missing buttons */
.action-grid {
    grid-template-rows: repeat(3, 1fr);
}

/* Results Panel */
.results-panel {
    margin-top: 24px;
    animation: slideDown 0.3s ease;
}

.results-content {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e8ecf0;
}

.results-header {
    display: flex;
    align-items: center;
    margin-bottom: 16px;
    color: #667eea;
}

.results-header i {
    margin-right: 8px;
    font-size: 18px;
}

.results-header h6 {
    margin: 0;
    font-weight: 600;
    font-size: 16px;
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .status-card {
        padding: 16px;
        margin-bottom: 16px;
    }
    
    .status-card .status-icon {
        width: 50px;
        height: 50px;
        margin-right: 16px;
        font-size: 20px;
    }
    
    .status-value {
        font-size: 20px;
    }
    
    .control-buttons {
        flex-direction: column;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .feature-card {
        margin-bottom: 20px;
    }
}

/* Modern Alerts */
.modern-alert {
    display: flex;
    align-items: flex-start;
    padding: 16px;
    border-radius: 12px;
    border: none;
    margin: 0;
}

.alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
}

.alert-success .alert-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.alert-danger .alert-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: #2d3748;
}

.alert-output {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    font-size: 12px;
    color: #495057;
    margin: 0;
    white-space: pre-wrap;
    word-break: break-word;
}

.alert-suggestion {
    background: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 8px;
    padding: 12px;
    margin-top: 12px;
    font-size: 13px;
    color: #1976d2;
}

/* Loading States */
.btn-modern:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.btn-modern:disabled:hover {
    transform: none !important;
    box-shadow: none !important;
}
</style>
@endpush

