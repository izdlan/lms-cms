@extends('layouts.finance-admin')

@section('title', 'Create Invoice | Finance Admin | Olympia Education')

@section('content')
<div class="finance-admin-dashboard">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create Invoice</h1>
                <p class="page-subtitle">Create a new invoice for {{ $student->name }}</p>
            </div>
            <div>
                <a href="{{ route('finance-admin.student.show', $student->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Student
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-invoice me-2"></i>Invoice Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('finance-admin.store-invoice', $student->id) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bill_type" class="form-label">Bill Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="bill_type" name="bill_type" required>
                                        <option value="">Select Bill Type</option>
                                        <option value="Tuition Fee" {{ old('bill_type') == 'Tuition Fee' ? 'selected' : '' }}>Tuition Fee</option>
                                        <option value="EET Fee" {{ old('bill_type') == 'EET Fee' ? 'selected' : '' }}>EET Fee</option>
                                        <option value="Registration Fee" {{ old('bill_type') == 'Registration Fee' ? 'selected' : '' }}>Registration Fee</option>
                                        <option value="Late Payment Fee" {{ old('bill_type') == 'Late Payment Fee' ? 'selected' : '' }}>Late Payment Fee</option>
                                        <option value="Other" {{ old('bill_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('bill_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="session" class="form-label">Academic Session <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="session" name="session" 
                                           value="{{ old('session', now()->year . now()->format('m')) }}" required>
                                    @error('session')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (RM) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           step="0.01" min="0.01" value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" 
                                           value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                                    @error('due_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Enter invoice description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Internal notes (not visible to student)...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('finance-admin.student.show', $student->id) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($student->profile_picture)
                            <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                                 alt="Profile" class="rounded-circle me-3" width="50" height="50">
                        @else
                            <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $student->name }}</h6>
                            <small class="text-muted">{{ $student->email }}</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Student ID</small>
                            <div>{{ $student->student_id ?? '-' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">IC Number</small>
                            <div>{{ $student->ic }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Quick Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Invoice will be automatically generated with a unique number</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Student will be able to view and pay the invoice</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Receipt will be auto-generated after payment</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
