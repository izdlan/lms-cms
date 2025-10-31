@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i data-feather="x-circle" width="64" height="64" class="text-danger"></i>
                    </div>
                    <h3 class="text-danger mb-3">Payment Failed</h3>
                    <p class="text-muted mb-4">
                        @if(session('error'))
                            {{ session('error') }}
                        @else
                            Your payment could not be processed. Please try again or contact support if the problem persists.
                        @endif
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('student.payments') }}" class="btn btn-primary">
                            <i data-feather="credit-card" width="16" height="16"></i>
                            Try Again
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
