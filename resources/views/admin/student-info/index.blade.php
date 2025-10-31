@extends('layouts.admin')

@section('title', 'Student Information PDFs')

@section('content')
<div class="container-fluid">
    <style>
        /* Vibrant, readable admin styling for this page only */
        .card { border: 0; box-shadow: 0 10px 30px rgba(51, 65, 85, .12); }
        .card-header { padding: 1rem 1.25rem; border: 0; }
        .card-header .card-title { margin: 0; font-weight: 700; letter-spacing: .3px; }
        .card-header.color-splash {
            background: linear-gradient(120deg, #7c3aed, #3b82f6, #06b6d4);
            color: #fff;
        }

        .btn.btn-primary { background: linear-gradient(135deg,#2563eb,#7c3aed); border: none; }
        .btn.btn-success { background: linear-gradient(135deg,#16a34a,#22c55e); border: none; }
        .btn.btn-info { background: linear-gradient(135deg,#06b6d4,#3b82f6); border: none; color: #fff; }
        .btn.btn-secondary { background: #64748b; border: none; }
        .btn:focus { box-shadow: 0 0 0 .2rem rgba(124,58,237,.25); }

        .table { border-radius: 10px; overflow: hidden; }
        .table thead.thead-dark th {
            background: linear-gradient(120deg, #111827, #1f2937);
            border-color: #111827;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .table tbody tr:nth-child(odd) { background: #f8fafc; }
        .table tbody tr:hover { background: #eef2ff; box-shadow: inset 0 0 0 9999px rgba(59,130,246,.06); }
        .table td { vertical-align: middle; }

        .badge-program { font-weight: 700; padding: .4rem .6rem; border-radius: 999px; color: #0b1021; }
        .badge-emba { background: #fde68a; color: #6b4000; }
        .badge-ebba { background: #a7f3d0; color: #065f46; }
        .badge-edba { background: #c7d2fe; color: #1e3a8a; }
        .badge-unknown { background: #f3f4f6; color: #374151; }

        .chip { display: inline-block; padding: .25rem .5rem; border-radius: .5rem; font-size: .85rem; }
        .chip-email { background: #e0f2fe; color: #0369a1; }
        .chip-username { background: #fee2e2; color: #991b1b; }

        .row-selected { outline: 2px solid #7c3aed; background: #faf5ff !important; }
        #selectedCount { background: #7c3aed; color: #fff; }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header color-splash">
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
                    <!-- Search and Filter Controls -->
                    <form method="GET" action="{{ route('admin.student-info.index') }}" id="searchForm">
                    <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search by name, ID, email, program..." 
                                           value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" title="Search">
                                            <i class="fas fa-search" aria-hidden="true"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="program" class="form-control" id="programFilter">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->programme_code }}" 
                                                {{ request('program') == $program->programme_code ? 'selected' : '' }}>
                                            {{ $program->programme_name ? $program->programme_name : $program->programme_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                            <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_all" value="yes" id="showAllToggle"
                                           {{ request('show_all') == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showAllToggle">
                                        Show All
                                </label>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <button type="button" class="btn btn-sm btn-secondary" id="clearFilters">
                                    <i class="fas fa-redo"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Selection Controls -->
                    <div class="row mb-3">
                        <div class="col-md-12 text-right">
                            <span class="badge badge-info" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-sm btn-secondary ml-2" id="clearSelection">
                                Clear Selection
                            </button>
                            <button type="button" class="btn btn-sm btn-info ml-2" id="downloadByProgramBtn" disabled>
                                <i class="fas fa-download"></i> Download by Program
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
                                    <td>
                                        @php
                                            $code = strtoupper($student->programme_code ?? '');
                                            $badgeClass = 'badge-unknown';
                                            if (str_contains($code, 'EMBA')) { $badgeClass = 'badge-emba'; }
                                            elseif (str_contains($code, 'EBBA')) { $badgeClass = 'badge-ebba'; }
                                            elseif (str_contains($code, 'EDBA')) { $badgeClass = 'badge-edba'; }
                                        @endphp
                                        <span class="badge-program {{ $badgeClass }}" title="{{ $student->programme_code }}">
                                            {{ $student->programme_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="chip chip-email">{{ $student->email ?? $student->student_email ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="chip chip-username">{{ $student->ic_passport ?? $student->ic ?? 'N/A' }}</span>
                                    </td>
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('Student Info page loaded successfully');
    
    // Filter functionality
    document.getElementById('programFilter').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });
    
    document.getElementById('showAllToggle').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });
    
    // Clear filters button
    document.getElementById('clearFilters').addEventListener('click', function() {
        window.location.href = "{{ route('admin.student-info.index') }}";
    });
    
    // Download by Program functionality
    document.getElementById('downloadByProgramBtn').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        const studentIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (studentIds.length === 0) {
            alert('Please select at least one student');
            return;
        }
        
        // Get the program of selected students
        const programs = new Set();
        checkedBoxes.forEach(cb => {
            const row = cb.closest('tr');
            row.classList.add('row-selected');
            const programCell = row.querySelector('td:nth-child(4)'); // Program is in 4th column
            if (programCell) {
                programs.add(programCell.textContent.trim());
            }
        });
        
        if (programs.size > 1) {
            if (!confirm('You have selected students from multiple programs. Download all anyway?')) {
                return;
            }
        }
        
        // Create and submit form for bulk download
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('admin.student-info.bulk-pdf') }}";
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        studentIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    });
    
    // Clear Selection button
    document.getElementById('clearSelection').addEventListener('click', function() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectedCount').textContent = '0 selected';
        document.getElementById('downloadByProgramBtn').disabled = true;
    });
    
    // Toggle download by program button based on selection
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = checkedCount + ' selected';
            document.getElementById('downloadByProgramBtn').disabled = checkedCount === 0;
        });
    });
    
    // Select all in header
    document.getElementById('selectAllHeader').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        const checkedCount = this.checked ? checkboxes.length : 0;
        document.getElementById('selectedCount').textContent = checkedCount + ' selected';
        document.getElementById('downloadByProgramBtn').disabled = checkedCount === 0;
    });
});
</script>
@endsection
