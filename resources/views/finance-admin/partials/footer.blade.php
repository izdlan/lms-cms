<!-- Finance Admin Footer -->
<footer class="finance-admin-footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="footer-content">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-md-4">
                            <div class="footer-logo">
                                <a href="{{ route('finance-admin.dashboard') }}">
                                    <img src="/store/1/white-logo.png" class="img-cover" alt="Olympia Education Logo" style="height: 40px;">
                                </a>
                            </div>
                        </div>

                        <!-- Copyright -->
                        <div class="col-md-4 text-center">
                            <p class="mb-0 text-white-50">
                                &copy; {{ date('Y') }} Olympia Education. All rights reserved.
                            </p>
                        </div>

                        <!-- Quick Links -->
                        <div class="col-md-4">
                            <div class="footer-links text-md-end d-flex flex-column">
                                <a href="{{ route('finance-admin.dashboard') }}" class="text-white-50">
                                    <i data-feather="home" width="14" height="14" class="me-1"></i>
                                    Dashboard
                                </a>
                                <a href="{{ route('finance-admin.students') }}" class="text-white-50">
                                    <i data-feather="users" width="14" height="14" class="me-1"></i>
                                    Students
                                </a>
                                <a href="{{ route('finance-admin.pending-payments') }}" class="text-white-50">
                                    <i data-feather="clock" width="14" height="14" class="me-1"></i>
                                    Pending Payments
                                </a>
                                <div class="footer-version mt-2">
                                    <span class="text-white-50" style="font-size: 0.8rem;">LMS Finance Admin Panel v1.0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <div class="footer-info">
                        <span class="text-white-50 me-3">
                            <i data-feather="phone" width="14" height="14" class="me-1"></i>
                            +6011 6254 2929
                        </span>
                        <span class="text-white-50">
                            <i data-feather="mail" width="14" height="14" class="me-1"></i>
                            admission@olympia-education.com
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.finance-admin-footer {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: #fff;
    margin-top: auto;
    padding: 2rem 0 0;
    flex-shrink: 0;
}

.footer-content {
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-logo img {
    max-height: 40px;
    width: auto;
}

.footer-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.footer-links a {
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.7);
}

.footer-links a:hover {
    color: #fff !important;
    text-decoration: none;
}

.footer-links a i {
    margin-right: 0.5rem;
    width: 14px;
    height: 14px;
}

.footer-bottom {
    padding: 1rem 0;
}

.footer-info span {
    font-size: 0.9rem;
}

.footer-version {
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    .finance-admin-footer {
        padding: 1.5rem 0 0;
    }
    
    .footer-content .row > div {
        text-align: center !important;
        margin-bottom: 1rem;
    }
    
    .footer-links {
        text-align: center !important;
        align-items: center !important;
    }
    
    .footer-bottom .row > div {
        text-align: center !important;
    }
    
    .footer-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .footer-version {
        margin-top: 1rem;
    }
}
</style>

