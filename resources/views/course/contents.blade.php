@extends('layouts.course')

@section('title', 'Course Contents')

@section('content')
<div class="course-header">
    <h1>Course Content : {{ $courseId }}</h1>
    <p>Access all course materials and resources</p>
</div>

<div class="row">
    <div class="col-12">
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control search-bar" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Instruction Note -->
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="fas fa-download me-2"></i>
            <strong>Please use PC or laptop to download the file other than PDF file.</strong>
        </div>

        <!-- Home Icon -->
        <div class="mb-4">
            <a href="/maintenance" class="btn btn-outline-primary">
                <i class="fas fa-home me-2"></i>
                Home
            </a>
        </div>

        <!-- Contents Table -->
        @if(count($contents) > 0)
            <div class="content-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>No. of files</th>
                            <th>Created By</th>
                            <th>Date Uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contents as $content)
                            <tr class="{{ $content['is_highlighted'] ? 'highlighted' : '' }}">
                                <td>
                                    <div class="content-name">
                                        <i class="fas fa-folder text-warning"></i>
                                        {{ $content['name'] }}
                                    </div>
                                </td>
                                <td>
                                    <span class="content-files">{{ $content['files_count'] }}</span>
                                </td>
                                <td>
                                    <div class="content-author">{{ $content['created_by'] }}</div>
                                </td>
                                <td>
                                    <div class="content-date">{{ $content['date_uploaded'] }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Content Available</h3>
                <p>There are no course contents available at the moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.querySelector('.search-bar');
    const tableRows = document.querySelectorAll('tbody tr');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        tableRows.forEach(row => {
            const contentName = row.querySelector('.content-name').textContent.toLowerCase();
            const createdBy = row.querySelector('.content-author').textContent.toLowerCase();
            
            if (contentName.includes(searchTerm) || createdBy.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    console.log('Course Contents page loaded');
});
</script>
@endpush
