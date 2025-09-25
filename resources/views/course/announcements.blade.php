@extends('layouts.course')

@section('title', 'Announcements')

@section('content')
<div class="course-header">
    <h1>Announcement</h1>
    <p>Stay updated with the latest course announcements</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    All
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="/maintenance">All</a></li>
                    <li><a class="dropdown-item" href="/maintenance">Recent</a></li>
                    <li><a class="dropdown-item" href="/maintenance">Important</a></li>
                </ul>
            </div>
        </div>

        @if(count($announcements) > 0)
            @foreach($announcements as $announcement)
                <div class="announcement-card">
                    <div class="announcement-header">
                        <img src="{{ $announcement['author_image'] }}" alt="{{ $announcement['author'] }}" class="announcement-avatar">
                        <div class="flex-grow-1">
                            <div class="announcement-title">{{ $announcement['title'] }}</div>
                            <div class="announcement-date">
                                <i class="fas fa-clock"></i>
                                On {{ $announcement['date'] }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="announcement-author">{{ $announcement['author'] }} said:</div>
                    <div class="announcement-content">{{ $announcement['content'] }}</div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-bullhorn"></i>
                <h3>No Announcements</h3>
                <p>There are no announcements at the moment. Check back later for updates.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any additional functionality
    console.log('Announcements page loaded');
});
</script>
@endpush
