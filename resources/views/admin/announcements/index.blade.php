@extends('layouts.admin')

@section('title', 'Announcements Management')

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
                            <h1>Announcements Management</h1>
                            <p>Manage public announcements for the website</p>
                        </div>
                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                            <i data-feather="plus" width="20" height="20"></i>
                            Add New Announcement
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
                        <h5 class="mb-0">All Announcements</h5>
                    </div>
                    <div class="card-body">
                        @if($announcements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Published</th>
                                            <th>Author</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($announcements as $announcement)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $announcement->title }}</strong>
                                                        @if($announcement->is_featured)
                                                            <span class="badge bg-warning ms-2">Featured</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ Str::limit($announcement->content, 100) }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($announcement->category) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $announcement->priority === 'high' ? 'danger' : ($announcement->priority === 'medium' ? 'warning' : 'success') }}">
                                                        {{ ucfirst($announcement->priority) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $announcement->is_active ? 'success' : 'danger' }}">
                                                        {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($announcement->published_at)
                                                        {{ $announcement->published_at->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">Draft</span>
                                                    @endif
                                                </td>
                                                <td>{{ $announcement->admin->name }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-primary">
                                                            <i data-feather="edit" width="16" height="16"></i>
                                                        </a>
                                                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
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
                                <i data-feather="bell" width="48" height="48" class="text-muted mb-3"></i>
                                <h5 class="text-muted">No announcements found</h5>
                                <p class="text-muted">Create your first announcement to get started.</p>
                                <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                                    <i data-feather="plus" width="20" height="20"></i>
                                    Add First Announcement
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
