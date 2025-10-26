@extends('layouts.admin')

@section('title', 'Student Information PDFs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-pdf"></i> Student Information PDF Generator
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" id="downloadSelectedBtn" disabled>
                            <i class="fas fa-download"></i> Download Selected PDFs
                        </button>
                        <button type="button" class="btn btn-success" id="downloadAllBtn">
                            <i class="fas fa-download"></i> Download All PDFs
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Selection Controls -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Select All Students
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="badge badge-info" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-sm btn-secondary ml-2" id="clearSelection">
                                Clear Selection
                            </button>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllHeader">
                                    </th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="student-checkbox" 
                                               value="{{ $student->student_id }}" 
                                               data-name="{{ $student->name }}">
                                    </td>
                                    <td>{{ $student->student_id ?? 'N/A' }}</td>
                                    <td>{{ $student->name ?? 'N/A' }}</td>
                                    <td>{{ $student->programme_name ?? 'N/A' }}</td>
                                    <td>{{ $student->email ?? $student->student_email ?? 'N/A' }}</td>
                                    <td>{{ $student->ic_passport ?? $student->ic ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.student-info.preview', $student->student_id) }}" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i> Preview
                                            </a>
                                            <a href="{{ route('admin.student-info.pdf', $student->student_id) }}" 
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-download"></i> PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No students found.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $students->links('pagination.no-arrows') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Download Confirmation Modal -->
<div class="modal fade" id="bulkDownloadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download"></i> Download Student Information PDFs
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>You are about to download PDFs for <strong id="modalStudentCount">0</strong> students.</p>
                <p>This will create a ZIP file containing individual PDF files for each selected student.</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> Large downloads may take a few moments to process.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBulkDownload">
                    <i class="fas fa-download"></i> Download ZIP
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let selectedStudents = new Set();
    
    // Select All functionality
    $('#selectAll, #selectAllHeader').change(function() {
        const isChecked = $(this).is(':checked');
        $('.student-checkbox').prop('checked', isChecked);
        
        if (isChecked) {
            $('.student-checkbox').each(function() {
                selectedStudents.add($(this).val());
            });
        } else {
            selectedStudents.clear();
        }
        
        updateUI();
    });
    
    // Individual checkbox change
    $('.student-checkbox').change(function() {
        const studentId = $(this).val();
        
        if ($(this).is(':checked')) {
            selectedStudents.add(studentId);
        } else {
            selectedStudents.delete(studentId);
        }
        
        updateUI();
    });
    
    // Clear selection
    $('#clearSelection').click(function() {
        selectedStudents.clear();
        $('.student-checkbox, #selectAll, #selectAllHeader').prop('checked', false);
        updateUI();
    });
    
    // Download selected
    $('#downloadSelectedBtn').click(function() {
        if (selectedStudents.size === 0) {
            alert('Please select at least one student.');
            return;
        }
        
        $('#modalStudentCount').text(selectedStudents.size);
        $('#bulkDownloadModal').modal('show');
    });
    
    // Download all
    $('#downloadAllBtn').click(function() {
        const totalStudents = {{ $students->total() }};
        $('#modalStudentCount').text(totalStudents);
        $('#bulkDownloadModal').modal('show');
    });
    
    // Confirm bulk download
    $('#confirmBulkDownload').click(function() {
        const form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("admin.student-info.bulk-pdf") }}'
        });
        
        // Add CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));
        
        // Add student IDs
        if (selectedStudents.size > 0) {
            // Download selected
            selectedStudents.forEach(function(studentId) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'student_ids[]',
                    'value': studentId
                }));
            });
        } else {
            // Download all - add all student IDs from current page
            $('.student-checkbox').each(function() {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'student_ids[]',
                    'value': $(this).val()
                }));
            });
        }
        
        $('body').append(form);
        form.submit();
        form.remove();
        
        $('#bulkDownloadModal').modal('hide');
    });
    
    function updateUI() {
        const count = selectedStudents.size;
        $('#selectedCount').text(count + ' selected');
        $('#downloadSelectedBtn').prop('disabled', count === 0);
        
        // Update select all checkbox state
        const totalCheckboxes = $('.student-checkbox').length;
        const checkedCheckboxes = $('.student-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#selectAll, #selectAllHeader').prop('checked', false).prop('indeterminate', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll, #selectAllHeader').prop('checked', true).prop('indeterminate', false);
        } else {
            $('#selectAll, #selectAllHeader').prop('checked', false).prop('indeterminate', true);
        }
    }
    
    // Initialize UI
    updateUI();
});
</script>
@endsection
