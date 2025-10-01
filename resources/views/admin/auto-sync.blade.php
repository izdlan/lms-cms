@extends('layouts.admin')

@section('title', 'Auto Sync Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Auto Sync Management</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Google Drive Auto Sync</h5>
                    <p class="text-muted">Automatically sync new students from your Google Drive Excel file</p>
                </div>
                <div class="card-body">
                    <!-- Sync Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Sync Status</h6>
                                    <div id="sync-status">
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                            <span>Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Configuration</h6>
                                    <div class="mb-2">
                                        <label class="form-label">Sync Interval (minutes)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="sync-interval" min="1" max="60" value="5">
                                            <button class="btn btn-outline-primary" type="button" onclick="updateSyncInterval()">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" onclick="startAutoSync()">
                                    <i data-feather="play" width="16" height="16"></i> Start Auto Sync
                                </button>
                                <button type="button" class="btn btn-warning" onclick="forceSync()">
                                    <i data-feather="refresh-cw" width="16" height="16"></i> Force Sync Now
                                </button>
                                <button type="button" class="btn btn-danger" onclick="stopAutoSync()">
                                    <i data-feather="stop" width="16" height="16"></i> Stop Auto Sync
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sync Log -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Recent Sync Activity</h6>
                                <small class="text-muted" id="activity-last-updated">Last updated: Never</small>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div id="sync-log" class="sync-log">
                                        <div class="text-center text-muted py-3">
                                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                            Loading activity...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">🚀 HTTP Cron (Recommended)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">For reliable auto-sync, use external cron service:</p>
                    <div class="mb-3">
                        <label class="form-label">Cron URL:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cron-url" value="{{ url('/admin/import-students') }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyCronUrl()">
                                <i data-feather="copy" width="16" height="16"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cron Command:</label>
                        <code class="d-block p-2 bg-light small">*/5 * * * * curl -s "{{ url('/admin/import-students') }}"</code>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ url('/admin/import-students') }}" target="_blank" class="btn btn-primary">
                            <i data-feather="external-link" width="16" height="16"></i> Test Endpoint
                        </a>
                        <a href="/docs/HTTP_CRON_SETUP.md" target="_blank" class="btn btn-outline-primary">
                            <i data-feather="book-open" width="16" height="16"></i> Setup Guide
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="testConnection()">
                            <i data-feather="wifi" width="16" height="16"></i> Test Google Drive Connection
                        </button>
                        <button class="btn btn-outline-info" onclick="viewStudents()">
                            <i data-feather="users" width="16" height="16"></i> View All Students
                        </button>
                        <button class="btn btn-outline-secondary" onclick="viewLogs()">
                            <i data-feather="file-text" width="16" height="16"></i> View System Logs
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">How It Works</h5>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">System checks Google Drive file every 5 minutes</li>
                        <li class="list-group-item">Detects changes by comparing file timestamps</li>
                        <li class="list-group-item">Imports only new students automatically</li>
                        <li class="list-group-item">Sends notifications when new students are added</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sync-log {
    max-height: 300px;
    overflow-y: auto;
    font-family: monospace;
    font-size: 12px;
}

.sync-log .log-entry {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.sync-log .log-entry:last-child {
    border-bottom: none;
}

.sync-log .log-success {
    color: #28a745;
}

.sync-log .log-error {
    color: #dc3545;
}

.sync-log .log-info {
    color: #17a2b8;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather icons with delay
    setTimeout(() => {
        try {
            if (typeof safeFeatherReplace === 'function') {
                safeFeatherReplace();
            } else if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.warn('Feather icons error:', error);
        }
    }, 200);
    
    // Load initial status with delay
    setTimeout(() => {
        loadSyncStatus();
        loadRecentActivities();
    }, 500);
    
    // Auto-sync every 5 minutes (as per sync interval)
    setInterval(performAutoSync, 5 * 60 * 1000);
    
    // Auto-refresh activities every 30 seconds
    setInterval(loadRecentActivities, 30 * 1000);
    
    // Cleanup old activities every hour
    setInterval(cleanupOldActivities, 60 * 60 * 1000);
});

