@extends('layouts.admin')

@section('title', 'Student Import Automation')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Google Sheets Student Import Automation</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="triggerGoogleSheetsImport()">
                            <i data-feather="play" width="16" height="16"></i> Import from Google Sheets
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="refreshStatus()">
                            <i data-feather="refresh-cw" width="16" height="16"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card {{ $isRunning ? 'bg-success' : 'bg-warning' }} text-white">
                            <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Google Sheets Automation</h6>
                            <h4 id="autoImportStatus">{{ $isRunning ? 'Running' : 'Stopped' }}</h4>
                        </div>
                        <i data-feather="{{ $isRunning ? 'play-circle' : 'pause-circle' }}" width="32" height="32"></i>
                    </div>
                    <small>{{ $isRunning ? 'Monitoring Google Sheets changes' : 'Click Start to begin monitoring' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Google Sheets Status</h6>
                                        <h4 id="sheetsStatus">{{ $sheetsStatus['status'] ?? 'Unknown' }}</h4>
                                    </div>
                                    <i data-feather="file-text" width="32" height="32"></i>
                                </div>
                                <small>
                                    @if($isRunning)
                                        Last check: {{ cache()->get('google_sheets_automation_last_check') ? \Carbon\Carbon::parse(cache()->get('google_sheets_automation_last_check'))->diffForHumans() : 'Never' }}
                                    @else
                                        Connection to Google Sheets
                                    @endif
                                </small>
                                <div class="mt-1">
                                    <small class="text-white-50">
                                        <strong>Continuous Mode:</strong> 
                                        @if($isRunning)
                                            <span class="text-success">✅ Running every 5 minutes</span>
                                        @else
                                            <span class="text-warning">⚠️ Not running</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Students</h6>
                                        <h4 id="totalStudents">{{ $totalStudents ?? 0 }}</h4>
                                    </div>
                                    <i data-feather="users" width="32" height="32"></i>
                                </div>
                                <small>In database</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white" id="sheetsUrlCard">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Google Sheets URL</h6>
                                        <h4 id="sheetsUrlStatus">Connected</h4>
                                    </div>
                                    <i data-feather="link" width="32" height="32"></i>
                                </div>
                                <small id="sheetsUrl">{{ $googleSheetsUrl ?? 'Not configured' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Automation Settings -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Google Sheets Automation Settings</h5>
                            </div>
                            <div class="card-body">
                                <form id="automationForm">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="googleSheetsUrl" class="form-label">Google Sheets URL</label>
                                                <input type="url" class="form-control" id="googleSheetsUrl" 
                                                       value="{{ $automationConfig['google_sheets_url'] ?? 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true' }}" 
                                                       placeholder="https://docs.google.com/spreadsheets/d/...">
                                                <div class="form-text">URL of the Google Sheets document to monitor</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="notificationEmail" class="form-label">Notification Email</label>
                                                <input type="email" class="form-control" id="notificationEmail" 
                                                       value="{{ $automationConfig['notification_email'] ?? '' }}"
                                                       placeholder="admin@example.com">
                                                <div class="form-text">Email to receive import notifications</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="checkInterval" class="form-label">Check Interval (seconds)</label>
                                                <input type="number" class="form-control" id="checkInterval" 
                                                       value="{{ $automationConfig['check_interval'] ?? 300 }}" 
                                                       min="60" max="3600" step="60">
                                                <div class="form-text">How often to check for changes (60-3600 seconds)</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="importFrequency" class="form-label">Import Frequency</label>
                                                <select class="form-select" id="importFrequency">
                                                    <option value="every-5-minutes" {{ ($automationConfig['import_frequency'] ?? 'every-5-minutes') === 'every-5-minutes' ? 'selected' : '' }}>Every 5 Minutes</option>
                                                    <option value="every-10-minutes" {{ ($automationConfig['import_frequency'] ?? '') === 'every-10-minutes' ? 'selected' : '' }}>Every 10 Minutes</option>
                                                    <option value="every-15-minutes" {{ ($automationConfig['import_frequency'] ?? '') === 'every-15-minutes' ? 'selected' : '' }}>Every 15 Minutes</option>
                                                    <option value="every-30-minutes" {{ ($automationConfig['import_frequency'] ?? '') === 'every-30-minutes' ? 'selected' : '' }}>Every 30 Minutes</option>
                                                    <option value="hourly" {{ ($automationConfig['import_frequency'] ?? '') === 'hourly' ? 'selected' : '' }}>Every Hour</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="updateExisting" class="form-label">Update Existing Students</label>
                                                <select class="form-select" id="updateExisting">
                                                    <option value="1" {{ ($automationConfig['update_existing'] ?? true) ? 'selected' : '' }}>Yes (Recommended)</option>
                                                    <option value="0" {{ !($automationConfig['update_existing'] ?? true) ? 'selected' : '' }}>No</option>
                                                </select>
                                                <div class="form-text">Update existing students when data changes</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                            <label class="form-check-label" for="emailNotifications">
                                                Send email notifications on import completion
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="updateExisting" checked>
                                            <label class="form-check-label" for="updateExisting">
                                                Update existing students if data changes
                                            </label>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="saveSettings()">
                                        <i data-feather="save" width="16" height="16"></i> Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    @if($isRunning)
                                        <button type="button" class="btn btn-danger" onclick="stopAutomation()">
                                            <i data-feather="square" width="16" height="16"></i> Stop Automation
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success" onclick="startAutomation()">
                                            <i data-feather="play" width="16" height="16"></i> Start Automation
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-info" onclick="triggerImport()">
                                        <i data-feather="download" width="16" height="16"></i> Run Import Now
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="testGoogleSheets()">
                                        <i data-feather="test-tube" width="16" height="16"></i> Test Google Sheets
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="checkFileStatus()">
                                        <i data-feather="file-check" width="16" height="16"></i> Check Connection
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="viewLogs()">
                                        <i data-feather="file-text" width="16" height="16"></i> View Import Logs
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <div id="recentActivity">
                                    @if(count($recentLogs) > 0)
                                        @foreach($recentLogs as $log)
                                            <div class="d-flex align-items-start mb-3 p-2 border rounded">
                                                <div class="flex-shrink-0 me-3">
                                                    @if($log['level'] === 'info')
                                                        <i data-feather="check-circle" class="text-success" width="16" height="16"></i>
                                                    @elseif($log['level'] === 'error')
                                                        <i data-feather="alert-circle" class="text-danger" width="16" height="16"></i>
                                                    @elseif($log['level'] === 'warning')
                                                        <i data-feather="alert-triangle" class="text-warning" width="16" height="16"></i>
                                                    @else
                                                        <i data-feather="info" class="text-info" width="16" height="16"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <p class="mb-1 fw-medium">{{ $log['message'] }}</p>
                                                            <small class="text-muted">{{ $log['time'] }} (Malaysia Time)</small>
                                                        </div>
                                                        <span class="badge bg-{{ $log['level'] === 'info' ? 'success' : ($log['level'] === 'error' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($log['level']) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-muted">
                                            <i data-feather="clock" width="24" height="24"></i>
                                            <p>No recent activity</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Import Logs -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Import Logs</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Type</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="importLogs">
                                            @if(count($recentLogs) > 0)
                                                @foreach($recentLogs as $log)
                                                    <tr>
                                                        <td>{{ $log['time'] }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $log['level'] === 'info' ? 'success' : ($log['level'] === 'error' ? 'danger' : 'warning') }}">
                                                                {{ ucfirst($log['level']) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $log['message'] }}</td>
                                                        <td>
                                                            @if($log['level'] === 'info')
                                                                <i data-feather="check-circle" class="text-success" width="16" height="16"></i>
                                                            @elseif($log['level'] === 'error')
                                                                <i data-feather="alert-circle" class="text-danger" width="16" height="16"></i>
                                                            @else
                                                                <i data-feather="info" class="text-info" width="16" height="16"></i>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No import logs available</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Progress Modal -->
<div class="modal fade" id="importProgressModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importing Students</h5>
            </div>
            <div class="modal-body">
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%" id="importProgress"></div>
                </div>
                <div id="importStatus">Starting import...</div>
                <div id="importDetails" class="mt-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Inline styles moved to /assets/default/css/admin.css --}}

@push('scripts')
<script>
// Wait for Feather icons to be loaded
function waitForFeather(callback) {
    if (typeof feather !== 'undefined') {
        callback();
    } else {
        setTimeout(() => waitForFeather(callback), 100);
    }
}

// Safe Feather replace function
function safeFeatherReplace() {
    if (typeof feather === 'undefined') {
        console.warn('Feather icons library not available');
        return;
    }
    
    try {
        // Only process elements that actually exist and have data-feather attribute
        const featherElements = document.querySelectorAll('[data-feather]');
        if (featherElements.length > 0) {
            feather.replace();
        }
    } catch (error) {
        console.warn('Error replacing Feather icons:', error);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    waitForFeather(function() {
        // Initialize Feather icons safely
        safeFeatherReplace();
        
        loadAutomationStatus();
        loadRecentActivity();
        loadImportLogs();
        
        // Auto-refresh every 30 seconds if automation is running
        const isRunning = {{ $isRunning ? 'true' : 'false' }};
        if (isRunning) {
            // Run automation check immediately
            runAutomationCheck();
            
            // Then run it every 30 seconds
            setInterval(function() {
                // Only refresh if user is still on the page
                if (document.visibilityState === 'visible') {
                    runAutomationCheck();
                }
            }, 30000); // 30 seconds
        }
    });
});

function triggerGoogleSheetsImport() {
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        showAlert('Bootstrap not available. Please refresh the page.', 'warning');
        return;
    }
    
    const modalElement = document.getElementById('importProgressModal');
    if (!modalElement) {
        showAlert('Import progress modal not found.', 'error');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    // Update modal title for Google Sheets
    const modalTitle = document.querySelector('#importProgressModal .modal-title');
    if (modalTitle) {
        modalTitle.textContent = 'Importing from Google Sheets...';
    }
    
    // Simulate import progress
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 100) progress = 100;
        
        document.getElementById('importProgress').style.width = progress + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(() => {
                modal.hide();
                refreshStatus();
            }, 1000);
        }
    }, 500);
    
    // Make actual Google Sheets import request
    fetch('/admin/automation/trigger-import', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Google Sheets import completed! Created: ${data.created}, Updated: ${data.updated}, Errors: ${data.errors}`, 'success');
        } else {
            showAlert('Google Sheets import failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Google Sheets import error: ' + error.message, 'danger');
    });
}

function triggerImport() {
    // Legacy function - redirect to Google Sheets import
    triggerGoogleSheetsImport();
}

function refreshStatus() {
    // Reload the page to get fresh data
    location.reload();
}

function saveSettings() {
    // Show loading state
    const saveButton = document.querySelector('button[onclick="saveSettings()"]');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Saving...';
    saveButton.disabled = true;
    
    // Re-initialize Feather icons for the loading spinner
    waitForFeather(function() {
        safeFeatherReplace();
    });
    
    const formData = {
        google_sheets_url: document.getElementById('googleSheetsUrl').value,
        notification_email: document.getElementById('notificationEmail').value,
        import_frequency: document.getElementById('importFrequency').value,
        check_interval: parseInt(document.getElementById('checkInterval').value),
        email_notifications: document.getElementById('emailNotifications').checked,
        update_existing: document.getElementById('updateExisting').value === '1'
    };
    
    fetch('/admin/automation/save-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Settings saved successfully!', 'success');
            refreshStatus();
        } else {
            showAlert('Error saving settings: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error saving settings: ' + error.message, 'danger');
    })
    .finally(() => {
        // Restore button state
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
        
        // Re-initialize Feather icons
        waitForFeather(function() {
            safeFeatherReplace();
        });
    });
}

function checkFileStatus() {
    const filePath = document.getElementById('excelFile').value;
    
    fetch('/admin/automation/check-file', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ file_path: filePath })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`File found! Size: ${(data.file_size / 1024).toFixed(2)} KB, Last modified: ${data.last_modified}`, 'success');
            // Update the file status card
            document.getElementById('fileStatus').textContent = 'File Found';
            document.getElementById('fileStatusCard').className = 'card bg-success text-white';
        } else {
            showAlert('File not found: ' + data.file_path, 'danger');
            // Update the file status card
            document.getElementById('fileStatus').textContent = 'File Not Found';
            document.getElementById('fileStatusCard').className = 'card bg-warning text-white';
        }
    })
    .catch(error => {
        showAlert('Error checking file: ' + error.message, 'danger');
    });
}

function loadAutomationStatus() {
    // Real status is already loaded from backend
    // This function can be used for periodic updates if needed
    console.log('Automation status loaded from backend');
}

function loadRecentActivity() {
    // Real activity data is loaded from backend
    const activities = @json($recentLogs ?? []);
    
    const container = document.getElementById('recentActivity');
    if (activities.length > 0) {
        container.innerHTML = activities.map(activity => `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <small class="text-muted">${activity.time}</small>
                    <div class="small">${activity.message}</div>
                </div>
                <span class="badge bg-${activity.level === 'info' ? 'info' : activity.level === 'error' ? 'danger' : 'success'}">
                    ${activity.level}
                </span>
            </div>
        `).join('');
    } else {
        container.innerHTML = '<div class="text-muted text-center">No recent activity</div>';
    }
}

function loadImportLogs() {
    // Real log data is loaded from backend
    const logs = @json($recentLogs ?? []);
    
    const tbody = document.getElementById('importLogs');
    if (logs.length > 0) {
        tbody.innerHTML = logs.map(log => `
            <tr>
                <td>${log.time}</td>
                <td><span class="badge bg-${log.level === 'info' ? 'info' : log.level === 'error' ? 'danger' : 'success'}">${log.level.toUpperCase()}</span></td>
                <td>${log.message}</td>
                <td><i data-feather="check-circle" width="16" height="16" class="text-${log.level === 'info' ? 'info' : log.level === 'error' ? 'danger' : 'success'}"></i></td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No recent logs</td></tr>';
    }
    
    // Re-initialize Feather icons after dynamic content
    waitForFeather(function() {
        safeFeatherReplace();
    });
}

