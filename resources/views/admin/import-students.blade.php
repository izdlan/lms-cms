@extends('layouts.app')

@section('title', 'Import Students')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4>Admin Panel</h4>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i data-feather="home" width="20" height="20"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.students') }}" class="nav-link">
                        <i data-feather="users" width="20" height="20"></i>
                        Students
                    </a>
                    <a href="{{ route('admin.import') }}" class="nav-link active">
                        <i data-feather="upload" width="20" height="20"></i>
                        Import Students
                    </a>
                    <a href="{{ route('admin.sync') }}" class="nav-link">
                        <i data-feather="refresh-cw" width="20" height="20"></i>
                        Sync from Excel/CSV
                    </a>
                    <a href="{{ route('admin.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-feather="log-out" width="20" height="20"></i>
                        Logout
                    </a>
                </nav>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>Import Students</h1>
                    <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" width="16" height="16"></i>
                        Back to Students
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Import Form -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Upload Excel/CSV File</h5>
                            </div>
                            <div class="card-body">
                        <form action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="file" class="form-label">Select Excel/CSV File</label>
                                <input type="file" 
                                       class="form-control @error('file') is-invalid @enderror" 
                                       id="file" 
                                       name="file" 
                                       accept=".xlsx,.xls,.csv,.txt"
                                       required>
                                @error('file')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    Supported formats: .xlsx, .xls, .csv, .txt
                                </div>
                            </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i data-feather="upload" width="20" height="20"></i>
                                            Import Students
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>File Format Requirements</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Your Excel/CSV file should have the following columns:</p>
                                
                                <div class="requirements-list">
                                    <div class="requirement-item">
                                        <i data-feather="check-circle" width="16" height="16" class="text-success"></i>
                                        <span><strong>name</strong> - Student's full name</span>
                                    </div>
                                    <div class="requirement-item">
                                        <i data-feather="check-circle" width="16" height="16" class="text-success"></i>
                                        <span><strong>ic</strong> - IC number (used as password)</span>
                                    </div>
                                    <div class="requirement-item">
                                        <i data-feather="check-circle" width="16" height="16" class="text-success"></i>
                                        <span><strong>email</strong> - Email address</span>
                                    </div>
                                    <div class="requirement-item">
                                        <i data-feather="check-circle" width="16" height="16" class="text-success"></i>
                                        <span><strong>number</strong> - Phone number (optional)</span>
                                    </div>
                                    <div class="requirement-item">
                                        <i data-feather="check-circle" width="16" height="16" class="text-success"></i>
                                        <span><strong>courses</strong> - Comma-separated courses (optional)</span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h6>Important Notes:</h6>
                                    <ul class="small text-muted">
                                        <li>First row should contain column headers</li>
                                        <li>Excel files (.xlsx, .xls) and CSV files (.csv, .txt) are supported</li>
                                        <li>IC number will be used as the default password</li>
                                        <li>Students can change their password via email</li>
                                        <li>Existing students will be updated if IC or email matches</li>
                                        <li>For Excel files, data should be in the first sheet</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Sample Download -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>Sample Files</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Download sample files to see the correct format:</p>
                                <div class="d-grid gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm" onclick="downloadSampleCSV()">
                                        <i data-feather="download" width="16" height="16"></i>
                                        Download CSV Sample
                                    </a>
                                    <a href="#" class="btn btn-outline-success btn-sm" onclick="downloadSampleExcel()">
                                        <i data-feather="download" width="16" height="16"></i>
                                        Download Excel Sample
                                    </a>
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

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #a0aec0;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: #4a5568;
    color: white;
}

.nav-link.active {
    background: #667eea;
    color: white;
    border-left-color: #5a67d8;
}

.nav-link i {
    margin-right: 0.75rem;
}

.main-content {
    padding: 2rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    color: #2d3748;
    font-weight: bold;
    margin: 0;
}

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.card-header h5 {
    margin: 0;
    color: #2d3748;
    font-weight: bold;
}

.card-body {
    padding: 1.5rem;
}

.requirements-list {
    margin-bottom: 1rem;
}

.requirement-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.requirement-item i {
    margin-right: 0.5rem;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // File input change handler
    const fileInput = document.getElementById('file');
    const form = document.getElementById('importForm');
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const validTypes = [
                    'text/csv',
                    'text/plain',
                    'application/csv',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                ];
                
                const validExtensions = ['.csv', '.txt', '.xlsx', '.xls'];
                const fileExtension = file.name.toLowerCase().substring(file.name.lastIndexOf('.'));
                
                if (!validTypes.includes(file.type) && !validExtensions.includes(fileExtension)) {
                    Swal.fire({
                        title: 'Invalid File Type',
                        text: 'Please select a valid Excel (.xlsx, .xls) or CSV (.csv, .txt) file.',
                        icon: 'error'
                    });
                    this.value = '';
                    return;
                }
                
                // Show file name
                const fileName = file.name;
                console.log('Selected file:', fileName);
                
                // Show success message
                Swal.fire({
                    title: 'File Selected',
                    text: `Selected: ${fileName}`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }
    
    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            if (!fileInput.files.length) {
                e.preventDefault();
                Swal.fire({
                    title: 'No File Selected',
                    text: 'Please select a file to upload.',
                    icon: 'warning'
                });
                return;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-feather="loader" width="20" height="20"></i> Importing...';
            feather.replace();
            
            // Show loading alert
            Swal.fire({
                title: 'Importing Students',
                text: 'Please wait while we process your file...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
});

function downloadSampleCSV() {
    // Create a sample CSV file content
    const sampleData = [
        ['name', 'ic', 'email', 'number', 'courses'],
        ['John Doe', '123456789012', 'john@example.com', '0123456789', 'Mathematics,Physics'],
        ['Jane Smith', '987654321098', 'jane@example.com', '0987654321', 'Chemistry,Biology'],
        ['Ahmad Ali', '112233445566', 'ahmad@example.com', '0112233445', 'English,History']
    ];
    
    // Convert to CSV
    const csvContent = sampleData.map(row => row.join(',')).join('\n');
    
    // Create and download file
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'students_sample.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function downloadSampleExcel() {
    // Create a sample Excel file content (simplified)
    const sampleData = [
        ['name', 'ic', 'email', 'number', 'courses'],
        ['John Doe', '123456789012', 'john@example.com', '0123456789', 'Mathematics,Physics'],
        ['Jane Smith', '987654321098', 'jane@example.com', '0987654321', 'Chemistry,Biology'],
        ['Ahmad Ali', '112233445566', 'ahmad@example.com', '0112233445', 'English,History']
    ];
    
    // For Excel, we'll create a CSV file with .xlsx extension
    // In a real implementation, you'd use a library like SheetJS
    const csvContent = sampleData.map(row => row.join(',')).join('\n');
    
    // Create and download file
    const blob = new Blob([csvContent], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'students_sample.xlsx';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endpush
