@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <h1>Edit Student</h1>
                    <div class="header-actions">
                        <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" width="16" height="16"></i>
                            Back to Students
                        </a>
                    </div>
                </div>

                @if(session('errors'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach(session('errors')->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Edit Student Form -->
                <div class="card">
                    <div class="card-header">
                        <h5>Student Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.students.update', $student) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $student->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ic" class="form-label">IC Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('ic') is-invalid @enderror" 
                                               id="ic" name="ic" value="{{ old('ic', $student->ic) }}" required>
                                        @error('ic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="email" class="form-label">Email Address</label>
                                         <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                id="email" name="email" value="{{ old('email', $student->email) }}">
                                         @error('email')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="student_email" class="form-label">Student Email</label>
                                         <input type="email" class="form-control @error('student_email') is-invalid @enderror" 
                                                id="student_email" name="student_email" value="{{ old('student_email', $student->student_email) }}">
                                         @error('student_email')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" 
                                          placeholder="Enter student's address">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Spreadsheet Fields -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status">
                                            <option value="">Select Status</option>
                                            <option value="In progress" {{ old('status', $student->status) == 'In progress' ? 'selected' : '' }}>In progress</option>
                                            <option value="Withdrawn" {{ old('status', $student->status) == 'Withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                            <option value="Completed" {{ old('status', $student->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-control @error('category') is-invalid @enderror" 
                                                id="category" name="category">
                                            <option value="">Select Category</option>
                                            <option value="LOCAL" {{ old('category', $student->category) == 'LOCAL' ? 'selected' : '' }}>LOCAL</option>
                                            <option value="INTERNATIONAL" {{ old('category', $student->category) == 'INTERNATIONAL' ? 'selected' : '' }}>INTERNATIONAL</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="contact_no" class="form-label">Contact Number</label>
                                         <input type="text" class="form-control @error('contact_no') is-invalid @enderror" 
                                                id="contact_no" name="contact_no" value="{{ old('contact_no', $student->contact_no) }}" 
                                                placeholder="Enter contact number">
                                         @error('contact_no')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
                             </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="programme_name" class="form-label">Programme Name</label>
                                        <input type="text" class="form-control @error('programme_name') is-invalid @enderror" 
                                               id="programme_name" name="programme_name" value="{{ old('programme_name', $student->programme_name) }}" 
                                               placeholder="Enter programme name">
                                        @error('programme_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="programme_code" class="form-label">Programme Code</label>
                                        <input type="text" class="form-control @error('programme_code') is-invalid @enderror" 
                                               id="programme_code" name="programme_code" value="{{ old('programme_code', $student->programme_code) }}" 
                                               placeholder="Enter programme code">
                                        @error('programme_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">Student ID</label>
                                        <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                               id="student_id" name="student_id" value="{{ old('student_id', $student->student_id) }}" 
                                               placeholder="Enter student ID">
                                        @error('student_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="col_ref_no" class="form-label">College Reference Number</label>
                                        <input type="text" class="form-control @error('col_ref_no') is-invalid @enderror" 
                                               id="col_ref_no" name="col_ref_no" value="{{ old('col_ref_no', $student->col_ref_no) }}" 
                                               placeholder="Enter college reference number">
                                        @error('col_ref_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_portal" class="form-label">Student Portal</label>
                                        <input type="text" class="form-control @error('student_portal') is-invalid @enderror" 
                                               id="student_portal" name="student_portal" value="{{ old('student_portal', $student->student_portal) }}" 
                                               placeholder="Enter student portal information">
                                        @error('student_portal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="semester_entry" class="form-label">Semester Entry</label>
                                        <input type="text" class="form-control @error('semester_entry') is-invalid @enderror" 
                                               id="semester_entry" name="semester_entry" value="{{ old('semester_entry', $student->semester_entry) }}" 
                                               placeholder="Enter semester entry">
                                        @error('semester_entry')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="research_title" class="form-label">Research Title</label>
                                <textarea class="form-control @error('research_title') is-invalid @enderror" 
                                          id="research_title" name="research_title" rows="3" 
                                          placeholder="Enter research title">{{ old('research_title', $student->research_title) }}</textarea>
                                @error('research_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="supervisor" class="form-label">Supervisor</label>
                                        <input type="text" class="form-control @error('supervisor') is-invalid @enderror" 
                                               id="supervisor" name="supervisor" value="{{ old('supervisor', $student->supervisor) }}" 
                                               placeholder="Enter supervisor name">
                                        @error('supervisor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="external_examiner" class="form-label">External Examiner</label>
                                        <input type="text" class="form-control @error('external_examiner') is-invalid @enderror" 
                                               id="external_examiner" name="external_examiner" value="{{ old('external_examiner', $student->external_examiner) }}" 
                                               placeholder="Enter external examiner">
                                        @error('external_examiner')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="internal_examiner" class="form-label">Internal Examiner</label>
                                        <input type="text" class="form-control @error('internal_examiner') is-invalid @enderror" 
                                               id="internal_examiner" name="internal_examiner" value="{{ old('internal_examiner', $student->internal_examiner) }}" 
                                               placeholder="Enter internal examiner">
                                        @error('internal_examiner')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="col_date" class="form-label">COL Date</label>
                                        <input type="date" class="form-control @error('col_date') is-invalid @enderror" 
                                               id="col_date" name="col_date" value="{{ old('col_date', $student->col_date) }}">
                                        @error('col_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="programme_intake" class="form-label">Programme Intake</label>
                                        <input type="text" class="form-control @error('programme_intake') is-invalid @enderror" 
                                               id="programme_intake" name="programme_intake" value="{{ old('programme_intake', $student->programme_intake) }}" 
                                               placeholder="Enter programme intake">
                                        @error('programme_intake')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="date_of_commencement" class="form-label">Date of Commencement</label>
                                        <input type="date" class="form-control @error('date_of_commencement') is-invalid @enderror" 
                                               id="date_of_commencement" name="date_of_commencement" value="{{ old('date_of_commencement', $student->date_of_commencement) }}">
                                        @error('date_of_commencement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="total_fees" class="form-label">Total Fees</label>
                                        <input type="number" class="form-control @error('total_fees') is-invalid @enderror" 
                                               id="total_fees" name="total_fees" value="{{ old('total_fees', $student->total_fees) }}" 
                                               step="0.01" placeholder="Enter total fees">
                                        @error('total_fees')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="transaction_month" class="form-label">Transaction Month</label>
                                        <input type="text" class="form-control @error('transaction_month') is-invalid @enderror" 
                                               id="transaction_month" name="transaction_month" value="{{ old('transaction_month', $student->transaction_month) }}" 
                                               placeholder="Enter transaction month">
                                        @error('transaction_month')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="pic" class="form-label">PIC</label>
                                        <input type="text" class="form-control @error('pic') is-invalid @enderror" 
                                               id="pic" name="pic" value="{{ old('pic', $student->pic) }}" 
                                               placeholder="Enter PIC">
                                        @error('pic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                          id="remarks" name="remarks" rows="3" 
                                          placeholder="Enter remarks">{{ old('remarks', $student->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="courses" class="form-label">Courses</label>
                                <input type="text" class="form-control @error('courses') is-invalid @enderror" 
                                       id="courses" name="courses" value="{{ old('courses', $student->courses ? implode(', ', $student->courses) : '') }}" 
                                       placeholder="Enter courses separated by commas (e.g., Math, Science, English)">
                                <div class="form-text">Separate multiple courses with commas</div>
                                @error('courses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Created At</label>
                                        <input type="text" class="form-control" value="{{ $student->created_at->format('M d, Y H:i') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Updated</label>
                                        <input type="text" class="form-control" value="{{ $student->updated_at->format('M d, Y H:i') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i data-feather="info" width="16" height="16"></i>
                                <strong>Note:</strong> The student's password is set to their IC number. They can change it after logging in.
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-danger" onclick="deleteStudent({{ $student->id }})">
                                        <i data-feather="trash-2" width="16" height="16"></i>
                                        Delete Student
                                    </button>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.students') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="save" width="16" height="16"></i>
                                        Update Student
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this student? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
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

.header-actions {
    display: flex;
    gap: 0.5rem;
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
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-control[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
}

.alert-info {
    background-color: #e6f3ff;
    border-color: #b3d9ff;
    color: #0066cc;
}

.alert-info i {
    margin-right: 0.5rem;
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #667eea;
    border-color: #667eea;
}

.btn-primary:hover {
    background-color: #5a67d8;
    border-color: #5a67d8;
}

.btn-outline-secondary {
    color: #718096;
    border-color: #e2e8f0;
}

.btn-outline-secondary:hover {
    background-color: #f7fafc;
    border-color: #cbd5e0;
}

.btn-danger {
    background-color: #e53e3e;
    border-color: #e53e3e;
}

.btn-danger:hover {
    background-color: #c53030;
    border-color: #c53030;
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
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof safeFeatherReplace === 'function') {
        safeFeatherReplace();
    } else {
        try {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.warn('Feather icons error in edit student:', error);
        }
    }
});

function deleteStudent(studentId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/students/${studentId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
