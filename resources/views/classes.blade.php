<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name='robots' content="index, follow, all">
    <meta name="description" content="Courses page Description">
    <meta property="og:description" content="Courses page Description">
    <meta name='twitter:description' content='Courses page Description'>
    <link rel='shortcut icon' type='image/x-icon' href="/store/1/favicon.png">
    <link rel="manifest" href="/mix-manifest.json?v=4">
    <meta name="theme-color" content="#FFF">
    <meta name="msapplication-starturl" content="/">
    <meta name="msapplication-TileColor" content="#FFF">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="apple-mobile-web-app-title" content="Olympia Education">
    <link rel="apple-touch-icon" href="/store/1/favicon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel='icon' href='/store/1/favicon.png'>
    <meta name="application-name" content="Olympia Education">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="layoutmode" content="fitscreen/standard">
    <link rel="home" href="/">
    <meta property='og:title' content='Courses'>
    <meta name='twitter:card' content='summary'>
    <meta name='twitter:title' content='Courses'>
    <meta property='og:site_name' content='Olympia Education'>
    <meta property='og:image' content='/store/1/favicon.png'>
    <meta name='twitter:image' content='/store/1/favicon.png'>
    <meta property='og:locale' content='en_US'>
    <meta property='og:type' content='website'>
    <title>Courses | Olympia Education</title>
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet" href="/assets/default/css/app.css">
    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <style>
        /* Optional self-hosted Optima — place files at /public/store/1/fonts/ */
        /* Uncomment if you upload the font files
        @font-face { font-family: 'Optima'; src: url(/store/1/fonts/Optima.woff2) format('woff2'), url(/store/1/fonts/Optima.woff) format('woff'); font-weight:400; font-style:normal; font-display:swap; }
        */
        html, body { font-family: Optima, 'main-font-family', Arial, Helvetica, sans-serif; }
        @font-face { font-family: 'main-font-family'; font-style: normal; font-weight: 400; font-display: swap; src: url(/store/1/fonts/montserrat-regular.woff2) format('woff2'); }
        @font-face { font-family: 'main-font-family'; font-style: normal; font-weight: bold; font-display: swap; src: url(/store/1/fonts/montserrat-bold.woff2) format('woff2'); }
        @font-face { font-family: 'main-font-family'; font-style: normal; font-weight: 500; font-display: swap; src: url(/store/1/fonts/montserrat-medium.woff2) format('woff2'); }
        @font-face { font-family: 'rtl-font-family'; font-style: normal; font-weight: 400; font-display: swap; src: url(/store/1/fonts/Vazir-Regular.woff2) format('woff2'); }
        @font-face { font-family: 'rtl-font-family'; font-style: normal; font-weight: bold; font-display: swap; src: url(/store/1/fonts/Vazir-Bold.woff2) format('woff2'); }
        @font-face { font-family: 'rtl-font-family'; font-style: normal; font-weight: 500; font-display: swap; src: url(/store/1/fonts/Vazir-Medium.woff2) format('woff2'); }
        :root { --primary:#0056d2; --primary-border:#084aa0; --primary-hover:#0069ff; --primary-border-hover:#084aa0; --primary-btn-shadow:0 3px 6px rgba(0,86,210,.3); --primary-btn-shadow-hover:0 3px 6px rgba(0,86,210,.4); --primary-btn-color:#ffffff; --primary-btn-color-hover:#ffffff; }
    </style>
    <link rel="stylesheet" href="/assets/vendors/nprogress/nprogress.min.css">
    <script src="/assets/vendors/nprogress/nprogress.min.js"></script>
    <script>
        NProgress.configure({ showSpinner: true, easing: 'ease', speed: 500 });
        document.addEventListener('DOMContentLoaded', function(){ NProgress.start(); });
        window.addEventListener('load', function(){ NProgress.done(); });
    </script>
</head>
<body class="">
<div id="app" class=" ">
    @php
        $baseUrl = request()->getBaseUrl();
        // Remove trailing /public if present
        if (Str::endsWith($baseUrl, '/public')) {
            $baseUrl = Str::beforeLast($baseUrl, '/public');
        }
        $html = file_get_contents(base_path('classes.html'));
        // Remove Store and Register links if present in this static HTML too
        $html = preg_replace('#<li class=\"nav-item\">\s*<a class=\"nav-link\" href=\"/products\">.*?</a>\s*</li>#s', '', $html);
        $html = preg_replace('#<a[^>]*href=\"/register\"[^>]*>.*?</a>#s', '', $html);
        // Prefix leading slashes in src and href with base URL path
        $prefix = rtrim($baseUrl, '/');
        if ($prefix !== '') {
            $html = preg_replace('#(\s(?:src|href)=\")/(?!/)([^\"]*)#', '$1' . $prefix . '/$2', $html);
        }
        echo $html;
    @endphp
</div>
<script src="/assets/default/js/app.js"></script>
<script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>
<script src="/assets/default/vendors/moment.min.js"></script>
<script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>
<script type="text/javascript" src="/assets/default/vendors/simplebar/simplebar.min.js"></script>
<script src="/assets/default/vendors/select2/select2.min.js"></script>
<script src="/assets/default/vendors/swiper/swiper-bundle.min.js"></script>
<script src="/assets/default/js/parts/categories.min.js"></script>
<link href="/assets/default/vendors/flagstrap/css/flags.css" rel="stylesheet">
<script src="/assets/default/vendors/flagstrap/js/jquery.flagstrap.min.js"></script>
<script src="/assets/default/js/parts/top_nav_flags.min.js"></script>
<script src="/assets/default/js/parts/navbar.min.js"></script>
<script src="/assets/default/js/parts/main.min.js"></script>

<script>
(function(){
  const $d = document;
  const form = $d.getElementById('filtersForm');
  if(!form) return;

  const gridRadio = $d.getElementById('gridView');
  const listRadio = $d.getElementById('listView');
  const upcomingToggle = $d.getElementById('upcoming');
  const freeToggle = $d.getElementById('free');
  const discountToggle = $d.getElementById('discount');
  const downloadToggle = $d.getElementById('download');
  const sortSelect = form.querySelector('select[name="sort"]');

  const cards = Array.from($d.querySelectorAll('.webinar-card'))
    .map(card => {
      const rootCol = card.closest('[class*="col-"]') || card;
      const priceEl = card.querySelector('.webinar-price-box .real');
      const priceText = priceEl ? priceEl.textContent.trim() : '';
      const isFree = /free/i.test(priceText);
      const badgeText = (card.querySelector('.badges-lists .badge') || {textContent:''}).textContent.trim();
      const isFinished = /finished/i.test(badgeText);
      const dateText = (card.querySelector('.date-published') || {textContent:''}).textContent.trim();
      const durationText = (card.querySelector('.duration') || {textContent:''}).textContent.trim();
      // Parse date like "10 Jul 2025"
      let ts = 0;
      if (dateText) {
        const parsed = Date.parse(dateText.replace(/(\d{2}) (\w{3}) (\d{4})/, '$1 $2 $3'));
        ts = isNaN(parsed) ? 0 : parsed;
      }
      // Derive numeric price (Free = 0; otherwise try to parse number)
      let priceNum = 0;
      if (!isFree && priceText) {
        const m = priceText.replace(/[^0-9.]/g, '');
        priceNum = m ? parseFloat(m) : 0;
      }
      return { card, rootCol, isFree, isFinished, ts, priceNum };
    });

  function applyViewMode(){
    const container = $d.querySelector('.row');
    if (!container) return;
    if (listRadio && listRadio.checked) {
      container.classList.add('list-view');
      cards.forEach(({card}) => { card.style.display = 'block'; card.style.width = '100%'; });
    } else {
      container.classList.remove('list-view');
      cards.forEach(({card}) => { card.style.display = ''; card.style.width = ''; });
    }
  }

  function applyFilters(pushState=true){
    const wantUpcoming = upcomingToggle && upcomingToggle.checked;
    const wantFree = freeToggle && freeToggle.checked;
    // discount/download toggles are placeholders; no data in DOM, so we ignore them gracefully

    // Filter visibility
    cards.forEach(item => {
      let visible = true;
      if (wantUpcoming) {
        // Show only not finished
        visible = visible && !item.isFinished;
      }
      if (wantFree) {
        visible = visible && item.isFree;
      }
      item.rootCol.style.display = visible ? '' : 'none';
    });

    // Sort
    const mode = sortSelect ? sortSelect.value : '';
    const parentRow = cards[0] ? cards[0].rootCol.parentElement : null;
    if (parentRow) {
      const visibleItems = cards.filter(i => i.rootCol.style.display !== 'none');
      let sorted = visibleItems.slice();
      if (mode === 'newest') {
        sorted.sort((a,b) => b.ts - a.ts);
      } else if (mode === 'expensive') {
        sorted.sort((a,b) => b.priceNum - a.priceNum);
      } else if (mode === 'inexpensive') {
        sorted.sort((a,b) => a.priceNum - b.priceNum);
      }
      // Re-append in sorted order
      sorted.forEach(i => parentRow.appendChild(i.rootCol));
    }

    applyViewMode();

    // Update URL query without reload
    if (pushState) {
      const params = new URLSearchParams(new FormData(form));
      history.replaceState(null, '', location.pathname + '?' + params.toString());
    }
  }

  // Initialize from URL
  try {
    const params = new URLSearchParams(location.search);
    if (params.has('card')) {
      const mode = params.get('card');
      if (mode === 'list' && listRadio) listRadio.checked = true;
      if (mode === 'grid' && gridRadio) gridRadio.checked = true;
    }
    if (params.get('upcoming') === 'on' && upcomingToggle) upcomingToggle.checked = true;
    if (params.get('free') === 'on' && freeToggle) freeToggle.checked = true;
    if (sortSelect && params.has('sort')) {
      const v = params.get('sort');
      const opt = Array.from(sortSelect.options).find(o => o.value === v);
      if (opt) sortSelect.value = v;
    }
  } catch (e) {}

  // Wire events
  form.addEventListener('submit', function(ev){ ev.preventDefault(); applyFilters(true); });
  [gridRadio, listRadio, upcomingToggle, freeToggle, discountToggle, downloadToggle, sortSelect]
    .filter(Boolean).forEach(el => el.addEventListener('change', () => applyFilters(true)));

  // First run
  applyFilters(false);
})();
</script>

</body>
</html>
