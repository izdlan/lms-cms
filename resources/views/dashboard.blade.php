@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">Welcome, {{ Auth::guard('student')->user()->name }}!</h1>
                    <p class="dashboard-subtitle">Student Dashboard</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i data-feather="book-open" width="24" height="24"></i>
                    </div>
                    <h3>My Courses</h3>
                    <p>View and manage your enrolled courses</p>
                    <a href="/classes" class="btn btn-primary">View Courses</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i data-feather="user" width="24" height="24"></i>
                    </div>
                    <h3>Profile</h3>
                    <p>Update your personal information</p>
                    <a href="#" class="btn btn-outline-primary">Edit Profile</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i data-feather="settings" width="24" height="24"></i>
                    </div>
                    <h3>Settings</h3>
                    <p>Manage your account settings</p>
                    <a href="#" class="btn btn-outline-primary">Settings</a>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="dashboard-info">
                    <h4>Student Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>IC Number:</strong> {{ Auth::guard('student')->user()->ic_number }}</p>
                            <p><strong>Email:</strong> {{ Auth::guard('student')->user()->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phone:</strong> {{ Auth::guard('student')->user()->phone ?? 'Not provided' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge badge-{{ Auth::guard('student')->user()->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst(Auth::guard('student')->user()->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i data-feather="log-out" width="16" height="16"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.dashboard-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.dashboard-header {
    text-align: center;
    margin-bottom: 3rem;
}

.dashboard-title {
    color: #0056d2;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.dashboard-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
}

.dashboard-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin-bottom: 2rem;
    transition: transform 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
}

.card-icon {
    background: #0056d2;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.dashboard-card h3 {
    color: #333;
    margin-bottom: 1rem;
}

.dashboard-card p {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.dashboard-info {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.dashboard-info h4 {
    color: #0056d2;
    margin-bottom: 1.5rem;
}

.dashboard-info p {
    margin-bottom: 0.5rem;
    color: #333;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger {
    background: #dc3545;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