function loadSyncStatus() {
    // Add timeout to prevent hanging
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
    
    fetch('/sync-status', {
        signal: controller.signal
    })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateSyncStatusDisplay(data.status);
            } else {
                showAlert('danger', 'Failed to load sync status: ' + data.message);
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                console.error('Sync status request timed out');
                showAlert('warning', 'Sync status request timed out');
            } else {
                console.error('Error loading sync status:', error);
                showAlert('danger', 'Error loading sync status: ' + error.message);
            }
        });
}

function updateSyncStatusDisplay(status) {
    const statusDiv = document.getElementById('sync-status');
    const isConfigured = status.is_configured;
    const lastSync = status.last_sync;
    const nextSyncIn = status.next_sync_in_minutes;
    
    let statusHtml = '';
    
    if (!isConfigured) {
        statusHtml = '<div class="text-danger"><i data-feather="alert-circle" width="16" height="16"></i> Not Configured</div>';
    } else if (lastSync === 'Never') {
        statusHtml = '<div class="text-warning"><i data-feather="clock" width="16" height="16"></i> Never Synced</div>';
    } else if (nextSyncIn > 0) {
        statusHtml = `<div class="text-primary fw-bold"><i data-feather="clock" width="16" height="16"></i> Next sync in ${nextSyncIn.toFixed(1)} minutes</div>`;
    } else {
        statusHtml = '<div class="text-success"><i data-feather="check-circle" width="16" height="16"></i> Ready to sync</div>';
    }
    
    statusHtml += `<div class="mt-2"><small class="text-muted">Last sync: ${lastSync}</small></div>`;
    statusHtml += `<div><small class="text-muted">File hash: ${status.file_hash}</small></div>`;
    
    statusDiv.innerHTML = statusHtml;
    
    // Update sync interval input
    document.getElementById('sync-interval').value = status.sync_interval_minutes;
    
    // Re-initialize Feather icons with delay
    setTimeout(() => {
        try {
            if (typeof safeFeatherReplace === 'function') {
                safeFeatherReplace();
            } else if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.warn('Feather icons error in status update:', error);
        }
    }, 100);
}

function startAutoSync() {
    showAlert('info', 'Starting auto sync...');
    
    fetch('/admin/auto-sync/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            addLogEntry('success', data.message);
            loadSyncStatus();
        } else {
            showAlert('danger', data.message);
            addLogEntry('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error starting auto sync:', error);
        showAlert('danger', 'Error starting auto sync: ' + error.message);
        addLogEntry('error', 'Error starting auto sync: ' + error.message);
    });
}

function stopAutoSync() {
    if (confirm('Are you sure you want to stop auto sync?')) {
        fetch('/admin/auto-sync/stop', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                addLogEntry('info', data.message);
                loadSyncStatus();
                loadRecentActivities();
            } else {
                showAlert('danger', data.message);
                addLogEntry('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error stopping auto sync:', error);
            showAlert('danger', 'Error stopping auto sync');
            addLogEntry('error', 'Error stopping auto sync');
        });
    }
}

function forceSync() {
    showAlert('info', 'Force syncing now...');
    
    fetch('/admin/auto-sync/force', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            addLogEntry('success', data.message);
            loadSyncStatus();
        } else {
            showAlert('danger', data.message);
            addLogEntry('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error force syncing:', error);
        showAlert('danger', 'Error force syncing');
        addLogEntry('error', 'Error force syncing');
    });
}

function updateSyncInterval() {
    const interval = document.getElementById('sync-interval').value;
    
    if (interval < 1 || interval > 60) {
        showAlert('danger', 'Sync interval must be between 1 and 60 minutes');
        return;
    }
    
    fetch('/admin/auto-sync/set-interval', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ interval: parseInt(interval) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            addLogEntry('info', data.message);
            loadSyncStatus();
        } else {
            showAlert('danger', data.message);
            addLogEntry('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error updating sync interval:', error);
        showAlert('danger', 'Error updating sync interval');
        addLogEntry('error', 'Error updating sync interval');
    });
}

