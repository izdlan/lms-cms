@extends('layouts.app')

@section('title', 'Invalid Certificate')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white text-center">
                    <h3><i class="fas fa-exclamation-triangle"></i> Certificate Verification</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-danger">Certificate Not Found</h4>
                    </div>

                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Invalid Certificate</h5>
                        <p class="mb-0">
                            The certificate number <strong>{{ $certificateNumber }}</strong> could not be found in our records.
                            This may be due to:
                        </p>
                        <ul class="mt-2 mb-0">
                            <li>Incorrect certificate number</li>
                            <li>Certificate has been revoked</li>
                            <li>Certificate does not exist</li>
                        </ul>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            If you believe this is an error, please contact the administration.
                        </p>
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="mailto:admin@olympia-education.com" class="btn btn-outline-primary ml-2">
                            <i class="fas fa-envelope"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

