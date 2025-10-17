@extends('admin.layouts.app')

@section('title', 'Accounting Integration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator"></i>
                        Accounting System Integration
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Connection Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Connection Status</h5>
                                    <div id="connection-status">
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm mr-2" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span>Testing connection...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Configuration</h5>
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="badge badge-{{ config('accounting.enabled') ? 'success' : 'danger' }}">
                                            {{ config('accounting.enabled') ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </p>
                                    <p class="mb-1"><strong>Auto Sync:</strong> 
                                        <span class="badge badge-{{ config('accounting.auto_sync') ? 'success' : 'warning' }}">
                                            {{ config('accounting.auto_sync') ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </p>
                                    <p class="mb-0"><strong>API URL:</strong> {{ config('accounting.api_url') ?: 'Not configured' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Payment Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div id="statistics-content">
                                        <div class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-2">Loading statistics...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sync Controls -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Sync Controls</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-primary" id="sync-all-btn">
                                                <i class="fas fa-sync"></i>
                                                Sync All Unsynced Payments
                                            </button>
                                            <button type="button" class="btn btn-info ml-2" id="test-connection-btn">
                                                <i class="fas fa-plug"></i>
                                                Test Connection
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date-range">Date Range:</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control" id="from-date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">to</span>
                                                    </div>
                                                    <input type="date" class="form-control" id="to-date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Recent Payments</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="payments-table">
                                            <thead>
                                                <tr>
                                                    <th>Payment ID</th>
                                                    <th>Student</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Paid At</th>
                                                    <th>Sync Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <div class="spinner-border spinner-border-sm" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        Loading payments...
                                                    </td>
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
</div>

<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sync Payments</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="sync-progress">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p class="mt-2" id="sync-status">Preparing sync...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Test connection on page load
    testConnection();
    loadStatistics();
    loadPayments();

    // Test connection button
    $('#test-connection-btn').click(function() {
        testConnection();
    });

    // Sync all button
    $('#sync-all-btn').click(function() {
        syncAllPayments();
    });

    // Load payments when date range changes
    $('#from-date, #to-date').change(function() {
        loadPayments();
    });

    function testConnection() {
        $('#connection-status').html(`
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm mr-2" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <span>Testing connection...</span>
            </div>
        `);

        $.get('/api/accounting/test-connection')
            .done(function(response) {
                if (response.success) {
                    $('#connection-status').html(`
                        <div class="d-flex align-items-center text-success">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Connected successfully</span>
                        </div>
                    `);
                } else {
                    $('#connection-status').html(`
                        <div class="d-flex align-items-center text-danger">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span>Connection failed: ${response.message}</span>
                        </div>
                    `);
                }
            })
            .fail(function() {
                $('#connection-status').html(`
                    <div class="d-flex align-items-center text-danger">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span>Connection failed: Unable to reach server</span>
                    </div>
                `);
            });
    }

    function loadStatistics() {
        $.get('/api/accounting/statistics')
            .done(function(response) {
                if (response.success) {
                    const stats = response.data.statistics;
                    const types = response.data.payment_types;
                    
                    let html = `
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-primary">${stats.total_payments}</h3>
                                    <p class="mb-0">Total Payments</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-success">RM ${parseFloat(stats.total_amount).toLocaleString()}</h3>
                                    <p class="mb-0">Total Amount</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-info">${stats.synced_count}</h3>
                                    <p class="mb-0">Synced</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-warning">${stats.unsynced_count}</h3>
                                    <p class="mb-0">Unsynced</p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#statistics-content').html(html);
                }
            })
            .fail(function() {
                $('#statistics-content').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load statistics
                    </div>
                `);
            });
    }

    function loadPayments() {
        const fromDate = $('#from-date').val();
        const toDate = $('#to-date').val();
        
        let url = '/api/accounting/payments';
        if (fromDate && toDate) {
            url += `?from_date=${fromDate}&to_date=${toDate}`;
        }

        $.get(url)
            .done(function(response) {
                if (response.success) {
                    let html = '';
                    if (response.data.length === 0) {
                        html = '<tr><td colspan="8" class="text-center">No payments found</td></tr>';
                    } else {
                        response.data.forEach(function(payment) {
                            const syncStatus = payment.accounting_synced ? 
                                '<span class="badge badge-success">Synced</span>' : 
                                '<span class="badge badge-warning">Not Synced</span>';
                            
                            const paidAt = payment.paid_at ? 
                                new Date(payment.paid_at).toLocaleDateString() : 
                                'N/A';

                            html += `
                                <tr>
                                    <td>${payment.lms_payment_id}</td>
                                    <td>${payment.student_name}</td>
                                    <td>RM ${parseFloat(payment.amount).toLocaleString()}</td>
                                    <td>${payment.payment_type}</td>
                                    <td><span class="badge badge-success">${payment.payment_status}</span></td>
                                    <td>${paidAt}</td>
                                    <td>${syncStatus}</td>
                                    <td>
                                        ${!payment.accounting_synced ? 
                                            `<button class="btn btn-sm btn-primary sync-single" data-id="${payment.lms_payment_id}">Sync</button>` : 
                                            '<span class="text-muted">Already synced</span>'
                                        }
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    $('#payments-table tbody').html(html);
                }
            })
            .fail(function() {
                $('#payments-table tbody').html(`
                    <tr>
                        <td colspan="8" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Failed to load payments
                        </td>
                    </tr>
                `);
            });
    }

    function syncAllPayments() {
        $('#syncModal').modal('show');
        $('#sync-progress .progress-bar').css('width', '0%');
        $('#sync-status').text('Starting sync...');

        $.post('/api/accounting/sync')
            .done(function(response) {
                if (response.success) {
                    $('#sync-progress .progress-bar').css('width', '100%');
                    $('#sync-status').text(`Sync completed! ${response.synced_count} payments synced.`);
                    loadPayments(); // Refresh the table
                } else {
                    $('#sync-status').text(`Sync failed: ${response.message}`);
                }
            })
            .fail(function() {
                $('#sync-status').text('Sync failed: Unable to reach server');
            });
    }

    // Sync single payment
    $(document).on('click', '.sync-single', function() {
        const paymentId = $(this).data('id');
        const button = $(this);
        
        button.prop('disabled', true).text('Syncing...');
        
        $.post('/api/accounting/sync', { payment_ids: [paymentId] })
            .done(function(response) {
                if (response.success) {
                    button.removeClass('btn-primary').addClass('btn-success').text('Synced');
                    loadPayments(); // Refresh the table
                } else {
                    button.prop('disabled', false).text('Sync Failed');
                }
            })
            .fail(function() {
                button.prop('disabled', false).text('Sync Failed');
            });
    });
});
</script>
@endsection
