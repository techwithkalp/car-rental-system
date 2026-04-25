</div>
<?php
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$basePrefix = (strpos($scriptName, '/admin/') === 0 || strpos($scriptName, '/car_rental_system/admin/') !== false) ? '' : 'admin/';
$assetPrefix = (strpos($scriptName, '/admin/') === 0 || strpos($scriptName, '/car_rental_system/admin/') !== false) ? '../assets/' : 'assets/';
?>
<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .footer-custom {
        background-color: #2d3436;
        color: #dfe6e9;
        box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
    }
    .text-primary-custom {
        color: #55efc4 !important;
    }
    .text-secondary-custom {
        color: #dfe6e9 !important;
    }
    .footer-link {
        color: #dfe6e9;
        transition: color 0.3s ease;
    }
    .footer-link:hover {
        color: #ffeaa7;
        text-decoration: none;
    }
    .social-link {
        color: #dfe6e9 !important;
        transition: color 0.3s ease;
    }
    .social-link:hover {
        color: #ffeaa7 !important;
    }
</style>
<footer class="footer-custom pt-5 pb-4 mt-5">
    <div class="container text-md-left">
        <div class="row text-md-left">

            <div class="col-md-6 col-lg-6 col-xl-6 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary-custom">Admin Panel</h5>
                <p class="text-secondary-custom">Manage your car rental service with ease.</p>
            </div>

            <div class="col-md-6 col-lg-6 col-xl-6 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary-custom">Useful Links</h5>
                <p><a href="<?php echo $basePrefix; ?>dashboard.php" class="footer-link text-decoration-none">Dashboard</a></p>
                <p><a href="<?php echo $basePrefix; ?>manage_cars.php" class="footer-link text-decoration-none">Manage Cars</a></p>
                <p><a href="<?php echo $basePrefix; ?>manage_bookings.php" class="footer-link text-decoration-none">Manage Bookings</a></p>
                <p><a href="<?php echo $basePrefix; ?>manage_users.php" class="footer-link text-decoration-none">Manage Users</a></p>
            </div>
        </div>

        <hr class="bg-secondary">

        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8">
                <p class="text-secondary-custom">© <?php echo date('Y'); ?> Admin Panel. All Rights Reserved.</p>
            </div>
            <div class="col-md-5 col-lg-4">
                <div class="text-center text-md-right">
                    <a href="#" class="social-link me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="social-link me-3"><i class="bi bi-twitter fs-5"></i></a>
                    <a href="#" class="social-link me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-linkedin fs-5"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

</script>
<script src="<?php echo $assetPrefix; ?>js/jquery.min.js"></script>
<script src="<?php echo $assetPrefix; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>
