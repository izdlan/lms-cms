@extends('layouts.admin')

@section('title', 'Student Certificates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-certificate"></i>
                        Student Certificates
                    </h3>
                    <a href="{{ route('admin.student-certificates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Generate from Excel
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('errors') && is_array(session('errors')))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Some errors occurred:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach(session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($certificates->count() > 0)
                        <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Certificate #</th>
                                            <th>Student Name</th>
                                            <th>Template</th>
                                            <th>Status</th>
                                            <th>Generated</th>
                                            <th>Downloaded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($certificates as $certificate)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="certificate_ids[]" value="{{ $certificate->id }}" class="form-check-input certificate-checkbox">
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $certificate->certificate_number }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $certificate->student_name }}</strong>
                                                </td>
                                                <td>{{ $certificate->template_name }}</td>
                                                <td>
                                                    @if($certificate->status === 'generated')
                                                        <span class="badge badge-success">Generated</span>
                                                    @elseif($certificate->status === 'downloaded')
                                                        <span class="badge badge-primary">Downloaded</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($certificate->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($certificate->generated_at)
                                                        {{ $certificate->generated_at->format('d M Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($certificate->downloaded_at)
                                                        {{ $certificate->downloaded_at->format('d M Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @if($certificate->file_path)
                                                            <a href="{{ route('admin.student-certificates.view', $certificate->id) }}" 
                                                               class="btn btn-sm btn-info" 
                                                               title="View Certificate"
                                                               target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.student-certificates.download', $certificate->id) }}" 
                                                               class="btn btn-sm btn-success" 
                                                               title="Download Certificate">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        @endif
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Delete Certificate"
                                                                onclick="deleteCertificate({{ $certificate->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <form id="bulkDownloadForm" method="POST" action="{{ route('admin.student-certificates.bulk-download') }}" style="display: inline;">
                                    @csrf
                                    <button type="button" id="bulkDownloadBtn" class="btn btn-warning" disabled>
                                        <i class="fas fa-download"></i>
                                        Download Selected
                                    </button>
                                </form>
                                
                                <button type="button" id="bulkDeleteBtn" class="btn btn-danger ml-2" disabled>
                                    <i class="fas fa-trash"></i>
                                    Delete Selected
                                </button>
                                <button type="button" id="clearAllBtn" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i>
                                    Clear All
                                </button>
                                <span id="selectedCount" class="ml-2 text-muted">0 selected</span>
                            </div>

                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No certificates generated yet</h5>
                            <p class="text-muted">Upload an Excel file to generate certificates for students.</p>
                            <a href="{{ route('admin.student-certificates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Generate from Excel
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this certificate? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Bulk Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="bulkDeleteCount">0</span> selected certificates? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This will permanently delete the certificate files and database records.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="bulkDeleteForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Delete All Selected</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all functionality
    $('#selectAll').change(function() {
        $('.certificate-checkbox').prop('checked', this.checked);
        storeSelectedIds();
        updateBulkActions();
    });

    // Individual checkbox change
    $('.certificate-checkbox').change(function() {
        storeSelectedIds();
        updateBulkActions();
    });

    function updateBulkActions() {
        const allSelectedIds = getAllSelectedIds();
        const currentPageChecked = $('.certificate-checkbox:checked').length;
        
        $('#selectedCount').text(allSelectedIds.length + ' selected (current page: ' + currentPageChecked + ')');
        $('#bulkDownloadBtn').prop('disabled', allSelectedIds.length === 0);
        $('#bulkDeleteBtn').prop('disabled', allSelectedIds.length === 0);
    }

    // Bulk download
    $('#bulkDownloadBtn').click(function() {
        const selectedIds = getAllSelectedIds();
        
        if (selectedIds.length > 0) {
            // Clear any existing hidden inputs
            $('#bulkDownloadForm').find('input[name="certificate_ids[]"]').remove();
            
            // Create hidden inputs for the IDs
            selectedIds.forEach(function(id) {
                $('#bulkDownloadForm').append('<input type="hidden" name="certificate_ids[]" value="' + id + '">');
            });
            
            // Submit the form
            $('#bulkDownloadForm').submit();
        }
    });

    // Bulk delete
    $('#bulkDeleteBtn').click(function() {
        const selectedIds = getAllSelectedIds();
        
        if (selectedIds.length > 0) {
            $('#bulkDeleteCount').text(selectedIds.length);
            $('#bulkDeleteModal').modal('show');
        }
    });

    // Handle bulk delete form submission
    $('#bulkDeleteForm').submit(function(e) {
        e.preventDefault();
        
        const selectedIds = getAllSelectedIds();
        
        // Clear any existing hidden inputs
        $('#bulkDeleteForm').find('input[name="certificate_ids[]"]').remove();
        
        // Create hidden inputs for the IDs
        selectedIds.forEach(function(id) {
            $('#bulkDeleteForm').append('<input type="hidden" name="certificate_ids[]" value="' + id + '">');
        });
        
        // Set the correct action and method
        $('#bulkDeleteForm').attr('action', '{{ route("admin.student-certificates.bulk-delete") }}');
        $('#bulkDeleteForm').attr('method', 'POST');
        
        // Submit the form
        this.submit();
    });

    // Function to get all selected IDs from all pages
    function getAllSelectedIds() {
        const selectedIds = [];
        
        // Get IDs from current page
        $('.certificate-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        // Get IDs from sessionStorage (stored from other pages)
        const storedIds = JSON.parse(sessionStorage.getItem('selectedCertificates') || '[]');
        storedIds.forEach(function(id) {
            if (selectedIds.indexOf(id) === -1) {
                selectedIds.push(id);
            }
        });
        
        return selectedIds;
    }

    // Function to store selected IDs in sessionStorage
    function storeSelectedIds() {
        const selectedIds = [];
        $('.certificate-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        sessionStorage.setItem('selectedCertificates', JSON.stringify(selectedIds));
    }

    // Clear all selections
    $('#clearAllBtn').click(function() {
        // Clear current page checkboxes
        $('.certificate-checkbox').prop('checked', false);
        $('#selectAll').prop('checked', false);
        
        // Clear sessionStorage
        sessionStorage.removeItem('selectedCertificates');
        
        // Update UI
        updateBulkActions();
    });
});

function deleteCertificate(id) {
    $('#deleteForm').attr('action', '{{ route("admin.student-certificates.destroy", ":id") }}'.replace(':id', id));
    $('#deleteModal').modal('show');
}
</script>
@endpush
