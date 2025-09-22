<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta name='robots' content="index, follow, all">

    <meta name="description" content="Home Page Description">
    <meta property="og:description" content="Home Page Description">
    <meta name='twitter:description' content='Home Page Description'>

    <link rel='shortcut icon' type='image/x-icon' href="https://lms.olympia-education.com/store/1/favicon.png">
    <link rel="manifest" href="/mix-manifest.json?v=4">
    <meta name="theme-color" content="#FFF">
    <!-- Windows Phone -->
    <meta name="msapplication-starturl" content="/">
    <meta name="msapplication-TileColor" content="#FFF">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-title" content="Olympia Education">
    <link rel="apple-touch-icon" href="https://lms.olympia-education.com/store/1/favicon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <!-- Android -->
    <link rel='icon' href='https://lms.olympia-education.com/store/1/favicon.png'>
    <meta name="application-name" content="Olympia Education">
    <meta name="mobile-web-app-capable" content="yes">
    <!-- Other -->
    <meta name="layoutmode" content="fitscreen/standard">
    <link rel="home" href="https://lms.olympia-education.com">

    <!-- Open Graph -->
    <meta property='og:title' content='Home'>
    <meta name='twitter:card' content='summary'>
    <meta name='twitter:title' content='Home'>

    <meta property='og:site_name' content='https://lms.olympia-education.com/Olympia Education'>
    <meta property='og:image' content='https://lms.olympia-education.com/store/1/favicon.png'>
    <meta name='twitter:image' content='https://lms.olympia-education.com/store/1/favicon.png'>
    <meta property='og:locale' content='en_US'>
    <meta property='og:type' content='website'>

    <title>@yield('title', 'Home') | Olympia Education</title>

    <!-- General CSS File -->
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet" href="/assets/default/css/app.css">

    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/owl-carousel2/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        @font-face {
            font-family: 'main-font-family';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('https://fonts.gstatic.com/s/montserrat/v26/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtr6Hw5aXpsog.woff2') format('woff2');
        }

        @font-face {
            font-family: 'main-font-family';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('https://fonts.gstatic.com/s/montserrat/v26/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtr6Hw3aXpsog.woff2') format('woff2');
        }

        @font-face {
            font-family: 'main-font-family';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url('https://fonts.gstatic.com/s/montserrat/v26/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtr6Hw9aXpsog.woff2') format('woff2');
        }

        @font-face {
            font-family: 'rtl-font-family';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('https://fonts.gstatic.com/s/montserrat/v26/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtr6Hw5aXpsog.woff2') format('woff2');
        }

        @font-face {
            font-family: 'rtl-font-family';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('https://fonts.gstatic.com/s/montserrat/v26/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtr6Hw3aXpsog.woff2') format('woff2');
        }

        @font-face {
            font-family: 'rtl-font-family';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url('https://fonts.gstatic.com/s/montserrat/v26/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtr6Hw9aXpsog.woff2') format('woff2');
        }

        :root {
            --primary: #0056d2;
            --primary-border: #084aa0;
            --primary-hover: #0069ff;
            --primary-border-hover: #084aa0;
            --primary-btn-shadow: 0 3px 6px rgba(0, 86, 210, 0.3);
            --primary-btn-shadow-hover: 0 3px 6px rgba(0, 86, 210, 0.4);
            --primary-btn-color: #ffffff;
            --primary-btn-color-hover: #ffffff;
        }

        /* Font fallback to prevent loading errors */
        body {
            font-family: 'Montserrat', 'main-font-family', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Hide font loading errors */
        @font-face {
            font-family: 'error-font';
            src: url('data:font/woff2;base64,') format('woff2');
            font-display: block;
        }
    </style>

    <link rel="stylesheet" href="/assets/vendors/nprogress/nprogress.min.css">
    <script src="/assets/vendors/nprogress/nprogress.min.js"></script>
    <script>
        NProgress.configure({
            showSpinner: true, // Hide spinner
            easing: 'ease', // Animation style
            speed: 500 // Animation speed
        });

        document.addEventListener("DOMContentLoaded", function() {
            NProgress.start(); // Start progress bar
        });

        window.addEventListener('load', () => {
            NProgress.done();
        });

        // Suppress font loading errors
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('Failed to decode downloaded font')) {
                e.preventDefault();
                console.warn('Font loading issue suppressed:', e.message);
                return false;
            }
        });

        // Suppress OTS parsing errors
        const originalConsoleWarn = console.warn;
        console.warn = function(...args) {
            if (args[0] && args[0].includes('OTS parsing error')) {
                return; // Suppress OTS parsing errors
            }
            originalConsoleWarn.apply(console, args);
        };
    </script>
</head>

<body class="">
    <div id="app" class=" ">
        @include('partials.top-navbar')
        @include('partials.navbar')
        
        @yield('content')
        
        @include('partials.footer')
    </div>
    
    <!-- Template JS File -->
    <script src="/assets/default/js/app.js"></script>
    <script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>
    <script type="text/javascript" src="/assets/default/vendors/simplebar/simplebar.min.js"></script>

    <script>
        var deleteAlertTitle = 'Are you sure?';
        var deleteAlertHint = 'This action cannot be undone!';
        var deleteAlertConfirm = 'Delete';
        var deleteAlertCancel = 'Cancel';
        var deleteAlertSuccess = 'Success';
        var deleteAlertFail = 'Failed';
        var deleteAlertFailHint = 'Error while deleting item!';
        var deleteAlertSuccessHint = 'Item successfully deleted.';
        var forbiddenRequestToastTitleLang = "FORBIDDEN Request";
        var forbiddenRequestToastMsgLang = 'You not access to this content.';
        var priceInvalidHintLang = 'update.price_invalid_hint';
    </script>

    <script src="/assets/default/vendors/lottie/lottie-player.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/swiper/swiper-bundle.min.js"></script>
    <script src="/assets/default/vendors/owl-carousel2/owl.carousel.min.js"></script>
    <script src="/assets/default/vendors/parallax/parallax.min.js"></script>
    <script>
        // Fix parallax error by adding safety check
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Check if parallax elements exist before initializing
                var parallaxElements = document.querySelectorAll('[data-parallax]');
                if (parallaxElements.length > 0) {
                    // Initialize parallax only if elements exist
                    if (typeof Parallax !== 'undefined') {
                        new Parallax(document.querySelector('[data-parallax]'));
                    }
                }
            } catch (e) {
                console.warn('Parallax initialization failed:', e);
            }
        });
    </script>
    <script src="/assets/default/js/parts/home.min.js"></script>
    <script src="/assets/default/js/parts/categories.min.js"></script>
    <link href="/assets/default/vendors/flagstrap/css/flags.css" rel="stylesheet">
    <script src="/assets/default/vendors/flagstrap/js/jquery.flagstrap.min.js"></script>
    <script src="/assets/default/js/parts/top_nav_flags.min.js"></script>
    <script src="/assets/default/js/parts/navbar.min.js"></script>
    <script src="/assets/default/js/parts/main.min.js"></script>

    <script>
    </script>

    @stack('styles')
    @stack('scripts')
</body>

</html>