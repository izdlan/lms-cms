@extends('layouts.app')

@section('title', 'My Program | Student | Olympia Education')

@section('content')
<div class="student-dashboard">
    <!-- Student Navigation Bar -->
    @include('student.partials.student-navbar')
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('student.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="courses-header">
                    <h1>My Program</h1>
                    <p>View your Executive Master in Business Administration program</p>
                    @if(request()->has('course'))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Showing details for course: <strong>{{ request('course') }}</strong>
                        </div>
                    @endif
                </div>

                @if(count($programs) > 0)
                    <div class="row">
                        @foreach($programs as $program)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="course-card">
                                    <div class="course-header">
                                        <div class="course-icon">
                                            <i data-feather="book" width="32" height="32"></i>
                                        </div>
                                        <div class="course-status">
                                            <span class="badge badge-enrolled">Available</span>
                                        </div>
                                    </div>
                                    <div class="course-body">
                                        <h5 class="course-title">{{ $program->name }}</h5>
                                        <p class="course-description">{{ $program->description }}</p>
                                        <div class="course-meta">
                                            <div class="course-duration">
                                                <i data-feather="clock" width="16" height="16"></i>
                                                <span>{{ $program->duration_months }} months</span>
                                            </div>
                                            <div class="course-level">
                                                <i data-feather="trending-up" width="16" height="16"></i>
                                                <span>{{ ucfirst($program->level) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="course-footer">
                                        <div class="course-progress">
                                            <div class="progress">
                                                @php $progress = rand(10, 90); @endphp
                                                <div class="progress-bar" role="progressbar" data-width="{{ $progress }}"></div>
                                            </div>
                                            <small class="text-muted">{{ $progress }}% Complete</small>
                                        </div>
                                        <div class="course-actions">
                                            <a href="{{ route('student.course.summary', strtolower($program->code)) }}" class="btn btn-sm btn-primary">View Program</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="book-open" width="64" height="64"></i>
                        </div>
                        <h3>No Programs Available</h3>
                        <p>There are currently no programs available. Please check back later or contact your administrator.</p>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>

.course-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.course-header {
    background: linear-gradient(135deg, #0056d2, #0041a3);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.course-icon {
    color: white;
}

.course-status .badge-enrolled {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.course-body {
    padding: 1.5rem;
    flex-grow: 1;
}

.course-title {
    color: #2d3748;
    font-weight: bold;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.course-description {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.course-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.course-duration, .course-level {
    display: flex;
    align-items: center;
    color: #718096;
    font-size: 0.85rem;
}

.course-duration i, .course-level i {
    margin-right: 0.25rem;
}

.course-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.course-progress {
    margin-bottom: 1rem;
}

.progress {
    height: 6px;
    border-radius: 3px;
    background-color: #e9ecef;
    margin-bottom: 0.5rem;
}

.progress-bar {
    background: linear-gradient(90deg, #0056d2, #0041a3);
    border-radius: 3px;
}

.course-actions {
    text-align: center;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.empty-state-icon {
    color: #cbd5e0;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: #2d3748;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #718096;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .sidebar {
        min-height: auto;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .course-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Set progress bar widths
    document.querySelectorAll('.progress-bar[data-width]').forEach(bar => {
        const width = bar.getAttribute('data-width');
        bar.style.width = width + '%';
    });
    
    // Handle course highlighting from dropdown
    const courseParam = new URLSearchParams(window.location.search).get('course');
    if (courseParam) {
        // Find the course card that matches the selected course
        const courseCards = document.querySelectorAll('.course-card');
        courseCards.forEach(card => {
            const courseTitle = card.querySelector('.course-title');
            if (courseTitle && courseTitle.textContent.trim() === courseParam) {
                // Highlight the selected course
                card.style.border = '2px solid #20c997';
                card.style.boxShadow = '0 4px 15px rgba(32, 201, 151, 0.3)';
                card.style.transform = 'scale(1.02)';
                
                // Scroll to the highlighted course
                card.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Add a temporary animation
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                }, 100);
            }
        });
    }
});
</script>
@endpush

