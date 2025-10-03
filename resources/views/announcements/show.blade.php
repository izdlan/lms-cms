@extends('layouts.app')

@section('title', $announcement['title'])

@section('content')
<div class="announcement-detail-page">
    <div class="container">
        <!-- Back Button -->
        <div class="back-section mb-4">
            <a href="{{ route('announcements.index') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Announcements
            </a>
        </div>

        <!-- Announcement Detail Card -->
        <div class="announcement-detail-card">
            <!-- Poster Image Section -->
            @if($announcement['has_poster'] && $announcement['poster'])
            <div class="announcement-poster-large">
                <img src="{{ $announcement['poster'] }}" alt="{{ $announcement['title'] }}" class="poster-image-large">
            </div>
            @endif
            
            <div class="announcement-header">
                <div class="announcement-meta">
                    <span class="announcement-category badge badge-{{ $announcement['priority'] === 'high' ? 'danger' : ($announcement['priority'] === 'medium' ? 'warning' : 'info') }}">
                        {{ $announcement['category'] }}
                    </span>
                    <span class="announcement-date">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ date('F d, Y', strtotime($announcement['date'])) }}
                    </span>
                </div>
                <div class="announcement-priority">
                    @if($announcement['priority'] === 'high')
                        <span class="priority-badge high">
                            <i class="fas fa-exclamation-triangle"></i>
                            High Priority
                        </span>
                    @elseif($announcement['priority'] === 'medium')
                        <span class="priority-badge medium">
                            <i class="fas fa-info-circle"></i>
                            Medium Priority
                        </span>
                    @else
                        <span class="priority-badge low">
                            <i class="fas fa-bell"></i>
                            Low Priority
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="announcement-body">
                <h1 class="announcement-title">{{ $announcement['title'] }}</h1>
                
                <div class="announcement-content">
                    {!! nl2br(e($announcement['full_content'])) !!}
                </div>
                
                <div class="announcement-footer">
                    <div class="announcement-author">
                        <i class="fas fa-user me-1"></i>
                        Published by: {{ $announcement['author'] }}
                    </div>
                    <div class="announcement-actions">
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>
                            Print
                        </button>
                        <button class="btn btn-outline-secondary" onclick="shareAnnouncement()">
                            <i class="fas fa-share me-1"></i>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Announcements -->
        @if($relatedAnnouncements && $relatedAnnouncements->count() > 0)
        <div class="related-announcements mt-5">
            <h3 class="section-title">Related Announcements</h3>
            <div class="row">
                @foreach($relatedAnnouncements as $relatedAnnouncement)
                <div class="col-md-6 mb-3">
                    <div class="related-card">
                        <div class="related-header">
                            <span class="related-category">{{ $relatedAnnouncement->category }}</span>
                            <span class="related-date">{{ $relatedAnnouncement->published_at ? $relatedAnnouncement->published_at->format('M j, Y') : 'No date' }}</span>
                        </div>
                        <h4 class="related-title">
                            <a href="{{ route('announcements.show', $relatedAnnouncement->id) }}">{{ $relatedAnnouncement->title }}</a>
                        </h4>
                        <p class="related-content">{{ Str::limit($relatedAnnouncement->content, 120) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.announcement-detail-page {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 100vh;
}

.back-btn {
    color: #0056d2;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    transition: color 0.3s ease;
}

.back-btn:hover {
    color: #004085;
    text-decoration: none;
}

.announcement-detail-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.announcement-poster-large {
    width: 100%;
    height: 400px;
    overflow: hidden;
    position: relative;
}

.poster-image-large {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.announcement-poster-large:hover .poster-image-large {
    transform: scale(1.02);
}

.announcement-header {
    background: #f8f9fa;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.announcement-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.announcement-category {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

.announcement-date {
    color: #6c757d;
    font-size: 1rem;
}

.priority-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

.priority-badge.high {
    background: #f8d7da;
    color: #721c24;
}

.priority-badge.medium {
    background: #fff3cd;
    color: #856404;
}

.priority-badge.low {
    background: #d1ecf1;
    color: #0c5460;
}

.announcement-body {
    padding: 2rem;
}

.announcement-title {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    line-height: 1.3;
}

.announcement-content {
    color: #495057;
    line-height: 1.8;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.announcement-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.announcement-author {
    color: #6c757d;
    font-size: 1rem;
}

.announcement-actions {
    display: flex;
    gap: 0.5rem;
}

.related-announcements {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.related-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.related-card:hover {
    transform: translateY(-2px);
}

.related-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.related-category {
    background: #0056d2;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.related-date {
    color: #6c757d;
    font-size: 0.9rem;
}

.related-title {
    margin-bottom: 0.5rem;
}

.related-title a {
    color: #2c3e50;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 600;
}

.related-title a:hover {
    color: #0056d2;
    text-decoration: underline;
}

.related-content {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

@media (max-width: 768px) {
    .announcement-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .announcement-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .announcement-title {
        font-size: 1.5rem;
    }
    
    .announcement-body {
        padding: 1.5rem;
    }
}
</style>

<script>
function shareAnnouncement() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $announcement["title"] }}',
            text: '{{ Str::limit($announcement["content"], 100) }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link copied to clipboard!');
        });
    }
}
</script>
@endsection