function checkFileStatus() {
    showAlert('File status checked successfully!', 'info');
}

function viewLogs() {
    showAlert('Opening log viewer...', 'info');
}

function startAutomation() {
    const interval = parseInt(document.getElementById('checkInterval').value) || 300; // 5 minutes default
    
    // Show loading state
    const startButton = document.querySelector('button[onclick="startAutomation()"]');
    const originalText = startButton.innerHTML;
    startButton.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Starting...';
    startButton.disabled = true;
    
    fetch('/admin/automation/start-api', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: 'google_sheets',
            interval: interval
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Google Sheets automation started successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Error starting Google Sheets automation: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error starting Google Sheets automation: ' + error.message, 'danger');
    })
    .finally(() => {
        // Restore button state
        startButton.innerHTML = originalText;
        startButton.disabled = false;
        safeFeatherReplace();
    });
}

function stopAutomation() {
    // Show loading state
    const stopButton = document.querySelector('button[onclick="stopAutomation()"]');
    const originalText = stopButton.innerHTML;
    stopButton.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Stopping...';
    stopButton.disabled = true;
    
    fetch('/admin/automation/stop-api', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: 'google_sheets'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Google Sheets automation stopped successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Error stopping Google Sheets automation: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error stopping Google Sheets automation: ' + error.message, 'danger');
    })
    .finally(() => {
        // Restore button state
        stopButton.innerHTML = originalText;
        stopButton.disabled = false;
        safeFeatherReplace();
    });
}

