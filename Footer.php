<style>
    /* CSS for the sticky footer */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .content-wrapper {
        flex: 1;
        margin-top:100px;
    }
    /* Updated Footer Styles */
    :root {
        --footer-bg: #2c3e50;
        --accent-color: #007BFF;
        --text-color: #ecf0f1;
        --hover-color: #e67e22;
        --divider-color: #495057;
    }
    .footer-custom {
        background-color: var(--footer-bg);
        box-shadow: 0 -4px 12px rgba(0,0,0,0.3);
        padding: 1.5rem 0; /* Reduced padding for a more compact footer */
        border-top: 2px solid var(--accent-color);
        color: var(--text-color);
    }
    .footer-title {
        color: var(--accent-color);
        font-weight: 700;
        font-size: 1.1rem; /* Smaller font size for titles */
        margin-bottom: 0.75rem;
    }
    .footer-text {
        color: rgba(236, 240, 241, 0.8);
        font-size: 0.85rem; /* Smaller font for contact details */
    }
    .footer-link {
        color: var(--text-color);
        transition: color 0.3s ease, transform 0.2s ease;
        text-decoration: none;
        font-size: 0.85rem; /* Smaller font for links */
        display: block;
        margin-bottom: 0.25rem;
    }
    .footer-link:hover {
        color: var(--accent-color);
        text-decoration: none;
        transform: translateX(3px);
    }
    .footer-link i {
        margin-right: 8px;
        transition: transform 0.2s ease;
    }
    .footer-link:hover i {
        transform: scale(1.1);
    }
    .social-icons a {
        color: var(--text-color);
        font-size: 1.25rem; /* Adjusted social icon size */
        transition: color 0.3s ease, transform 0.2s ease;
    }
    .social-icons a:hover {
        color: var(--accent-color);
        transform: translateY(-2px);
    }
    .divider-custom {
        background-color: var(--divider-color);
        height: 1px;
        margin: 1.5rem 0;
    }
</style>
<div class="content-wrapper">
<footer class="footer-custom text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-4 mx-auto mt-2 text-start">
                <h5 class="text-uppercase mb-4 font-weight-bold footer-title">Car Rental Service</h5>
                <p class="footer-text">Your reliable partner for car rentals. We offer fast, safe, and affordable services available anytime, anywhere.</p>
            </div>

            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3 text-start">
                <h5 class="text-uppercase mb-4 font-weight-bold footer-title">Useful Links</h5>
                <a href="dashboard.php" class="footer-link"><i class="bi bi-caret-right-fill"></i>Dashboard</a>
                <a href="search_cars.php" class="footer-link"><i class="bi bi-caret-right-fill"></i>Search Cars</a>
                <a href="booking_history.php" class="footer-link"><i class="bi bi-caret-right-fill"></i>Booking History</a>
                <a href="feedback.php" class="footer-link"><i class="bi bi-caret-right-fill"></i>Feedback</a>
                <a href="profile.php" class="footer-link"><i class="bi bi-caret-right-fill"></i>Profile</a>
                <a href="privacy_policy.php" class="footer-link"><i class="bi bi-caret-right-fill"></i>Privacy Policy</a>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3 text-start">
                <h5 class="text-uppercase mb-4 font-weight-bold footer-title">Contact</h5>
                <p class="footer-text"><i class="bi bi-geo-alt-fill me-2"></i> Ganpat Vidyanagar, Mehsana-Gozaria Highway, Kherava, Pin code - 384012, Gujarat</p>
                <p class="footer-text"><i class="bi bi-telephone-fill me-2"></i> +91 9876543210 +91 8200219804</p>
                <p class="footer-text"><i class="bi bi-envelope-fill me-2"></i> support@carrental.com</p>
            </div>
        </div>

        <hr class="divider-custom">

        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8 text-center text-md-start">
                <p class="footer-text">© <?php echo date('Y'); ?> Car Rental Service. All Rights Reserved.</p>
            </div>
            <div class="col-md-5 col-lg-4">
                <div class="text-center text-md-end social-icons mt-3 mt-md-0">
                    <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>