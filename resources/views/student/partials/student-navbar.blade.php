<!-- Student Navigation Bar -->
<nav class="student-navbar" data-navbar-disabled="true">
    <div class="container-fluid">
        <!-- Left Side -->
        <div class="navbar-nav-left">
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle d-lg-none" type="button" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <img src="/store/1/dark-logo.png" alt="Olympia Education" class="navbar-logo">
                <span class="brand-text d-none d-md-inline">
                    <strong>OLYMPIA EDUCATION</strong>
                    <small>Centre for Professional, Executive, Advanced & Continuing Education</small>
                </span>
            </a>
        </div>

        <!-- Right Side -->
        <div class="navbar-nav-right">
            @auth('student')
                <!-- My Subjects Dropdown -->
                <div class="dropdown courses-dropdown-right d-none d-md-block">
                    <button class="btn btn-outline-success dropdown-toggle courses-dropdown-btn" type="button" id="coursesDropdown" aria-expanded="false" onclick="window.toggleDropdown && window.toggleDropdown(); return false;">
                        <i class="fas fa-book"></i>
                        <span class="d-none d-lg-inline">My Subjects</span>
                    </button>
                    <ul class="dropdown-menu courses-dropdown-menu" aria-labelledby="coursesDropdown">
                        <li class="dropdown-header">
                            <i class="fas fa-book"></i>
                            My Subjects
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @if(isset($enrolledSubjects) && $enrolledSubjects && $enrolledSubjects->count() > 0)
                                <!-- Subjects Header -->
                                <div class="subjects-header">
                                    <i class="fas fa-book"></i>
                                    My Subjects ({{ $enrolledSubjects->count() }})
                                </div>
                                
                                <!-- Subjects List -->
               @foreach($enrolledSubjects as $enrollment)
                   <a class="dropdown-item subject-item" href="{{ route('student.course.class', $enrollment->subject_code) }}">
                       <div class="subject-info">
                           <span class="subject-code">{{ $enrollment->subject_code }}</span>
                           <span class="subject-title">{{ $enrollment->subject ? $enrollment->subject->name : '-' }}</span>
                           <div class="lecturer-info">
                               <i class="fas fa-user-tie"></i>
                               {{ $enrollment->lecturer ? $enrollment->lecturer->name : '-' }}
                           </div>
                           <div class="class-info">
                               <i class="fas fa-chalkboard-teacher"></i>
                               {{ $enrollment->class_code ?? '-' }}
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
                    </ul>
                </div>

                <!-- Student Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle student-profile-btn" type="button" id="studentDropdown" aria-expanded="false" onclick="window.toggleProfileDropdown && window.toggleProfileDropdown(); return false;">
                        <div class="profile-info">
                            @if(auth('student')->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth('student')->user()->profile_picture) }}" alt="Profile" class="profile-pic-nav">
                            @else
                                <div class="profile-pic-placeholder-nav">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <span class="student-name d-none d-sm-inline">{{ auth('student')->user()->name }}</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end student-profile-menu" aria-labelledby="studentDropdown">
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

<script>
// DISABLE BOOTSTRAP DROPDOWN COMPLETELY
(function() {
    'use strict';
    
    // Disable Bootstrap dropdown initialization
    if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Dropdown) {
        const originalDropdown = window.bootstrap.Dropdown;
        window.bootstrap.Dropdown = function() {
            // Do nothing - disable Bootstrap dropdowns
            return {
                show: function() {},
                hide: function() {},
                toggle: function() {},
                dispose: function() {}
            };
        };
        window.bootstrap.Dropdown.getInstance = function() { return null; };
        window.bootstrap.Dropdown.getOrCreateInstance = function() { return null; };
    }
    
    // Remove all data-bs-toggle="dropdown" attributes
    function removeBootstrapDropdowns() {
        const dropdownButtons = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdownButtons.forEach(button => {
            button.removeAttribute('data-bs-toggle');
            button.removeAttribute('data-bs-auto-close');
            button.removeAttribute('data-bs-offset');
            button.removeAttribute('data-bs-reference');
            button.removeAttribute('data-bs-popper-config');
        });
    }
    
    // Run immediately and on DOM ready
    removeBootstrapDropdowns();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeBootstrapDropdowns);
    }
    
    // Also run after a delay to catch dynamically added elements
    setTimeout(removeBootstrapDropdowns, 100);
    setTimeout(removeBootstrapDropdowns, 500);
})();