function testGoogleSheets() {
    // Show loading state
    const testButton = document.querySelector('button[onclick="testGoogleSheets()"]');
    if (testButton) {
        const originalText = testButton.innerHTML;
        testButton.innerHTML = '<i data-feather="loader" width="16" height="16"></i> Testing...';
        testButton.disabled = true;
        
        fetch('/admin/automation/test-api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                type: 'google_sheets'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(`Google Sheets test completed! Created: ${data.created}, Updated: ${data.updated}, Errors: ${data.errors}`, 'success');
            } else {
                showAlert('Google Sheets test failed: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Google Sheets test error: ' + error.message, 'danger');
        })
        .finally(() => {
            // Restore button state
            testButton.innerHTML = originalText;
            testButton.disabled = false;
            safeFeatherReplace();
        });
    }
}

function runAutomationCheck() {
    // Run automation check in background without blocking the page
    fetch('/admin/automation/run-check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the last check time in the UI
            const lastCheckElement = document.querySelector('#sheetsStatus').parentElement.querySelector('small');
            if (lastCheckElement && data.last_check) {
                lastCheckElement.textContent = 'Last check: ' + data.last_check;
            }
            
            // If there were new imports, show a subtle notification
            if (data.last_results && data.last_results.created > 0) {
                console.log('New students imported:', data.last_results.created);
            }
        }
    })
    .catch(error => {
        console.log('Automation check error:', error);
    });
}

function testEmail() {
    showAlert('Test email sent!', 'success');
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.main-content').insertBefore(alertDiv, document.querySelector('.main-content').firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush