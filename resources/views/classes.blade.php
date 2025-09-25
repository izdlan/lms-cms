@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<section class="site-top-banner search-top-banner opacity-04 position-relative">
    <img src="/store/1/default_images/category_cover.png" class="img-cover" alt="" />

    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-12 col-md-9 col-lg-7">
                <div class="top-search-categories-form">
                    <h1 class="text-white font-30 mb-15">Courses</h1>
                    <span class="course-count-badge py-5 px-10 text-white rounded">126 Courses</span>

                    <div class="search-input bg-white p-10 flex-grow-1">
                        <form action="/search" method="get">
                            <div class="form-group d-flex align-items-center m-0">
                                <input type="text" name="search" class="form-control border-0" placeholder="Search courses, instructors and organizations..." />
                                <button type="submit" class="btn btn-primary rounded-pill">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mt-30">
    <section class="mt-lg-50 pt-lg-20 mt-md-40 pt-md-40">
        <form action="/classes" method="get" id="filtersForm">
            <div id="topFilters" class="shadow-lg border border-gray300 rounded-sm p-10 p-md-20">
                <div class="row align-items-center">
                    <div class="col-lg-3 d-flex align-items-center">
                        <div class="checkbox-button primary-selected">
                            <input type="radio" name="card" id="gridView" value="grid" checked="checked">
                            <label for="gridView" class="bg-white border-0 mb-0">
                                <i data-feather="grid" width="25" height="25" class=" text-primary "></i>
                            </label>
                        </div>

                        <div class="checkbox-button primary-selected ml-10">
                            <input type="radio" name="card" id="listView" value="list">
                            <label for="listView" class="bg-white border-0 mb-0">
                                <i data-feather="list" width="25" height="25" class=""></i>
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-6 d-block d-md-flex align-items-center justify-content-end my-25 my-lg-0">
                        <div class="d-flex align-items-center justify-content-between justify-content-md-center mx-0 mx-md-20 my-20 my-md-0">
                            <label class="mb-0 mr-10 cursor-pointer" for="upcoming">Upcoming</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="upcoming" class="custom-control-input" id="upcoming">
                                <label class="custom-control-label" for="upcoming"></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between justify-content-md-center">
                            <label class="mb-0 mr-10 cursor-pointer" for="free">Free</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="free" class="custom-control-input" id="free">
                                <label class="custom-control-label" for="free"></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between justify-content-md-center mx-0 mx-md-20 my-20 my-md-0">
                            <label class="mb-0 mr-10 cursor-pointer" for="discount">Discount</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="discount" class="custom-control-input" id="discount">
                                <label class="custom-control-label" for="discount"></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between justify-content-md-center">
                            <label class="mb-0 mr-10 cursor-pointer" for="download">Download</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="downloadable" class="custom-control-input" id="download">
                                <label class="custom-control-label" for="download"></label>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 d-flex align-items-center">
                        <select name="sort" class="form-control font-14">
                            <option disabled selected>Sort by</option>
                            <option value="">All</option>
                            <option value="newest" selected="selected">Newest</option>
                            <option value="expensive">Highest Price</option>
                            <option value="inexpensive">Lowest Price</option>
                            <option value="bestsellers">Bestsellers</option>
                            <option value="best_rates">Best Rated</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-20">
                <div class="col-12 col-lg-8">
                    <div class="row">
                        <div class="col-12 col-lg-6 mt-20">
                            <div class="webinar-card">
                                <figure>
                                    <div class="image-box">
                                        <div class="badges-lists">
                                            <span class="badge badge-secondary">Finished</span>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Bachelor-of-Education-in-Leadership-Management">
                                            <img src="/store/1/Courses/DOCTORATE'S PROGRAMMES/Industrial Bachelor of Education in Leadership Management-1.jpg" class="img-cover" alt="Industrial Bachelor of Education in Leadership Management">
                                        </a>

                                        <div class="progress">
                                            <span class="progress-bar" style="width: 0%"></span>
                                        </div>

                                    </div>

                                    <figcaption class="webinar-card-body">
                                        <div class="user-inline-avatar d-flex align-items-center">
                                            <div class="avatar bg-gray200">
                                                <img src="/store/1/Instructor/image 19766.png" class="img-cover" alt="Prof Madya Dr. Norreha Binti Othman">
                                            </div>
                                            <a href="/users/1047/profile" target="_blank" class="user-name ml-5 font-14">Prof Madya Dr. Norreha Binti Othman</a>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Bachelor-of-Education-in-Leadership-Management">
                                            <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">Industrial Bachelor of Education in Leadership Management</h3>
                                        </a>

                                        <span class="d-block font-14 mt-10">in <a href="/categories/DOCTORATE-S-PROGRAMMES" target="_blank" class="text-decoration-underline">DOCTORATE'S PROGRAMMES</a></span>

                                        <div class="stars-card d-flex align-items-center  mt-15">
                                        </div>

                                        <div class="d-flex justify-content-between mt-20">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                                <span class="duration font-14 ml-5">60:00 Hours</span>
                                            </div>

                                            <div class="vertical-line mx-15"></div>

                                            <div class="d-flex align-items-center">
                                                <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                                <span class="date-published font-14 ml-5">10 Jul 2025</span>
                                            </div>
                                        </div>

                                        <div class="webinar-price-box mt-25">
                                            <span class="real font-14">Free</span>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mt-20">
                            <div class="webinar-card">
                                <figure>
                                    <div class="image-box">
                                        <div class="badges-lists">
                                            <span class="badge badge-secondary">Finished</span>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Bachelor-of-Business-Administration-in-International-Business">
                                            <img src="/store/1/Courses/DOCTORATE'S PROGRAMMES/Industrial Bachelor of Business Administration in International Business-1.jpg" class="img-cover" alt="Industrial Bachelor of Business Administration in International Business">
                                        </a>

                                        <div class="progress">
                                            <span class="progress-bar" style="width: 0%"></span>
                                        </div>

                                    </div>

                                    <figcaption class="webinar-card-body">
                                        <div class="user-inline-avatar d-flex align-items-center">
                                            <div class="avatar bg-gray200">
                                                <img src="/store/1/Instructor/image 19766.png" class="img-cover" alt="Prof Madya Dr. Norreha Binti Othman">
                                            </div>
                                            <a href="/users/1047/profile" target="_blank" class="user-name ml-5 font-14">Prof Madya Dr. Norreha Binti Othman</a>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Bachelor-of-Business-Administration-in-International-Business">
                                            <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">Industrial Bachelor of Business Administration in International Business</h3>
                                        </a>

                                        <span class="d-block font-14 mt-10">in <a href="/categories/DOCTORATE-S-PROGRAMMES" target="_blank" class="text-decoration-underline">DOCTORATE'S PROGRAMMES</a></span>

                                        <div class="stars-card d-flex align-items-center  mt-15">
                                        </div>

                                        <div class="d-flex justify-content-between mt-20">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                                <span class="duration font-14 ml-5">3:00 Hours</span>
                                            </div>

                                            <div class="vertical-line mx-15"></div>

                                            <div class="d-flex align-items-center">
                                                <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                                <span class="date-published font-14 ml-5">10 Jul 2025</span>
                                            </div>
                                        </div>

                                        <div class="webinar-price-box mt-25">
                                            <span class="real font-14">Free</span>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mt-20">
                            <div class="webinar-card">
                                <figure>
                                    <div class="image-box">
                                        <div class="badges-lists">
                                            <span class="badge badge-secondary">Finished</span>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Master-in-Sustainable-Education-Transformation">
                                            <img src="/store/1/Courses/DOCTORATE'S PROGRAMMES/Industrial Master in Sustainable Education Transformation-1.jpg" class="img-cover" alt="Industrial Master in Sustainable Education Transformation">
                                        </a>

                                    </div>

                                    <figcaption class="webinar-card-body">
                                        <div class="user-inline-avatar d-flex align-items-center">
                                            <div class="avatar bg-gray200">
                                                <img src="/store/1016/avatar/617a4f17c8e72.png" class="img-cover" alt="Ricardo dave">
                                            </div>
                                            <a href="/users/1016/profile" target="_blank" class="user-name ml-5 font-14">Ricardo dave</a>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Master-in-Sustainable-Education-Transformation">
                                            <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">Industrial Master in Sustainable Education Transformation</h3>
                                        </a>

                                        <span class="d-block font-14 mt-10">in <a href="/categories/DOCTORATE-S-PROGRAMMES" target="_blank" class="text-decoration-underline">DOCTORATE'S PROGRAMMES</a></span>

                                        <div class="stars-card d-flex align-items-center  mt-15">
                                        </div>

                                        <div class="d-flex justify-content-between mt-20">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                                <span class="duration font-14 ml-5">1:00 Hours</span>
                                            </div>

                                            <div class="vertical-line mx-15"></div>

                                            <div class="d-flex align-items-center">
                                                <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                                <span class="date-published font-14 ml-5">10 Jul 2025</span>
                                            </div>
                                        </div>

                                        <div class="webinar-price-box mt-25">
                                            <span class="real font-14">Free</span>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mt-20">
                            <div class="webinar-card">
                                <figure>
                                    <div class="image-box">
                                        <div class="badges-lists">
                                            <span class="badge badge-secondary">Finished</span>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Master-in-Digital-Transformation-Management">
                                            <img src="/store/1/Courses/DOCTORATE'S PROGRAMMES/Industrial Master in Digital Transformation Management-1.jpg" class="img-cover" alt="Industrial Master in Digital Transformation Management">
                                        </a>

                                    </div>

                                    <figcaption class="webinar-card-body">
                                        <div class="user-inline-avatar d-flex align-items-center">
                                            <div class="avatar bg-gray200">
                                                <img src="/store/1016/avatar/617a4f17c8e72.png" class="img-cover" alt="Ricardo dave">
                                            </div>
                                            <a href="/users/1016/profile" target="_blank" class="user-name ml-5 font-14">Ricardo dave</a>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Industrial-Master-in-Digital-Transformation-Management">
                                            <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">Industrial Master in Digital Transformation Management</h3>
                                        </a>

                                        <span class="d-block font-14 mt-10">in <a href="/categories/DOCTORATE-S-PROGRAMMES" target="_blank" class="text-decoration-underline">DOCTORATE'S PROGRAMMES</a></span>

                                        <div class="stars-card d-flex align-items-center  mt-15">
                                        </div>

                                        <div class="d-flex justify-content-between mt-20">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                                <span class="duration font-14 ml-5">1:00 Hours</span>
                                            </div>

                                            <div class="vertical-line mx-15"></div>

                                            <div class="d-flex align-items-center">
                                                <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                                <span class="date-published font-14 ml-5">10 Jul 2025</span>
                                            </div>
                                        </div>

                                        <div class="webinar-price-box mt-25">
                                            <span class="real font-14">Free</span>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mt-20">
                            <div class="webinar-card">
                                <figure>
                                    <div class="image-box">
                                        <div class="badges-lists">
                                            <span class="badge badge-secondary">Finished</span>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Diploma-in-Occupational-Safety-Health">
                                            <img src="/store/1/Courses/DIPLOMA'S PROGRAMMES/Diploma in Occupational Safety & Health-1.jpg" class="img-cover" alt="Diploma in Occupational Safety & Health">
                                        </a>

                                    </div>

                                    <figcaption class="webinar-card-body">
                                        <div class="user-inline-avatar d-flex align-items-center">
                                            <div class="avatar bg-gray200">
                                                <img src="/store/1016/avatar/617a4f17c8e72.png" class="img-cover" alt="Ricardo dave">
                                            </div>
                                            <a href="/users/1016/profile" target="_blank" class="user-name ml-5 font-14">Ricardo dave</a>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Diploma-in-Occupational-Safety-Health">
                                            <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">Diploma in Occupational Safety & Health</h3>
                                        </a>

                                        <span class="d-block font-14 mt-10">in <a href="/categories/diplomaproggrammes" target="_blank" class="text-decoration-underline">DIPLOMA PROGRAMMES</a></span>

                                        <div class="stars-card d-flex align-items-center  mt-15">
                                        </div>

                                        <div class="d-flex justify-content-between mt-20">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                                <span class="duration font-14 ml-5">2:30 Hours</span>
                                            </div>

                                            <div class="vertical-line mx-15"></div>

                                            <div class="d-flex align-items-center">
                                                <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                                <span class="date-published font-14 ml-5">10 Jul 2025</span>
                                            </div>
                                        </div>

                                        <div class="webinar-price-box mt-25">
                                            <span class="real font-14">Free</span>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mt-20">
                            <div class="webinar-card">
                                <figure>
                                    <div class="image-box">
                                        <div class="badges-lists">
                                            <span class="badge badge-secondary">Finished</span>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Diploma-in-Information-Technology">
                                            <img src="/store/1/Courses/DIPLOMA'S PROGRAMMES/Diploma in Information Technology-1.jpg" class="img-cover" alt="Diploma in Information Technology">
                                        </a>

                                    </div>

                                    <figcaption class="webinar-card-body">
                                        <div class="user-inline-avatar d-flex align-items-center">
                                            <div class="avatar bg-gray200">
                                                <img src="/store/1016/avatar/617a4f17c8e72.png" class="img-cover" alt="Ricardo dave">
                                            </div>
                                            <a href="/users/1016/profile" target="_blank" class="user-name ml-5 font-14">Ricardo dave</a>
                                        </div>

                                        <a href="https://lms.olympia-education.com/course/Diploma-in-Information-Technology">
                                            <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">Diploma in Information Technology</h3>
                                        </a>

                                        <span class="d-block font-14 mt-10">in <a href="/categories/diplomaproggrammes" target="_blank" class="text-decoration-underline">DIPLOMA PROGRAMMES</a></span>

                                        <div class="stars-card d-flex align-items-center  mt-15">
                                        </div>

                                        <div class="d-flex justify-content-between mt-20">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                                <span class="duration font-14 ml-5">2:30 Hours</span>
                                            </div>

                                            <div class="vertical-line mx-15"></div>

                                            <div class="d-flex align-items-center">
                                                <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                                <span class="date-published font-14 ml-5">11 Jul 2025</span>
                                            </div>
                                        </div>

                                        <div class="webinar-price-box mt-25">
                                            <span class="real font-14">Free</span>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="sidebar">
                        <div class="sidebar-card">
                            <h3 class="sidebar-title font-weight-bold text-primary mb-20">Type</h3>
                            <div class="sidebar-content">
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="bundle" id="typeBundle">
                                    <label class="form-check-label" for="typeBundle">
                                        Course Bundle
                                    </label>
                                </div>
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="live" id="typeLive">
                                    <label class="form-check-label" for="typeLive">
                                        Live class
                                    </label>
                                </div>
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="course" id="typeCourse">
                                    <label class="form-check-label" for="typeCourse">
                                        Course
                                    </label>
                                </div>
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="text" id="typeText">
                                    <label class="form-check-label" for="typeText">
                                        Text course
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar-card mt-30">
                            <h3 class="sidebar-title font-weight-bold text-primary mb-20">More options</h3>
                            <div class="sidebar-content">
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="subscribe" id="moreSubscribe">
                                    <label class="form-check-label" for="moreSubscribe">
                                        Show only subscribe
                                    </label>
                                </div>
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="certificate" id="moreCertificate">
                                    <label class="form-check-label" for="moreCertificate">
                                        Show only certificate included
                                    </label>
                                </div>
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="quiz" id="moreQuiz">
                                    <label class="form-check-label" for="moreQuiz">
                                        Show only courses with quiz
                                    </label>
                                </div>
                                <div class="form-check mb-10">
                                    <input class="form-check-input" type="checkbox" value="featured" id="moreFeatured">
                                    <label class="form-check-label" for="moreFeatured">
                                        Show only featured courses
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar-card mt-30">
                            <button type="button" class="btn btn-primary btn-block" id="filterItemsBtn">Filter items</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

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
@endsection

@push('styles')
<style>
.sidebar {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.sidebar-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.sidebar-title {
    color: #0056d2;
    font-size: 16px;
    margin-bottom: 15px;
}

.sidebar-content .form-check {
    margin-bottom: 10px;
}

.sidebar-content .form-check-label {
    font-size: 14px;
    color: #333;
    cursor: pointer;
}

.sidebar-content .form-check-input {
    margin-right: 8px;
}

#filterItemsBtn {
    width: 100%;
    padding: 12px;
    font-weight: bold;
}
</style>
@endpush