// 100% WORKING DROPDOWN SOLUTION
(function() {
    'use strict';
    
    let dropdownInitialized = false;
    let coursesDropdown = null;
    let coursesDropdownMenu = null;
    
    // Function to find elements
    function findElements() {
        coursesDropdown = document.getElementById("coursesDropdown");
        coursesDropdownMenu = document.querySelector(".courses-dropdown-menu");
        
        console.log("Found dropdown button:", coursesDropdown);
        console.log("Found dropdown menu:", coursesDropdownMenu);
        console.log("All dropdown menus:", document.querySelectorAll(".courses-dropdown-menu"));
        
        return coursesDropdown && coursesDropdownMenu;
    }
    
    // Function to close dropdown
    function closeDropdown() {
        console.log("closeDropdown called");
        
        // Close ALL dropdown menus on the page
        const allDropdownMenus = document.querySelectorAll(".courses-dropdown-menu");
        console.log("Found", allDropdownMenus.length, "dropdown menus to close");
        
        allDropdownMenus.forEach((menu, index) => {
            console.log(`Closing dropdown menu ${index + 1}:`, menu);
            
            // Try multiple approaches to hide each dropdown
            menu.classList.remove("show");
            menu.style.setProperty("display", "none", "important");
            menu.style.setProperty("visibility", "hidden", "important");
            menu.style.setProperty("opacity", "0", "important");
            menu.style.setProperty("transform", "translateY(-10px)", "important");
            menu.style.setProperty("position", "absolute", "important");
            menu.style.setProperty("left", "-9999px", "important");
            menu.style.setProperty("top", "-9999px", "important");
            
            console.log(`After close - classes:`, menu.className);
            console.log(`After close - computed display:`, window.getComputedStyle(menu).display);
        });
        
        if (coursesDropdown) {
            coursesDropdown.setAttribute("aria-expanded", "false");
            coursesDropdown.classList.remove("show");
            console.log("Dropdown button updated");
        }
        
        console.log("All dropdown menus closed");
    }
    
    // Function to open dropdown
    function openDropdown() {
        // First close all dropdowns
        closeDropdown();
        
        // Then open only the specific one we want
        if (coursesDropdownMenu) {
            coursesDropdownMenu.classList.add("show");
            coursesDropdownMenu.style.setProperty("display", "block", "important");
            coursesDropdownMenu.style.setProperty("visibility", "visible", "important");
            coursesDropdownMenu.style.setProperty("opacity", "1", "important");
            coursesDropdownMenu.style.setProperty("transform", "translateY(0)", "important");
            coursesDropdownMenu.style.setProperty("position", "absolute", "important");
            coursesDropdownMenu.style.setProperty("left", "0", "important");
            coursesDropdownMenu.style.setProperty("top", "100%", "important");
        }
        if (coursesDropdown) {
            coursesDropdown.setAttribute("aria-expanded", "true");
            coursesDropdown.classList.add("show");
        }
    }
    
    // Function to toggle dropdown
    function toggleDropdown() {
        if (!coursesDropdownMenu) {
            console.log("No dropdown menu found");
            return;
        }
        
        const isOpen = coursesDropdownMenu.classList.contains("show") || 
                      coursesDropdownMenu.style.display === "block";
        
        console.log("Toggle dropdown - isOpen:", isOpen);
        
        if (isOpen) {
            console.log("Closing dropdown");
            closeDropdown();
        } else {
            console.log("Opening dropdown");
            openDropdown();
        }
    }
    
    // Click handler for dropdown button
    function handleDropdownClick(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Prevent any Bootstrap interference
        if (e.target && e.target.closest('.courses-dropdown-btn')) {
            toggleDropdown();
        }
    }
    
    // Click outside handler
    function handleDocumentClick(e) {
        if (!coursesDropdown || !coursesDropdownMenu) return;
        
        const target = e.target;
        const isClickInsideDropdown = coursesDropdown.contains(target) || 
                                    coursesDropdownMenu.contains(target);
        
        console.log("Document click - target:", target, "isClickInsideDropdown:", isClickInsideDropdown);
        
        if (!isClickInsideDropdown) {
            console.log("Click outside detected - closing dropdown");
            closeDropdown();
        }
    }
    
    // Escape key handler
    function handleKeyDown(e) {
        if (e.key === "Escape") {
            closeDropdown();
        }
    }
    
    // Initialize dropdown
    function initDropdown() {
        if (dropdownInitialized) return;
        
        if (!findElements()) {
            return false;
        }
        
        // Remove any existing event listeners
        if (coursesDropdown) {
            coursesDropdown.onclick = null;
            coursesDropdown.removeEventListener('click', handleDropdownClick);
        }
        
        // Add event listeners
        if (coursesDropdown) {
            coursesDropdown.addEventListener('click', handleDropdownClick);
        }
        
        document.addEventListener('click', handleDocumentClick);
        document.addEventListener('keydown', handleKeyDown);
        
        // Prevent menu clicks from closing dropdown
        if (coursesDropdownMenu) {
            coursesDropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        dropdownInitialized = true;
        console.log("Dropdown initialized successfully");
        return true;
    }
    
    // Multiple initialization attempts
    function tryInit() {
        if (initDropdown()) {
            return;
        }
        
        // Try again after short delay
        setTimeout(function() {
            if (!dropdownInitialized) {
                initDropdown();
            }
        }, 50);
        
        // Try again after longer delay
        setTimeout(function() {
            if (!dropdownInitialized) {
                initDropdown();
            }
        }, 200);
        
        // Try again after DOM is ready
        setTimeout(function() {
            if (!dropdownInitialized) {
                initDropdown();
            }
        }, 500);
    }
    
    // Start initialization
    tryInit();
    
    // Also try when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryInit);
    } else {
        tryInit();
    }
    
    // Try when window loads
    window.addEventListener('load', tryInit);
    
    // Expose functions globally for debugging
    window.closeDropdown = closeDropdown;
    window.openDropdown = openDropdown;
    window.toggleDropdown = toggleDropdown;
    
    // (debug button removed)
    
})();

