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
                        <form method="POST" action="{{ route('admin.student-info.bulk-pdf') }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="download_all" value="true">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download"></i> Download All PDFs
                            </button>
                        </form>
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
// Simple test to confirm page loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Student Info page loaded successfully');
    console.log('Download All button is now a direct form - no JavaScript needed');
});
</script>
@endsection
