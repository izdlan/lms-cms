<!-- Header partial: Top Navbar and Main Navbar -->
<div class="top-navbar d-flex border-bottom">
    <div class="container d-flex justify-content-between flex-column flex-lg-row">
        <div class="top-contact-box border-bottom d-flex flex-column flex-md-row align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-between justify-content-md-center">
                <form action="/locale" method="post" class="mr-15 mx-md-20">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="locale">
                    <div class="language-select">
                        <div id="localItems" data-selected-country="US" data-countries='{"US":"English","MS":"Malay"}'></div>
                    </div>
                </form>
                <form action="/search" method="get" class="form-inline my-2 my-lg-0 navbar-search position-relative">
                    <input class="form-control mr-5 rounded" type="text" name="search" placeholder="Search..." aria-label="Search">
                    <button type="submit" class="btn-transparent d-flex align-items-center justify-content-center search-icon">
                        <i data-feather="search" width="20" height="20" class="mr-10"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="xs-w-100 d-flex align-items-center justify-content-between ">
            <div class="d-flex align-items-center ml-md-50">
                <a href="/login" class="py-5 px-10 mr-10 text-dark-blue font-14">Login</a>
                <a href="/register" class="py-5 px-10 text-dark-blue font-14">Register</a>
            </div>
        </div>
    </div>
</div>
<div id="navbarVacuum"></div>
<nav id="navbar" class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a class="navbar-brand navbar-order d-flex align-items-center justify-content-center mr-0 ml-auto" href="/">
                <img src="/store/1/dark-logo.png" alt="site logo" style="height: 40px; width: auto; object-fit: contain;">
            </a>
            <button class="navbar-toggler navbar-order" type="button" id="navbarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="mx-lg-30 d-none d-lg-flex flex-grow-1 navbar-toggle-content " id="navbarContent">
                <div class="navbar-toggle-header text-right d-lg-none">
                    <button class="btn-transparent" id="navbarClose">
                        <i data-feather="x" width="32" height="32"></i>
                    </button>
                </div>
                <ul class="navbar-nav mr-auto d-flex align-items-center">
                    <li class="mr-lg-25">
                        <div class="menu-category">
                            <ul>
                                <li class="cursor-pointer user-select-none d-flex xs-categories-toggle">
                                    <i data-feather="grid" width="20" height="20" class="mr-10 d-none d-lg-block"></i> Categories
                                    <ul class="cat-dropdown-menu">
                                        <li>
                                            <a href="/categories/sample-category" class="">
                                                <div class="d-flex align-items-center">
                                                    <img src="/store/1/default_images/categories_icons/anchor.png" class="cat-dropdown-menu-icon mr-10" alt="Sample Category icon"> Sample Category
                                                </div>
                                            </a>
                                        </li>
                                        <!-- ...repeat for all categories as in index.html... -->
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/classes?sort=newest">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/announcements">Announcements</a>
                    </li>
                </ul>
            </div>
            <div class="nav-icons-or-start-live navbar-order d-flex align-items-center justify-content-end">
                <!-- ...cart and notification dropdowns as in index.html... -->
            </div>
        </div>
    </div>
</nav>
