@extends('layouts.app')

@section('title', 'Certificate Verification')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h3><i class="fas fa-certificate"></i> Certificate Verification</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-success">Certificate Verified Successfully</h4>
                    </div>

                    <div class="certificate-details">
                        <h5 class="text-primary mb-3">Certificate Details</h5>
                        <table class="table table-bordered">
                            <tr>
                                <td class="font-weight-bold">Student Name:</td>
                                <td>{{ $student->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Certificate Number:</td>
                                <td>{{ $student->certificate_number }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Programme:</td>
                                <td>{{ $student->programme_name ?? 'Not Specified' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Graduation Date:</td>
                                <td>{{ $student->graduation_date ? \Carbon\Carbon::parse($student->graduation_date)->format('d F Y') : 'Not Specified' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Student ID:</td>
                                <td>{{ $student->student_id ?? $student->id }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Verification Date:</td>
                                <td>{{ now()->format('d F Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            <i class="fas fa-shield-alt"></i>
                            This certificate has been verified and is authentic.
                        </p>
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

