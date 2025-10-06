@extends('layouts.student')

@section('title', 'Payment Successful')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i data-feather="check-circle" width="64" height="64" class="text-success"></i>
                    </div>
                    <h3 class="text-success mb-3">Payment Successful!</h3>
                    <p class="text-muted mb-4">
                        Your payment has been processed successfully. You will receive a confirmation email shortly.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('student.payments') }}" class="btn btn-primary">
                            <i data-feather="credit-card" width="16" height="16"></i>
                            View All Payments
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                            <i data-feather="home" width="16" height="16"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
