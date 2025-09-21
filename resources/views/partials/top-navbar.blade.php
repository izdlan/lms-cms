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
            <div class="d-flex">
                <div class="dropdown">
                    <button type="button" disabled class="btn btn-transparent dropdown-toggle" id="navbarShopingCart" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="shopping-cart" width="20" height="20" class="mr-10"></i>
                    </button>

                    <div class="dropdown-menu" aria-labelledby="navbarShopingCart">
                        <div class="d-md-none border-bottom mb-20 pb-10 text-right">
                            <i class="close-dropdown" data-feather="x" width="32" height="32" class="mr-10"></i>
                        </div>
                        <div class="h-100">
                            <div class="navbar-shopping-cart h-100" data-simplebar>
                                <div class="d-flex align-items-center text-center py-50">
                                    <i data-feather="shopping-cart" width="20" height="20" class="mr-10"></i>
                                    <span class="">Your cart is empty</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-left mx-5 mx-lg-15"></div>

                <div class="dropdown">
                    <button type="button" class="btn btn-transparent dropdown-toggle" disabled id="navbarNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="bell" width="20" height="20" class="mr-10"></i>
                    </button>

                    <div class="dropdown-menu pt-20" aria-labelledby="navbarNotification">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-auto navbar-notification-card" data-simplebar>
                                <div class="d-md-none border-bottom mb-20 pb-10 text-right">
                                    <i class="close-dropdown" data-feather="x" width="32" height="32" class="mr-10"></i>
                                </div>

                                <div class="d-flex align-items-center text-center py-50">
                                    <i data-feather="bell" width="20" height="20" class="mr-10"></i>
                                    <span class="">Empty notifications</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center ml-md-50">
                <a href="/login" class="py-5 px-10 text-dark-blue font-14">Login</a>
            </div>
        </div>
    </div>
</div>
