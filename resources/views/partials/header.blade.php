<!-- Header partial: Main Navbar only -->
<nav id="navbar" class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a class="navbar-brand navbar-order d-flex align-items-center justify-content-center mr-0 ml-auto" href="/">
                <img src="/store/1/dark-logo.png" alt="site logo" style="height: 40px; width: auto; object-fit: contain; max-width: 100%;">
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
            <div class="nav-icons-or-start-live navbar-order d-flex align-items-center justify-content-end" style="display: flex !important;">
                <div class="d-flex align-items-center mr-3" style="display: flex !important;">
                    <a href="/login" class="btn btn-outline-primary btn-sm mr-2" style="display: inline-block !important; visibility: visible !important;">Login</a>
                    <a href="/register" class="btn btn-primary btn-sm" style="display: inline-block !important; visibility: visible !important;">Register</a>
                </div>
                <!-- ...cart and notification dropdowns as in index.html... -->
            </div>
        </div>
    </div>
</nav>
