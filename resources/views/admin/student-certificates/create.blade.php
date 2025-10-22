@extends('layouts.admin')

@section('title', 'Generate Certificates from Excel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-excel"></i>
                        Generate Certificates from Excel
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.student-certificates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Certificates
                        </a>
                    </div>
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

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Upload Excel File</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.student-certificates.generate') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="excel_file">Excel File <span class="text-danger">*</span></label>
                                            <div class="custom-file">
                                                <input type="file" 
                                                       class="custom-file-input @error('excel_file') is-invalid @enderror" 
                                                       id="excel_file" 
                                                       name="excel_file" 
                                                       accept=".xlsx,.xls,.csv"
                                                       required>
                                                <label class="custom-file-label" for="excel_file">Choose Excel file...</label>
                                            </div>
                                            @error('excel_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Supported formats: .xlsx, .xls, .csv (Max: 10MB)
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="fas fa-upload"></i>
                                                Generate Certificates
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                                <i class="fas fa-undo"></i>
                                                Reset
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Instructions</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Excel File Requirements:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> Must contain a column with student names</li>
                                        <li><i class="fas fa-check text-success"></i> Supported column names: "Name", "Student Name", "Full Name", "Nama"</li>
                                        <li><i class="fas fa-check text-success"></i> First row should contain headers</li>
                                        <li><i class="fas fa-check text-success"></i> Empty rows will be skipped</li>
                                    </ul>

                                    <hr>

                                    <h6>Template Information:</h6>
                                    <p class="mb-1"><strong>Template:</strong> E-Certs - Class RM</p>
                                    <p class="mb-1"><strong>Placeholder:</strong> ${Student name}</p>
                                    <p class="mb-0"><strong>Date:</strong> 18 October 2025</p>

                                    <hr>

                                    <h6>Process:</h6>
                                    <ol class="mb-0">
                                        <li>Upload Excel file</li>
                                        <li>System reads student names</li>
                                        <li>Generates certificates using template</li>
                                        <li>Creates downloadable .docx files</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Template Preview</h5>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fas fa-file-word fa-3x text-primary mb-2"></i>
                                    <p class="mb-1"><strong>E-Certs - Class RM.docx</strong></p>
                                    <small class="text-muted">Research Methodology Seminar Certificate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // File input change
    $('#excel_file').change(function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName || 'Choose Excel file...');
        
        // Enable submit button if file is selected
        if (fileName) {
            $('#submitBtn').prop('disabled', false);
        }
    });

    // Form submission
    $('#uploadForm').submit(function() {
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });
});

function resetForm() {
    $('#uploadForm')[0].reset();
    $('.custom-file-label').text('Choose Excel file...');
    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-upload"></i> Generate Certificates');
}
</script>
@endpush
