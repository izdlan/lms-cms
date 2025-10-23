@extends('layouts.student')

@section('title', 'Payment Pending')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">

                    <div class="mb-4">
                        <i data-feather="clock" width="64" height="64" class="text-warning"></i>
                    </div>

                    <h3 class="text-warning mb-3">Payment Pending</h3>
                    <p class="text-muted mb-4">
                        @if(session('info'))
                            {{ session('info') }}
                        @else
                            Your payment is being processed. Please wait a moment and refresh the page to check the status.
                        @endif
                    </p>

                    <div class="d-flex gap-2 justify-content-center">
                        <button onclick="location.reload()" class="btn btn-primary">
                            <i data-feather="refresh-cw" width="16" height="16"></i>
                            Refresh Status
                        </button>
                        <a href="{{ route('student.payments') }}" class="btn btn-outline-primary">
                            <i data-feather="credit-card" width="16" height="16"></i>
                            View All Payments
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