function addLogEntry(type, message) {
    const logDiv = document.getElementById('sync-log');
    const timestamp = new Date().toLocaleTimeString();
    
    let logClass = 'log-info';
    if (type === 'success') logClass = 'log-success';
    if (type === 'error') logClass = 'log-error';
    
    const logEntry = document.createElement('div');
    logEntry.className = `log-entry ${logClass}`;
    logEntry.innerHTML = `[${timestamp}] ${message}`;
    
    // Remove "No sync activity yet" message if it exists
    const noActivity = logDiv.querySelector('.text-muted');
    if (noActivity) {
        noActivity.remove();
    }
    
    // Add new entry at the top
    logDiv.insertBefore(logEntry, logDiv.firstChild);
    
    // Keep only last 20 entries
    const entries = logDiv.querySelectorAll('.log-entry');
    if (entries.length > 20) {
        entries[entries.length - 1].remove();
    }
}

function testConnection() {
    showAlert('info', 'Testing Google Drive connection...');
    
    fetch('/admin/test-import')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Google Drive connection successful!');
                addLogEntry('success', 'Google Drive connection test passed');
            } else {
                showAlert('danger', 'Google Drive connection failed: ' + data.message);
                addLogEntry('error', 'Google Drive connection test failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error testing connection:', error);
            showAlert('danger', 'Error testing connection');
            addLogEntry('error', 'Error testing connection');
        });
}

function viewStudents() {
    window.location.href = '/admin/students';
}

function viewLogs() {
    window.open('/admin/test-import', '_blank');
}

function loadRecentActivities() {
    fetch('/recent-activities')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateActivityDisplay(data.activities);
                document.getElementById('activity-last-updated').textContent = 
                    'Last updated: ' + new Date().toLocaleTimeString();
            } else {
                console.error('Failed to load activities:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading activities:', error);
        });
}

function updateActivityDisplay(activities) {
    const syncLog = document.getElementById('sync-log');
    
    if (activities.length === 0) {
        syncLog.innerHTML = '<div class="text-muted text-center py-3">No sync activity yet</div>';
        return;
    }
    
    let html = '';
    activities.forEach(activity => {
        const badgeClass = activity.status_badge_class || 'secondary';
        const icon = activity.status === 'success' ? 'check-circle' : 
                   activity.status === 'error' ? 'x-circle' : 'alert-circle';
        
        html += `
            <div class="d-flex justify-content-between align-items-start mb-2 p-2 border rounded">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-${badgeClass} me-2">${activity.status.toUpperCase()}</span>
                        <small class="text-muted">${activity.formatted_time}</small>
                    </div>
                    <div class="text-sm">${activity.message}</div>
                    ${activity.created_count > 0 || activity.updated_count > 0 || activity.error_count > 0 ? 
                        `<small class="text-muted">
                            Created: ${activity.created_count}, 
                            Updated: ${activity.updated_count}, 
                            Errors: ${activity.error_count}
                        </small>` : ''
                    }
                </div>
                <i data-feather="${icon}" width="16" height="16" class="text-${badgeClass}"></i>
            </div>
        `;
    });
    
    syncLog.innerHTML = html;
    
    // Re-initialize Feather icons
    setTimeout(() => {
        try {
            if (typeof safeFeatherReplace === 'function') {
                safeFeatherReplace();
            } else if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.warn('Feather icons error in activity update:', error);
        }
    }, 100);
}

function cleanupOldActivities() {
    fetch('/cleanup-activities')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.deleted_count > 0) {
                console.log(`Cleaned up ${data.deleted_count} old activities`);
                // Reload activities after cleanup
                loadRecentActivities();
            }
        })
        .catch(error => {
            console.error('Error cleaning up activities:', error);
        });
}

function performAutoSync() {
    console.log('Auto-sync: Checking if sync should run...');
    
    fetch('/auto-sync')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.skipped) {
                    console.log('Auto-sync: Skipped - not yet time for next sync');
                } else {
                    console.log('Auto-sync: Completed successfully', data.result);
                    // Update display after successful sync
                    loadSyncStatus();
                    loadRecentActivities();
                }
            } else {
                console.error('Auto-sync: Failed', data.message);
            }
        })
        .catch(error => {
            console.error('Auto-sync: Error', error);
        });
}

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of page
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function copyCronUrl() {
    const cronUrlInput = document.getElementById('cron-url');
    cronUrlInput.select();
    cronUrlInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        showAlert('success', 'Cron URL copied to clipboard!');
    } catch (err) {
        showAlert('warning', 'Could not copy to clipboard. Please copy manually.');
    }
}
</script>
@endpush
