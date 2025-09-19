<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home | Olympia Education</title>
    <link rel="manifest" href="/mix-manifest.json?v=4">
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet" href="/assets/default/css/app.css">
    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/owl-carousel2/owl.carousel.min.css">
</head>
<body>
@php
    $baseUrl = request()->getBaseUrl();
    $html = file_get_contents(base_path('laravel-lms-olympia/index.html'));
    // point Courses to /classes
    $html = str_replace('/classes?sort=newest', url('/classes').'?sort=newest', $html);
    // Remove Store nav item (href="/products")
    $html = preg_replace('#<li class="nav-item">\s*<a class="nav-link" href="/products">.*?</a>\s*</li>#s', '', $html);
    // Remove Register links (href="/register")
    $html = preg_replace('#<a[^>]*href="/register"[^>]*>.*?</a>#s', '', $html);
    // Prefix leading slash resources with base path for subdir installs
    $prefix = rtrim($baseUrl, '/');
    if ($prefix !== '') {
        $html = preg_replace('#(\s(?:src|href)=\")/(?!/)([^\"]*)#', '$1' . $prefix . '/$2', $html);
    }
    echo $html;
@endphp
</body>
</html>
