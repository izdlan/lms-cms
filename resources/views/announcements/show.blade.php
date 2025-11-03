@extends('layouts.app')

@php
    $isArray = is_array($announcement);
    $title = $isArray ? ($announcement['title'] ?? '') : ($announcement->title ?? '');
    $imageUrl = $isArray
        ? ($announcement['poster'] ?? ($announcement['image_url'] ?? null))
        : ($announcement->poster ?? ($announcement->image_url ?? null));
    $publishedAt = $isArray ? ($announcement['date'] ?? null) : ($announcement->published_at ?? null);
    $category = $isArray ? ($announcement['category'] ?? 'general') : ($announcement->category ?? 'general');
    $priority = $isArray ? ($announcement['priority'] ?? 'low') : ($announcement->priority ?? 'low');
    $author = $isArray ? ($announcement['author'] ?? '') : ($announcement->author ?? ($announcement->author_name ?? ''));
    $hasImage = !empty($imageUrl);
    $formattedDate = $publishedAt ? \Illuminate\Support\Carbon::parse($publishedAt)->format('F d, Y') : 'No date';
@endphp

@section('title', $title)

@section('content')
<div class="announcement-detail-page">
    <div class="container-fluid">
        <!-- Back Button -->
        <div class="back-section mb-4">
            <a href="{{ route('announcements.index') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Announcements
            </a>
        </div>

        <!-- Announcement Detail Card -->
        <div class="announcement-detail-card">
            <!-- Removed top poster image to avoid duplicate image rendering -->
            
            <div class="announcement-header">
                <div class="announcement-meta">
                    <span class="announcement-category badge badge-{{ $priority === 'high' ? 'danger' : ($priority === 'medium' ? 'warning' : 'info') }}">
                        {{ $category }}
                    </span>
                    <span class="announcement-date">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $formattedDate }}
                    </span>
                </div>
                <div class="announcement-priority">
                    @if($priority === 'high')
                        <span class="priority-badge high">
                            <i class="fas fa-exclamation-triangle"></i>
                            High Priority
                        </span>
                    @elseif($priority === 'medium')
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
                <h1 class="announcement-title">{{ $title }}</h1>
                @if($hasImage)
                <div class="announcement-image-wrapper">
                    <button type="button" class="image-lightbox-trigger" aria-label="View image" onclick="openAnnouncementLightbox('{{ $imageUrl }}')">
                        <img src="{{ $imageUrl }}" alt="{{ $title }}" class="announcement-full-image">
                    </button>
                </div>
                @endif
                
                <div class="announcement-content">
                    @if($isArray)
                        {!! nl2br(e($announcement['full_content'] ?? ($announcement['content'] ?? ''))) !!}
                    @else
                        {!! nl2br(e($announcement->full_content ?? ($announcement->content ?? ''))) !!}
                    @endif
                </div>
                
                <div class="announcement-footer">
                    <div class="announcement-author">
                        <i class="fas fa-user me-1"></i>
                        Published by: {{ $author }}
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

/* Full image (no cropping) inside announcement body */
.announcement-image-wrapper {
    margin: 0 0 1.5rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    background: #f8f9fa;
}

.announcement-full-image {
    width: 100%;
    height: auto;
    max-height: 520px;
    object-fit: contain;
    display: block;
}

.image-lightbox-trigger {
    appearance: none;
    border: 0;
    background: transparent;
    padding: 0;
    width: 100%;
    cursor: zoom-in;
}

/* Lightbox */
.lightbox-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.lightbox-overlay.open {
    display: flex;
}

.lightbox-image {
    max-width: 95vw;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}

.lightbox-close {
    position: fixed;
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, 0.9);
    color: #2c3e50;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    font-weight: 600;
    z-index: 1060;
}

.lightbox-close:hover {
    background: white;
    border-color: #0056d2;
    color: #0056d2;
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
    
    .announcement-full-image {
        max-height: 320px;
    }
}
</style>

<!-- Image Lightbox -->
<div id="announcement-lightbox" class="lightbox-overlay" onclick="closeAnnouncementLightbox(event)">
    <img id="announcement-lightbox-image" class="lightbox-image" alt="Preview">
    <button type="button" class="lightbox-close" onclick="closeAnnouncementLightbox(event)">Close</button>
</div>

<script>
function shareAnnouncement() {
    var title = '{{ $title }}';
    var content = '{{ $isArray ? Str::limit($announcement["content"] ?? "", 100) : Str::limit($announcement->content ?? "", 100) }}';
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: content,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link copied to clipboard!');
        });
    }
}

function openAnnouncementLightbox(src) {
    var overlay = document.getElementById('announcement-lightbox');
    var image = document.getElementById('announcement-lightbox-image');
    image.src = src;
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeAnnouncementLightbox(event) {
    // close if clicking backdrop or close button
    var overlay = document.getElementById('announcement-lightbox');
    if (event && event.target) {
        var isBackdrop = event.target.id === 'announcement-lightbox';
        var isCloseBtn = event.target.classList.contains('lightbox-close');
        if (!isBackdrop && !isCloseBtn) return;
    }
    overlay.classList.remove('open');
    document.body.style.overflow = '';
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var overlay = document.getElementById('announcement-lightbox');
        if (overlay && overlay.classList.contains('open')) {
            closeAnnouncementLightbox(e);
        }
    }
});
</script>
@endsection
