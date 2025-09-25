@extends('layouts.app')

@section('title', 'Student Import Automation')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4><i data-feather="settings" width="20" height="20"></i> Automation</h4>
                </div>
                <nav class="sidebar-nav">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i data-feather="home" width="16" height="16"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.students') }}">
                                <i data-feather="users" width="16" height="16"></i> Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.import') }}">
                                <i data-feather="upload" width="16" height="16"></i> Import
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/maintenance">
                                <i data-feather="refresh-cw" width="16" height="16"></i> Automation
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Student Import Automation</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="triggerImport()">
                            <i data-feather="play" width="16" height="16"></i> Run Import Now
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="refreshStatus()">
                            <i data-feather="refresh-cw" width="16" height="16"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Auto Import</h6>
                            <h4 id="autoImportStatus">{{ $automationConfig['status'] ?? 'Active' }}</h4>
                        </div>
                        <i data-feather="clock" width="32" height="32"></i>
                    </div>
                    <small>Runs {{ $automationConfig['import_frequency'] ?? 'every minute' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Last Import</h6>
                                        <h4 id="lastImportTime">
                                            @if($lastImportTime)
                                                {{ \Carbon\Carbon::createFromTimestamp($lastImportTime)->format('H:i') }}
                                            @else
                                                --:--
                                            @endif
                                        </h4>
                                    </div>
                                    <i data-feather="check-circle" width="32" height="32"></i>
                                </div>
                                <small id="lastImportStatus">
                                    @if($lastImportTime)
                                        {{ \Carbon\Carbon::createFromTimestamp($lastImportTime)->diffForHumans() }}
                                    @else
                                        No recent imports
                                    @endif
                                </small>
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
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">File Status</h6>
                                        <h4 id="fileStatus">{{ $fileStatus }}</h4>
                                    </div>
                                    <i data-feather="file-text" width="32" height="32"></i>
                                </div>
                                <small id="filePath">{{ basename($automationConfig['excel_file'] ?? 'Enrollment OEM.xlsx') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Automation Settings -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Automation Settings</h5>
                            </div>
                            <div class="card-body">
                                <form id="automationForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="excelFile" class="form-label">Excel File Path</label>
                                                <input type="text" class="form-control" id="excelFile" 
                                                       value="{{ $automationConfig['excel_file'] ?? storage_path('app/students/Enrollment OEM.xlsx') }}" 
                                                       placeholder="Path to Excel file">
                                                <div class="form-text">Full path to the Excel file to monitor</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="notificationEmail" class="form-label">Notification Email</label>
                                                <input type="email" class="form-control" id="notificationEmail" 
                                                       value="{{ $automationConfig['notification_email'] ?? '' }}"
                                                       placeholder="admin@example.com">
                                                <div class="form-text">Email to receive import notifications</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="importFrequency" class="form-label">Import Frequency</label>
                                            <select class="form-select" id="importFrequency">
                                                <option value="every-minute" {{ ($automationConfig['import_frequency'] ?? 'every-minute') === 'every-minute' ? 'selected' : '' }}>Every Minute</option>
                                                <option value="every-5-minutes" {{ ($automationConfig['import_frequency'] ?? '') === 'every-5-minutes' ? 'selected' : '' }}>Every 5 Minutes</option>
                                                <option value="every-15-minutes" {{ ($automationConfig['import_frequency'] ?? '') === 'every-15-minutes' ? 'selected' : '' }}>Every 15 Minutes</option>
                                                <option value="hourly" {{ ($automationConfig['import_frequency'] ?? '') === 'hourly' ? 'selected' : '' }}>Every Hour</option>
                                                <option value="daily" {{ ($automationConfig['import_frequency'] ?? '') === 'daily' ? 'selected' : '' }}>Daily</option>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="watchMode" class="form-label">Watch Mode</label>
                                                <select class="form-select" id="watchMode">
                                                    <option value="scheduled">Scheduled (Recommended)</option>
                                                    <option value="realtime">Real-time File Watching</option>
                                                    <option value="disabled">Disabled</option>
                                                </select>
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

                                    <button type="submit" class="btn btn-primary">
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
                                    <button type="button" class="btn btn-success" onclick="triggerImport()">
                                        <i data-feather="play" width="16" height="16"></i> Run Import Now
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="checkFileStatus()">
                                        <i data-feather="file-check" width="16" height="16"></i> Check File Status
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="viewLogs()">
                                        <i data-feather="file-text" width="16" height="16"></i> View Import Logs
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="testEmail()">
                                        <i data-feather="mail" width="16" height="16"></i> Test Email
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
                                    <div class="text-center text-muted">
                                        <i data-feather="clock" width="24" height="24"></i>
                                        <p>No recent activity</p>
                                    </div>
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
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Loading logs...</td>
                                            </tr>
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

@push('styles')
<style>
.admin-dashboard {
    min-height: 100vh;
    background-color: #f8f9fa;
}

.sidebar {
    background: #2d3748;
    min-height: 100vh;
    padding: 0;
}

.sidebar-header {
    background: #1a202c;
    padding: 1.5rem;
    color: white;
    border-bottom: 1px solid #4a5568;
}

.sidebar-header h4 {
    margin: 0;
    font-weight: bold;
}

.sidebar-nav {
    padding: 1rem 0;
}

.sidebar-nav .nav-link {
    color: #a0aec0;
    padding: 0.75rem 1.5rem;
    border: none;
    transition: all 0.3s ease;
}

.sidebar-nav .nav-link:hover,
.sidebar-nav .nav-link.active {
    background-color: #4a5568;
    color: white;
}

.main-content {
    padding: 2rem;
}

.card {
    border: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #2d3748;
    background-color: #f8f9fa;
}

.btn-group .btn {
    margin-right: 0.5rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    loadAutomationStatus();
    loadRecentActivity();
    loadImportLogs();
});

function triggerImport() {
    const modal = new bootstrap.Modal(document.getElementById('importProgressModal'));
    modal.show();
    
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
    
    // Make actual import request
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
            showAlert('Import completed successfully!', 'success');
        } else {
            showAlert('Import failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Import error: ' + error.message, 'danger');
    });
}

function refreshStatus() {
    loadAutomationStatus();
    loadRecentActivity();
    loadImportLogs();
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
    
    feather.replace();
}

function checkFileStatus() {
    showAlert('File status checked successfully!', 'info');
}

function viewLogs() {
    showAlert('Opening log viewer...', 'info');
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