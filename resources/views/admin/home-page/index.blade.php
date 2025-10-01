@extends('layouts.admin')

@section('title', 'Home Page Management')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Home Page Management</h1>
                            <p>Manage home page content sections</p>
                        </div>
                        <a href="{{ route('admin.home-page.create') }}" class="btn btn-primary">
                            <i data-feather="plus" width="20" height="20"></i>
                            Add New Section
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i data-feather="check-circle" width="20" height="20"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Home Page Sections</h5>
                    </div>
                    <div class="card-body">
                        @if($contents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Section Name</th>
                                            <th>Title</th>
                                            <th>Sort Order</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contents as $content)
                                            <tr>
                                                <td>
                                                    <strong>{{ $content->section_name }}</strong>
                                                </td>
                                                <td>{{ $content->title ?: 'No title' }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $content->sort_order }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $content->is_active ? 'success' : 'danger' }}">
                                                        {{ $content->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>{{ $content->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.home-page.edit', $content) }}" class="btn btn-sm btn-outline-primary">
                                                            <i data-feather="edit" width="16" height="16"></i>
                                                        </a>
                                                        <form action="{{ route('admin.home-page.destroy', $content) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this section?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i data-feather="trash-2" width="16" height="16"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i data-feather="home" width="48" height="48" class="text-muted mb-3"></i>
                                <h5 class="text-muted">No home page sections found</h5>
                                <p class="text-muted">Create your first home page section to get started.</p>
                                <a href="{{ route('admin.home-page.create') }}" class="btn btn-primary">
                                    <i data-feather="plus" width="20" height="20"></i>
                                    Add First Section
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Ensure action buttons are visible */
.btn-group {
    display: flex !important;
    gap: 0.25rem;
}

.btn-group .btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 32px !important;
    height: 32px !important;
    padding: 0.375rem !important;
    border: 1px solid !important;
    border-radius: 0.375rem !important;
    text-decoration: none !important;
    transition: all 0.15s ease-in-out !important;
}

.btn-group .btn:hover {
    text-decoration: none !important;
    transform: translateY(-1px) !important;
}

.btn-outline-primary {
    color: #0d6efd !important;
    border-color: #0d6efd !important;
    background-color: transparent !important;
}

.btn-outline-primary:hover {
    color: #fff !important;
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}

.btn-outline-danger {
    color: #dc3545 !important;
    border-color: #dc3545 !important;
    background-color: transparent !important;
}

.btn-outline-danger:hover {
    color: #fff !important;
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

/* Ensure icons are visible */
.btn i[data-feather] {
    width: 16px !important;
    height: 16px !important;
    display: inline-block !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Home page DOM loaded');
    console.log('Feather available:', typeof feather !== 'undefined');
    console.log('SafeFeatherReplace available:', typeof safeFeatherReplace !== 'undefined');
    
    // Initialize Feather icons
    setTimeout(() => {
        console.log('Initializing Feather icons...');
        if (typeof safeFeatherReplace === 'function') {
            console.log('Using safeFeatherReplace');
            safeFeatherReplace();
        } else if (typeof feather !== 'undefined') {
            console.log('Using feather.replace');
            feather.replace();
        } else {
            console.error('Feather icons not available');
        }
        
        // Check if buttons are visible after initialization
        setTimeout(() => {
            const actionButtons = document.querySelectorAll('.btn-group .btn');
            console.log('Action buttons found:', actionButtons.length);
            actionButtons.forEach((btn, index) => {
                console.log(`Button ${index + 1}:`, btn.outerHTML);
            });
        }, 500);
    }, 200);
});
</script>
@endpush