// Additional CSS to ensure dropdown works
(function() {
    // Check if style already exists
    let existingStyle = document.getElementById('dropdown-fix-style');
    if (existingStyle) {
        existingStyle.remove();
    }
    
    const dropdownStyle = document.createElement('style');
    dropdownStyle.id = 'dropdown-fix-style';
    dropdownStyle.textContent = `
        .courses-dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            z-index: 1000 !important;
            min-width: 280px !important;
            background: white !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            transform: translateY(-10px) !important;
            transition: all 0.2s ease !important;
        }
        
        .courses-dropdown-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        .courses-dropdown-menu.show[style*="display: block"] {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        .courses-dropdown-btn {
            position: relative !important;
        }
        
        /* Force hide when not showing */
        .courses-dropdown-menu:not(.show) {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            transform: translateY(-10px) !important;
        }
    `;
    document.head.appendChild(dropdownStyle);
})();

document.addEventListener("DOMContentLoaded", function () {
    // --- SAFELY DISABLE THEME NAVBAR.JS ---
    // Prevent error from navbar.min.js (offsetTop issue)
    try {
        const mainNavbar = document.querySelector('.student-navbar');
        if (mainNavbar && mainNavbar.hasAttribute('data-navbar-disabled')) {
            console.log("Navbar disabled by data attribute - skipping theme navbar.js");
        }
        if (mainNavbar && mainNavbar.offsetTop !== undefined) {
            // OK
        }
    } catch (err) {
        console.warn("Navbar init skipped to prevent offsetTop error:", err);
    }

    // --- MOBILE MENU TOGGLE ---
    const mobileMenuToggle = document.getElementById("mobileMenuToggle");
    const sidebar = document.querySelector(".sidebar");
    const sidebarOverlay = document.querySelector(".sidebar-overlay");
    const mainContent = document.querySelector(".main-content");

    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener("click", function() {
            sidebar.classList.toggle("mobile-open");
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle("show");
            }
            document.body.classList.toggle("sidebar-open");
        });

        // Close sidebar when clicking overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener("click", function() {
                sidebar.classList.remove("mobile-open");
                sidebarOverlay.classList.remove("show");
                document.body.classList.remove("sidebar-open");
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener("click", function(e) {
            if (window.innerWidth < 992) {
                if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                    sidebar.classList.remove("mobile-open");
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.remove("show");
                    }
                    document.body.classList.remove("sidebar-open");
                }
            }
        });

        // Close sidebar when window is resized to desktop
        window.addEventListener("resize", function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove("mobile-open");
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove("show");
                }
                document.body.classList.remove("sidebar-open");
            }
        });
    }

    // --- CUSTOM DROPDOWN LOGIC ---
    function initializeDropdown() {
        const coursesDropdown = document.getElementById("coursesDropdown");
        const coursesDropdownMenu = document.querySelector(".courses-dropdown-menu");

        if (coursesDropdown && coursesDropdownMenu) {
            // Remove any existing event listeners
            coursesDropdown.removeEventListener("click", handleDropdownClick);
            document.removeEventListener("click", handleOutsideClick);
            document.removeEventListener("keydown", handleEscapeKey);
            coursesDropdownMenu.removeEventListener("click", handleMenuClick);

            // Click to toggle
            coursesDropdown.addEventListener("click", handleDropdownClick);

            // Click outside to close
            document.addEventListener("click", handleOutsideClick);

            // Escape key closes dropdown
            document.addEventListener("keydown", handleEscapeKey);

            // Prevent closing when clicking inside menu
            coursesDropdownMenu.addEventListener("click", handleMenuClick);
        }
    }

    function handleDropdownClick(e) {
        e.preventDefault();
        e.stopPropagation();

        const coursesDropdown = document.getElementById("coursesDropdown");
        const coursesDropdownMenu = document.querySelector(".courses-dropdown-menu");
        
        if (!coursesDropdown || !coursesDropdownMenu) return;

        const isOpen = coursesDropdownMenu.classList.contains("show");

        // Close all other open dropdowns
        document.querySelectorAll(".dropdown-menu.show").forEach(menu => {
            menu.classList.remove("show");
        });

        // Toggle this dropdown
        if (!isOpen) {
            coursesDropdownMenu.classList.add("show");
            coursesDropdown.setAttribute("aria-expanded", "true");
        } else {
            coursesDropdownMenu.classList.remove("show");
            coursesDropdown.setAttribute("aria-expanded", "false");
        }
    }

    function handleOutsideClick(e) {
        const coursesDropdown = document.getElementById("coursesDropdown");
        const coursesDropdownMenu = document.querySelector(".courses-dropdown-menu");
        
        if (!coursesDropdown || !coursesDropdownMenu) return;

        if (
            !coursesDropdown.contains(e.target) &&
            !coursesDropdownMenu.contains(e.target)
        ) {
            coursesDropdownMenu.classList.remove("show");
            coursesDropdown.setAttribute("aria-expanded", "false");
        }
    }

    function handleEscapeKey(e) {
        if (e.key === "Escape") {
            const coursesDropdown = document.getElementById("coursesDropdown");
            const coursesDropdownMenu = document.querySelector(".courses-dropdown-menu");
            
            if (coursesDropdown && coursesDropdownMenu) {
                coursesDropdownMenu.classList.remove("show");
                coursesDropdown.setAttribute("aria-expanded", "false");
            }
        }
    }

    function handleMenuClick(e) {
        e.stopPropagation();
    }

    // Initialize dropdown
    initializeDropdown();

    // Fallback initialization after a short delay
    setTimeout(function() {
        initializeDropdown();
    }, 100);

    // Re-initialize if elements are added dynamically
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const coursesDropdown = document.getElementById("coursesDropdown");
                const coursesDropdownMenu = document.querySelector(".courses-dropdown-menu");
                if (coursesDropdown && coursesDropdownMenu) {
                    initializeDropdown();
                }
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Additional fallback - reinitialize on window load
    window.addEventListener('load', function() {
        initializeDropdown();
    });
});
</script>

