<!-- Student Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light student-navbar">
    <div class="container-fluid">
        <!-- Left Side -->
        <div class="navbar-nav-left">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <img src="https://lms.olympia-education.com/store/1/dark-logo.png" alt="Olympia Education" height="40">
                <span class="brand-text">
                    <strong>OLYMPIA EDUCATION</strong>
                    <small>Centre for Professional, Executive, Advanced & Continuing Education</small>
                </span>
            </a>
        </div>

        <!-- Right Side -->
        <div class="navbar-nav-right">
            @auth('student')
                <!-- My Program Dropdown -->
                <div class="dropdown courses-dropdown-right">
                    <button class="btn btn-outline-success dropdown-toggle courses-dropdown-btn" type="button" id="coursesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-graduation-cap"></i>
                        My Program
                    </button>
                    <ul class="dropdown-menu courses-dropdown-menu" aria-labelledby="coursesDropdown">
                        <li class="dropdown-header">
                            <i class="fas fa-graduation-cap"></i>
                            My Program
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @php
                            // Get EMBA program from database
                            $embaProgram = \App\Models\Program::where('code', 'EMBA')->first();
                        @endphp
                        @if($embaProgram)
                            <!-- Program Header -->
                            <div class="program-header">
                                <a class="dropdown-item program-item" href="{{ route('student.courses') }}">
                                    <div class="program-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="program-info">
                                        <div class="program-code">{{ $embaProgram->code }}</div>
                                        <div class="program-name">{{ $embaProgram->name }}</div>
                                    </div>
                                </a>
                            </div>
                            
                            @if(isset($enrolledSubjects) && $enrolledSubjects->count() > 0)
                                <!-- Subjects Header -->
                                <div class="subjects-header">
                                    <i class="fas fa-book"></i>
                                    My Subjects ({{ $enrolledSubjects->count() }})
                                </div>
                                
                                <!-- Subjects List -->
               @foreach($enrolledSubjects as $enrollment)
                   <a class="dropdown-item subject-item" href="{{ route('student.course.class', $enrollment->subject_code) }}">
                       <div class="subject-icon">
                           <i class="fas fa-book-open"></i>
                       </div>
                       <div class="subject-info">
                           <span class="subject-code">{{ $enrollment->subject_code }}</span>
                           <span class="subject-title">{{ $enrollment->subject ? $enrollment->subject->name : 'Unknown Subject' }}</span>
                           <div class="lecturer-info">
                               <i class="fas fa-user-tie"></i>
                               {{ $enrollment->lecturer ? $enrollment->lecturer->name : 'TBA' }}
                           </div>
                           <div class="class-info">
                               <i class="fas fa-chalkboard-teacher"></i>
                               {{ $enrollment->class_code ?? 'TBA' }}
                           </div>
                       </div>
                   </a>
               @endforeach
                            @else
                                <div class="subjects-header">
                                    <i class="fas fa-info-circle"></i>
                                    No Subjects Enrolled
                                </div>
                                <div class="text-center p-4 text-muted">
                                    <i class="fas fa-book-open fa-2x mb-2"></i>
                                    <p>No subjects enrolled yet</p>
                                </div>
                            @endif
                        @else
                            <div class="program-header">
                                <div class="text-center text-white">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <p class="mb-0">No program available</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- View All Button -->
                        <a class="view-all-btn" href="{{ route('student.courses') }}">
                            <i class="fas fa-eye"></i> View All Subjects
                        </a>
                    </ul>
                </div>

                <!-- Student Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle student-profile-btn" type="button" id="studentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-info">
                            @if(auth('student')->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth('student')->user()->profile_picture) }}" alt="Profile" class="profile-pic-nav">
                            @else
                                <div class="profile-pic-placeholder-nav">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <span class="student-name">{{ auth('student')->user()->name }}</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="studentDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('student.profile') }}">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('student.password.change') }}">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <!-- Login Button for non-authenticated users -->
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            @endauth
        </div>
    </div>
</nav>
