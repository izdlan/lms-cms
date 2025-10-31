@extends('layouts.app')

@section('title', 'My Payments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">My Payments</h4>
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                        <i data-feather="plus" width="16" height="16"></i>
                        Create Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Payment History</h5>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>#{{ $payment->id }}</td>
                                            <td>{{ $payment->description }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>
                                                @if($payment->isPaid())
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($payment->isPending())
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($payment->isFailed())
                                                    <span class="badge bg-danger">Failed</span>
                                                @elseif($payment->isCancelled())
                                                    <span class="badge bg-secondary">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->payment_method)
                                                    {{ ucfirst($payment->payment_method) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($payment->isPending() && !$payment->isExpired())
                                                    <a href="{{ $payment->payment_url }}" target="_blank" class="btn btn-sm btn-primary">
                                                        <i data-feather="external-link" width="14" height="14"></i>
                                                        Pay Now
                                                    </a>
                                                @elseif($payment->isPaid())
                                                    <span class="text-success">
                                                        <i data-feather="check-circle" width="16" height="16"></i>
                                                        Completed
                                                    </span>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        Expired
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($payments->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $payments->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i data-feather="credit-card" width="48" height="48" class="text-muted mb-3"></i>
                            <h5 class="text-muted">No payments found</h5>
                            <p class="text-muted">You haven't made any payments yet.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                                Create Your First Payment
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Payment Modal -->
<div class="modal fade" id="createPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createPaymentForm">
                    @csrf
                    <div class="mb-3">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select class="form-select" id="payment_type" name="payment_type" required>
                            <option value="">Select payment type</option>
                            <option value="general">General Payment</option>
                            <option value="course">Course Fee</option>
                        </select>
                    </div>

                    <div id="course_selection" class="mb-3" style="display: none;">
                        <label for="course_id" class="form-label">Select Course</label>
                        <select class="form-select" id="course_id" name="course_id">
                            <option value="">Select course</option>
                            <!-- Courses will be loaded via AJAX -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (RM)</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div id="reference_field" class="mb-3" style="display: none;">
                        <label for="reference" class="form-label">Reference</label>
                        <input type="text" class="form-control" id="reference" name="reference" placeholder="Optional reference">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createPaymentBtn">Create Payment</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentTypeSelect = document.getElementById('payment_type');
    const courseSelection = document.getElementById('course_selection');
    const referenceField = document.getElementById('reference_field');
    const createPaymentBtn = document.getElementById('createPaymentBtn');
    const createPaymentForm = document.getElementById('createPaymentForm');

    // Handle payment type change
    paymentTypeSelect.addEventListener('change', function() {
        if (this.value === 'course') {
            courseSelection.style.display = 'block';
            referenceField.style.display = 'none';
            loadCourses();
        } else if (this.value === 'general') {
            courseSelection.style.display = 'none';
            referenceField.style.display = 'block';
        } else {
            courseSelection.style.display = 'none';
            referenceField.style.display = 'none';
        }
    });

    // Load courses for course payment
    function loadCourses() {
        // This would typically load from an API endpoint
        // For now, we'll add some sample courses
        const courseSelect = document.getElementById('course_id');
        courseSelect.innerHTML = '<option value="">Select course</option>';
        
        // Add sample courses (replace with actual API call)
        const courses = [
            { id: 1, name: 'Introduction to Programming' },
            { id: 2, name: 'Database Management' },
            { id: 3, name: 'Web Development' }
        ];
        
        courses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.id;
            option.textContent = course.name;
            courseSelect.appendChild(option);
        });
    }

    // Handle create payment
    createPaymentBtn.addEventListener('click', function() {
        const formData = new FormData(createPaymentForm);
        const paymentType = formData.get('payment_type');
        
        if (!paymentType) {
            alert('Please select a payment type');
            return;
        }

        this.disabled = true;
        this.textContent = 'Creating...';

        const url = paymentType === 'course' ? 
            '{{ route("student.payments.course") }}' : 
            '{{ route("student.payments.general") }}';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to payment URL
                window.open(data.payment_url, '_blank');
                // Close modal and reload page
                bootstrap.Modal.getInstance(document.getElementById('createPaymentModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = 'Create Payment';
        });
    });
});
</script>
@endpush
