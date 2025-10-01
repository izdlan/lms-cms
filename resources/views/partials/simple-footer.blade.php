<!-- Simple Footer -->
<footer class="simple-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <a href="/">
                    <img src="/store/1/white-logo.png" alt="Olympia Education" class="footer-logo">
                </a>
            </div>
            <div class="footer-info">
                <p>&copy; {{ date('Y') }} Olympia Education. All rights reserved.</p>
                <div class="footer-contact">
                    <span><i class="fas fa-phone"></i> +6011 6254 2929</span>
                    <span><i class="fas fa-envelope"></i> admission@olympia-education.com</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.simple-footer {
    background: #000000;
    color: white;
    padding: 2rem 0;
    margin-top: auto;
    border-top: 1px solid #333333;
    position: relative;
    z-index: 1001;
    clear: both;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-brand {
    display: flex;
    align-items: center;
    position: relative;
    z-index: 1002;
}

.footer-brand a {
    text-decoration: none;
    display: block;
}

.footer-logo {
    height: 40px;
    width: auto;
    object-fit: contain;
    display: block;
    max-width: 100%;
    opacity: 1;
    visibility: visible;
}

.footer-info {
    text-align: right;
}

.footer-info p {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    color: #bdc3c7;
}

.footer-contact {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.footer-contact span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #ecf0f1;
}

.footer-contact i {
    color: #ffffff;
}

/* Footer positioning for pages with sidebar */
.simple-footer {
    margin-left: 0;
}

/* Only apply margin-left when there's a sidebar present */
body:has(.sidebar) .simple-footer {
    margin-left: 200px;
}

@media (max-width: 768px) {
    .simple-footer {
        margin-left: 0;
    }
    
    .footer-content {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-info {
        text-align: center;
    }
    
    .footer-contact {
        justify-content: center;
    }
}
</style>