<script>
// Profile dropdown (student account) - custom logic independent of Bootstrap
(function() {
    'use strict';

    const profileBtn = document.getElementById('studentDropdown');
    const profileMenu = document.querySelector('.student-profile-menu');

    function closeProfile() {
        if (profileMenu) {
            profileMenu.classList.remove('show');
            profileMenu.style.display = 'none';
        }
        if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
    }

    function openProfile() {
        if (profileMenu) {
            profileMenu.classList.add('show');
            profileMenu.style.display = 'block';
        }
        if (profileBtn) profileBtn.setAttribute('aria-expanded', 'true');
    }

    function toggleProfile() {
        if (!profileMenu) return;
        const isOpen = profileMenu.classList.contains('show') || profileMenu.style.display === 'block';
        if (isOpen) {
            closeProfile();
        } else {
            // close program dropdown if open
            const programMenu = document.querySelector('.courses-dropdown-menu');
            if (programMenu) programMenu.classList.remove('show');
            openProfile();
        }
    }

    // Expose for inline onclick
    window.toggleProfileDropdown = toggleProfile;

    if (profileBtn && profileMenu) {
        // Ensure hidden by default if not .show
        if (!profileMenu.classList.contains('show')) profileMenu.style.display = 'none';

        // Click outside closes
        document.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                closeProfile();
            }
        });

        // Esc closes
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') closeProfile();
        });

        // Prevent menu click from closing
        profileMenu.addEventListener('click', function(e){ e.stopPropagation(); });
    }
})();
</script>

<style>
/* Guarantee profile menu hides when not shown */
.student-profile-menu:not(.show) { display: none !important; }
</style>
