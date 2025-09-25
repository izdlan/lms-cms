@extends('layouts.admin')

@section('title', 'Automation Status')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Automation Status</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="refreshStatus()">
                            <i data-feather="refresh-cw" width="16" height="16"></i> Refresh
                        </button>
                        <a href="{{ route('admin.automation') }}" class="btn btn-outline-secondary">
                            <i data-feather="settings" width="16" height="16"></i> Settings
                        </a>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card {{ $isRunning ? 'bg-success' : 'bg-warning' }} text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Status</h6>
                                        <h4 id="status">{{ $isRunning ? 'Running' : 'Stopped' }}</h4>
                                    </div>
                                    <i data-feather="{{ $isRunning ? 'play-circle' : 'pause-circle' }}" width="32" height="32"></i>
                                </div>
                                <small id="statusMessage">
                                    {{ $isRunning ? 'Monitoring file changes' : 'Click Start to begin monitoring' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Last Import</h6>
                                        <h4 id="lastImport">
                                            @if($lastImportTime)
                                                {{ \Carbon\Carbon::createFromTimestamp($lastImportTime)->format('H:i') }}
                                            @else
                                                --:--
                                            @endif
                                        </h4>
                                    </div>
                                    <i data-feather="clock" width="32" height="32"></i>
                                </div>
                                <small id="lastImportText">
                                    @if($lastImportTime)
                                        {{ \Carbon\Carbon::createFromTimestamp($lastImportTime)->diffForHumans() }}
                                    @else
                                        No recent imports
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card {{ $fileExists ? 'bg-success' : 'bg-danger' }} text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">File Status</h6>
                                        <h4 id="fileStatus">{{ $fileExists ? 'Found' : 'Missing' }}</h4>
                                    </div>
                                    <i data-feather="file-text" width="32" height="32"></i>
                                </div>
                                <small id="filePath">{{ basename($filePath) }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success w-100 mb-2" onclick="startAutomation()" {{ $isRunning ? 'disabled' : '' }}>
                                    <i data-feather="play" width="16" height="16"></i> Start Automation
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger w-100 mb-2" onclick="stopAutomation()" {{ !$isRunning ? 'disabled' : '' }}>
                                    <i data-feather="stop" width="16" height="16"></i> Stop Automation
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary w-100 mb-2" onclick="triggerImport()">
                                    <i data-feather="download" width="16" height="16"></i> Run Import Now
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-info w-100 mb-2" onclick="checkFileStatus()">
                                    <i data-feather="search" width="16" height="16"></i> Check File
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentActivity">
                            @if(count($recentLogs) > 0)
                                @foreach($recentLogs as $log)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <small class="text-muted">{{ $log['time'] }}</small>
                                            <div class="small">{{ $log['message'] }}</div>
                                        </div>
                                        <span class="badge bg-{{ $log['level'] === 'info' ? 'info' : ($log['level'] === 'error' ? 'danger' : 'success') }}">
                                            {{ strtoupper($log['level']) }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted text-center">No recent activity</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshStatus() {
    location.reload();
}

function startAutomation() {
    fetch('/admin/automation/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            file_path: '{{ $filePath }}',
            interval: 30
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Automation started successfully!', 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('Error starting automation: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error starting automation: ' + error.message, 'danger');
    });
}

function stopAutomation() {
    fetch('/admin/automation/stop', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Automation stopped successfully!', 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('Error stopping automation: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error stopping automation: ' + error.message, 'danger');
    });
}

function triggerImport() {
    fetch('/admin/automation/trigger-import', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            file_path: '{{ $filePath }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Import completed successfully!', 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('Import failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Import error: ' + error.message, 'danger');
    });
}

function checkFileStatus() {
    fetch('/admin/automation/check-file', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            file_path: '{{ $filePath }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            showAlert(`File found! Size: ${(data.file_size / 1024).toFixed(2)} KB`, 'success');
        } else {
            showAlert('File not found: ' + data.file_path, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error checking file: ' + error.message, 'danger');
    });
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

// Auto-refresh every 30 seconds
setInterval(() => {
    refreshStatus();
}, 30000);
</script>
@endsection

