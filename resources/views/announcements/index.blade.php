@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="announcements-page">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <h1 class="page-title">Announcements</h1>
            <p class="page-subtitle">Stay updated with the latest news and important information</p>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="filter-section">
                    <label for="categoryFilter" class="form-label">Filter by Category:</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="Academic">Academic</option>
                        <option value="Events">Events</option>
                        <option value="Financial">Financial</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="search-section">
                    <label for="searchAnnouncements" class="form-label">Search:</label>
                    <input type="text" class="form-control" id="searchAnnouncements" placeholder="Search announcements...">
                </div>
            </div>
        </div>

        <!-- Announcements List -->
        <div class="announcements-list">
            @foreach($announcements as $announcement)
            <div class="announcement-card" data-category="{{ $announcement['category'] }}" data-title="{{ strtolower($announcement['title']) }}" data-content="{{ strtolower($announcement['content']) }}">
                <!-- Poster Image Section -->
                @if($announcement['has_poster'] && $announcement['poster'])
                <div class="announcement-poster">
                    <a href="{{ route('announcements.show', $announcement['id']) }}">
                        <img src="{{ $announcement['poster'] }}" alt="{{ $announcement['title'] }}" class="poster-image">
                    </a>
                </div>
                @endif
                
                <div class="announcement-header">
                    <div class="announcement-meta">
                        <span class="announcement-category badge badge-{{ $announcement['priority'] === 'high' ? 'danger' : ($announcement['priority'] === 'medium' ? 'warning' : 'info') }}">
                            {{ $announcement['category'] }}
                        </span>
                        <span class="announcement-date">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ date('M d, Y', strtotime($announcement['date'])) }}
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
                    <h3 class="announcement-title">
                        <a href="{{ route('announcements.show', $announcement['id']) }}">
                            {{ $announcement['title'] }}
                        </a>
                    </h3>
                    <p class="announcement-content">
                        {{ Str::limit($announcement['content'], 150) }}
                    </p>
                    <div class="announcement-footer">
                        <div class="announcement-author">
                            <i class="fas fa-user me-1"></i>
                            {{ $announcement['author'] }}
                        </div>
                        <a href="{{ route('announcements.show', $announcement['id']) }}" class="read-more-btn">
                            Read More <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- No Results Message -->
        <div class="no-results" style="display: none;">
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>No announcements found</h4>
                <p class="text-muted">Try adjusting your search criteria or filters.</p>
            </div>
        </div>
    </div>
</div>

<style>
.announcements-page {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 100vh;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    margin: 0;
}

.filter-section, .search-section {
    margin-bottom: 1rem;
}

.announcements-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.announcement-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.announcement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.announcement-poster {
    width: 100%;
    height: 200px;
    overflow: hidden;
    position: relative;
}

.poster-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.announcement-poster:hover .poster-image {
    transform: scale(1.05);
}

.announcement-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
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
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
}

.announcement-date {
    color: #6c757d;
    font-size: 0.9rem;
}

.priority-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
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
    padding: 1.5rem;
}

.announcement-title {
    margin-bottom: 1rem;
}

.announcement-title a {
    color: #2c3e50;
    text-decoration: none;
    font-size: 1.3rem;
    font-weight: 600;
}

.announcement-title a:hover {
    color: #0056d2;
    text-decoration: underline;
}

.announcement-content {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.announcement-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.announcement-author {
    color: #6c757d;
    font-size: 0.9rem;
}

.read-more-btn {
    color: #0056d2;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.read-more-btn:hover {
    color: #004085;
    text-decoration: none;
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
        gap: 0.5rem;
    }
    
    .announcement-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('categoryFilter');
    const searchInput = document.getElementById('searchAnnouncements');
    const announcementCards = document.querySelectorAll('.announcement-card');
    const noResults = document.querySelector('.no-results');

    function filterAnnouncements() {
        const selectedCategory = categoryFilter.value.toLowerCase();
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;

        announcementCards.forEach(card => {
            const category = card.dataset.category.toLowerCase();
            const title = card.dataset.title;
            const content = card.dataset.content;
            
            const categoryMatch = !selectedCategory || category === selectedCategory;
            const searchMatch = !searchTerm || title.includes(searchTerm) || content.includes(searchTerm);
            
            if (categoryMatch && searchMatch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    categoryFilter.addEventListener('change', filterAnnouncements);
    searchInput.addEventListener('input', filterAnnouncements);
});
</script>
@endsection
