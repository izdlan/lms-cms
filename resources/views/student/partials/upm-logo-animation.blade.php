@php
    $brand = null;
    $subtitle = null;
    if (Auth::guard('student')->check()) {
        $student = Auth::guard('student')->user();
        if (($student->source_sheet === 'UPM LMS') || ($student->student_id === 'AA0876231')) {
            $brand = 'UPM';
            $subtitle = 'Universiti Putra Malaysia • Berilmu Berbakti';
        } elseif ($student->source_sheet === 'DRB LMS' || $student->source_sheet === 'DHU LMS') {
            $brand = 'DRB';
            $subtitle = 'DRB';
        } elseif ($student->source_sheet === 'IUC LMS') {
            $brand = 'IUC';
            $subtitle = 'IUC';
        } elseif (empty($student->source_sheet) || $student->source_sheet === 'N/A') {
            $brand = 'Olympia';
            $subtitle = 'Olympia Education';
        }
    }
@endphp

@if($brand)
    <div id="upmLogoOverlay" class="upm-logo-overlay" style="display:none;">
        <div class="upm-logo-container upm-hero">
            <div class="upm-logo-wrap">
                <img src="/assets/{{ $brand }}.png" alt="{{ $brand }}" class="upm-logo" />
                <span class="upm-shine" aria-hidden="true"></span>
            </div>
            <div class="upm-taglines">
                <h3 class="upm-heading">Proud to be {{ $brand }}</h3>
                <div class="upm-sub">{{ $subtitle }}</div>
            </div>
        </div>
    </div>

    <style>
        .upm-logo-overlay {
            position: fixed !important;
            inset: 0 !important;
            background: rgba(255,255,255,0.96) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 4000 !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: all !important;
        }
        .upm-logo-container {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 24px !important;
            border-radius: 16px !important;
            background: radial-gradient(120% 120% at 50% 0%, #ffffff 0%, #f8f9fb 55%, #eef2f7 100%) !important;
            box-shadow: 0 18px 48px rgba(0,0,0,0.18), 0 2px 0 rgba(0,0,0,0.02) inset !important;
            animation: upmPopIn 650ms ease-out forwards, upmGlow 2.2s ease-in-out 200ms 1;
            flex-direction: column !important;
            gap: 12px !important;
        }
        .upm-logo {
            width: min(40vw, 320px) !important;
            height: auto !important;
            animation: upmPulse 1.6s ease-in-out 200ms 2;
        }
        .upm-logo-wrap { position: relative !important; display: inline-block !important; }
        .upm-shine {
            position: absolute !important;
            top: 0 !important;
            left: -150% !important;
            height: 100% !important;
            width: 60% !important;
            background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.7) 45%, rgba(255,255,255,0) 90%) !important;
            transform: skewX(-20deg) !important;
            animation: upmShine 1200ms ease-out 300ms 1 forwards;
            pointer-events: none !important;
            border-radius: 8px !important;
        }
        .upm-taglines { text-align: center !important; }
        .upm-heading {
            margin: 0 !important;
            font-weight: 800 !important;
            letter-spacing: 0.4px !important;
            color: #a10f2b !important; /* UPM crimson accent */
            text-transform: uppercase !important;
            font-size: 1.15rem !important;
            animation: upmTextIn 500ms ease 350ms both;
        }
        .upm-sub {
            color: #6b7280 !important;
            font-size: 0.9rem !important;
            animation: upmTextIn 500ms ease 450ms both;
        }
        @keyframes upmPopIn {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1.0); opacity: 1; }
        }
        @keyframes upmGlow {
            0% { box-shadow: 0 18px 48px rgba(0,0,0,0.18), 0 0 0 rgba(161,15,43,0); }
            40% { box-shadow: 0 18px 48px rgba(0,0,0,0.18), 0 0 24px rgba(161,15,43,0.35); }
            100% { box-shadow: 0 18px 48px rgba(0,0,0,0.18), 0 0 0 rgba(161,15,43,0); }
        }
        @keyframes upmPulse {
            0% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(0,0,0,0)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 10px 18px rgba(0,0,0,0.15)); }
            100% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(0,0,0,0)); }
        }
        @keyframes upmShine {
            0% { left: -150%; opacity: 0; }
            10% { opacity: 1; }
            100% { left: 150%; opacity: 0; }
        }
        @keyframes upmTextIn {
            0% { opacity: 0; transform: translateY(8px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 576px) {
            .upm-logo { width: min(70vw, 280px) !important; }
        }
    </style>

    <script>
        (function() {
            function safeHide(overlay) {
                try {
                    if (!overlay) return;
                    overlay.style.setProperty('transition', 'opacity 300ms ease', 'important');
                    overlay.style.setProperty('opacity', '0', 'important');
                    overlay.style.setProperty('pointer-events', 'none', 'important');
                    overlay.style.setProperty('visibility', 'hidden', 'important');
                    setTimeout(function() {
                        try {
                            overlay.style.setProperty('display', 'none', 'important');
                        } catch (_) {
                            overlay.style.display = 'none';
                        }
                        // As a last resort, remove it from the DOM
                        try { overlay.parentNode && overlay.parentNode.removeChild(overlay); } catch (_) {}
                        proceedToDashboard();
                    }, 320);
                } catch (_) {}
            }

            function init() {
                try {
                    var overlayAtStart = document.getElementById('upmLogoOverlay');

                    var overlay = document.getElementById('upmLogoOverlay');
                    if (!overlay) { return; }

                    // Always show overlay on dashboard load (post-login)
                    overlay.style.setProperty('display', 'flex', 'important');
                    overlay.style.setProperty('opacity', '1', 'important');
                    overlay.style.setProperty('visibility', 'visible', 'important');
                    overlay.classList.remove('show'); // prevent any bootstrap show-related styling

                    // Click anywhere on screen to dismiss
                    overlay.addEventListener('click', function() { safeHide(overlay); });
                    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') safeHide(overlay); });

                    // No auto-hide - popup stays until user clicks or presses Escape
                } catch (e) {
                    var ov = document.getElementById('upmLogoOverlay');
                    if (ov) { ov.style.display = 'none'; }
                }
            }

            function proceedToDashboard() {
                try {
                    var dashboardUrl = "{{ route('student.dashboard') }}";
                    // If we're not already on the dashboard, redirect
                    var onDashboard = window.location.pathname.indexOf('/student/dashboard') === 0;
                    if (!onDashboard) {
                        setTimeout(function(){ window.location.assign(dashboardUrl); }, 100);
                    }
                } catch (_) {}
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
            window.addEventListener('load', function(){
                // Ensure it's not lingering on fully loaded page
                var ov = document.getElementById('upmLogoOverlay');
                if (ov && ov.style.opacity === '0') {
                    ov.style.display = 'none';
                }
            });
        })();
    </script>
@endif


