@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Automation Management
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="refresh" onclick="refreshStatus()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-robot"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Automation Status</span>
                                    <span class="info-box-number" id="automation-status">
                                        <span class="badge badge-secondary">Checking...</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Last Check</span>
                                    <span class="info-box-number" id="last-check">
                                        {{ now()->format('Y-m-d H:i:s') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Control Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Automation Controls</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" onclick="startAutomation('google_sheets')">
                                    <i class="fas fa-play"></i> Start Google Sheets
                                </button>
                                <button type="button" class="btn btn-primary" onclick="startAutomation('excel')">
                                    <i class="fas fa-play"></i> Start Excel
                                </button>
                                <button type="button" class="btn btn-warning" onclick="stopAutomation()">
                                    <i class="fas fa-stop"></i> Stop Automation
                                </button>
                                <button type="button" class="btn btn-info" onclick="refreshStatus()">
                                    <i class="fas fa-sync-alt"></i> Refresh Status
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Test Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Test Automation</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="testGoogleSheets()">
                                    <i class="fas fa-vial"></i> Test Google Sheets
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="testExcel()">
                                    <i class="fas fa-vial"></i> Test Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Logs Section -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Recent Logs</h5>
                            <div class="card">
                                <div class="card-body">
                                    <pre id="logs-content" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;">
Loading logs...
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Processing...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let statusCheckInterval;

$(document).ready(function() {
    refreshStatus();
    loadLogs();
    
    // Auto-refresh status every 30 seconds
    statusCheckInterval = setInterval(refreshStatus, 30000);
});

function refreshStatus() {
    $.ajax({
        url: '{{ route("admin.automation.status") }}',
        method: 'GET',
        success: function(response) {
            updateStatus(response);
        },
        error: function() {
            $('#automation-status').html('<span class="badge badge-danger">Error</span>');
        }
    });
}

function updateStatus(data) {
    if (data.running) {
        $('#automation-status').html('<span class="badge badge-success">Running (PID: ' + data.pid + ')</span>');
    } else {
        $('#automation-status').html('<span class="badge badge-secondary">Stopped</span>');
    }
    
    if (data.last_check) {
        $('#last-check').text(data.last_check);
    }
}

function startAutomation(type) {
    showLoading();
    
    $.ajax({
        url: '{{ route("admin.automation.start") }}',
        method: 'POST',
        data: {
            type: type,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Automation started successfully!');
                refreshStatus();
            } else {
                showAlert('error', response.message || 'Failed to start automation');
            }
        },
        error: function() {
            hideLoading();
            showAlert('error', 'Failed to start automation');
        }
    });
}

function stopAutomation() {
    showLoading();
    
    $.ajax({
        url: '{{ route("admin.automation.stop") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Automation stopped successfully!');
                refreshStatus();
            } else {
                showAlert('error', response.message || 'Failed to stop automation');
            }
        },
        error: function() {
            hideLoading();
            showAlert('error', 'Failed to stop automation');
        }
    });
}

function testGoogleSheets() {
    showLoading();
    
    $.ajax({
        url: '{{ route("admin.automation.test") }}',
        method: 'POST',
        data: {
            type: 'google_sheets',
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            hideLoading();
            showAlert('info', 'Test completed. Check logs for details.');
            loadLogs();
        },
        error: function() {
            hideLoading();
            showAlert('error', 'Test failed');
        }
    });
}

function testExcel() {
    showLoading();
    
    $.ajax({
        url: '{{ route("admin.automation.test") }}',
        method: 'POST',
        data: {
            type: 'excel',
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            hideLoading();
            showAlert('info', 'Test completed. Check logs for details.');
            loadLogs();
        },
        error: function() {
            hideLoading();
            showAlert('error', 'Test failed');
        }
    });
}

function loadLogs() {
    $.ajax({
        url: '{{ route("admin.automation.logs") }}',
        method: 'GET',
        success: function(response) {
            $('#logs-content').text(response.logs);
        },
        error: function() {
            $('#logs-content').text('Failed to load logs');
        }
    });
}

function showLoading() {
    $('#loadingModal').modal('show');
}

function hideLoading() {
    $('#loadingModal').modal('hide');
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    $('.container-fluid').prepend(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

// Clean up interval on page unload
$(window).on('beforeunload', function() {
    if (statusCheckInterval) {
        clearInterval(statusCheckInterval);
    }
});
</script>
@endpush

