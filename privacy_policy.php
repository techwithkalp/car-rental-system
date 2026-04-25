<?php
session_start(); // Start the session to check login status
include '../config.php';
// Include Header.php which contains the opening <body> tag and navigation
include 'Header.php'; 

// --- Logic to check if user is logged in ---
$is_logged_in = isset($_SESSION['user_id']);
?>

<div class="container">
    <div class="policy-container">
        
        <h1 class="text-center">Car Rental Terms, Privacy Policy & Rules</h1>
        <p class="text-center text-muted mb-5">Last Updated: <?php echo date('F d, Y'); ?></p>

        <div class="text-center mb-5">
            <button class="btn btn-primary btn-lg" onclick="window.print()">
                <i class="bi bi-download me-2"></i> Download/Print Rules
            </button>
            <p class="mt-2 text-muted small">Use your browser's print function (Ctrl+P or Cmd+P) to save this page as a PDF.</p>
        </div>
        
        <?php if ($is_logged_in): ?>
            
            <h2>1. Privacy Policy & Data Handling</h2>
            <p><strong>[Car Rental]</strong> ("we," "our," or "us") is committed to protecting your privacy. This section explains how we collect, use, and safeguard your personal information.</p>

            <h3 class="mt-4">1.1. Information We Collect</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-shield-lock-fill"></i> Personal Details: Name, address, phone number, and email.</li>
                <li class="list-group-item"><i class="bi bi-shield-lock-fill"></i> Identification: Driver's license details and a copy of your [Required ID: e.g., National ID/Passport].</li>
                <li class="list-group-item"><i class="bi bi-shield-lock-fill"></i> Payment Data: Credit card number, billing address, and transaction history.</li>
            </ul>

            <h3 class="mt-4">1.2. How We Use Your Information</h3>
            <p>Your data is used solely for the purpose of fulfilling your car rental agreement, processing payments, vehicle tracking for safety, and for communication regarding your booking.</p>
            
            <h3 class="mt-4">1.3. Data Security</h3>
            <p>We employ [Describe Security Measures, e.g., SSL encryption, secure servers] to protect your data. However, no method of transmission over the Internet is 100% secure.</p>

            <h2>2. Car Rental Rules and Regulations</h2>
            <p>By making a reservation with us, you agree to the following terms and conditions:</p>

            <h3 class="mt-4">2.1. Driver Requirements</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-car-front-fill"></i> Minimum Age: The primary driver must be at least [Minimum Age, e.g., 21] years old.</li>
                <li class="list-group-item"><i class="bi bi-car-front-fill"></i> License: A valid, full, and current driver's license held for at least [Minimum Years, e.g., 1] year is required.</li>
                <li class="list-group-item"><i class="bi bi-car-front-fill"></i> Identification: A valid form of photo ID (Passport/National ID) and a major credit card in the driver's name are mandatory.</li>
            </ul>
            
            <h3 class="mt-4">2.2. Rental Conditions</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-clock"></i> Duration: The minimum rental period is [Minimum Rental Time, e.g., 24 hours]. Late returns may incur an extra day's charge.</li>
                <li class="list-group-item"><i class="bi bi-fuel-pump-fill"></i> Fuel Policy: All vehicles are provided with a full tank and must be returned full. Otherwise, a refueling service charge plus the cost of missing fuel will be applied.</li>
                <li class="list-group-item"><i class="bi bi-currency-dollar"></i> Security Deposit: A refundable security deposit of [Amount, e.g., ₹5000] will be authorized on the primary driver’s credit card upon pickup.</li>
                <li class="list-group-item"><i class="bi bi-truck"></i> Geographical Restrictions: Vehicles may only be driven within the [Allowed Area, e.g., state borders, country]. Driving outside this area voids the insurance.</li>
            </ul>

            <h3 class="mt-4">2.3. Prohibited Uses</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-x-octagon-fill text-danger"></i> Sub-leasing the vehicle to any third party.</li>
                <li class="list-group-item"><i class="bi bi-x-octagon-fill text-danger"></i> Driving under the influence of alcohol or drugs.</li>
                <li class="list-group-item"><i class="bi bi-x-octagon-fill text-danger"></i> Using the vehicle for any illegal purpose or for racing/speed testing.</li>
            </ul>

            <h2>3. Fees, Charges, and Penalties</h2>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-cash-stack"></i> Cancellation: Free cancellation up to [Time, e.g., 48 hours] before pickup. Cancellations after this time incur a [Fee/Percentage] charge.</li>
                <li class="list-group-item"><i class="bi bi-cash-stack"></i> Traffic Violations: All fines, tolls, and parking tickets incurred during the rental period are the sole responsibility of the renter. An administration fee of [Amount, e.g., ₹500] per fine will be applied.</li>
                <li class="list-group-item"><i class="bi bi-cash-stack"></i> Cleaning Fee: A fee of up to [Amount, e.g., ₹2000] may be charged for excessive dirt, spills, or smoking odors in the vehicle. (Smoking is strictly prohibited).</li>
            </ul>

            <h2 class="mt-5">4. Contact Us</h2>
            <p>If you have any questions regarding this policy or your rental, please contact us:</p>
            <p>Email: <strong>support@carrental.com</strong></p>
            <p>Phone: <strong>+91 9979311208 +91 8200219804</strong></p>
            <p>Address: <strong>Ganpat Vidyanagar, Mehsana-Gozaria Highway, Kherava, Pin code - 384012, Gujarat</strong></p>

        <?php else: ?>
            
            <div class="alert alert-warning text-center mt-5 p-5">
                <i class="bi bi-lock-fill fs-1 d-block mb-3"></i>
                <h4 class="alert-heading">Full Policy Details Restricted</h4>
                <p>The detailed Privacy Policy and Rental Rules are only viewable by logged-in users to ensure agreement during the booking process.</p>
                <hr>
                <a href="index.php" class="btn btn-warning fw-bold"><i class="bi bi-box-arrow-in-right"></i> Login Now</a>
            </div>

        <?php endif; ?>
        
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
    @media print {
        .policy-container {
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .navbar-custom, .btn-lg, .text-muted.small, footer {
            display: none !important;
        }
    }
</style>
</body>
</html>