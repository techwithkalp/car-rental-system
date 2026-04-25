<?php
session_start();
include '../config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['id'] ?? 0); // Ensure integer

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("
    SELECT b.id, b.user_id, b.car_id, b.start_date, b.end_date, b.total_amount, b.payment_status, b.created_at,
           u.username, u.email, u.mobile, 
           c.car_name, c.model, c.type, c.fuel_type, c.seating_capacity, c.rate_per_day
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN cars c ON b.car_id = c.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking_result = $stmt->get_result();
$booking = $booking_result->fetch_assoc();
$stmt->close();

if (!$booking) {
    $_SESSION['message'] = "Invalid invoice ID or you do not have permission to view it.";
    header('Location: booking_history.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Booking #<?= htmlspecialchars($booking['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .d-print-none {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
            }
        }
        .invoice-card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">

<!-- START: Navigation Bar (Content of Header.php) -->
<!-- <?php include 'Header.php'; ?> -->
<!-- END: Navigation Bar -->

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 invoice-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="card-title fw-bold text-primary">Invoice</h2>
                        <div>
                            <p class="text-muted mb-0">Invoice ID: <strong><?= htmlspecialchars($booking['id']) ?></strong></p>
                            <p class="text-muted mb-0">Date: <strong><?= date('F j, Y', strtotime($booking['created_at'])) ?></strong></p>
                        </div>
                    </div>

                    <h5 class="fw-bold text-secondary mb-3">Customer Details</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Name:</strong> <?= htmlspecialchars($booking['username']) ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?><br>
                            <strong>Mobile:</strong> <?= htmlspecialchars($booking['mobile']) ?><br>
                        </div>
                    </div>

                    <h5 class="fw-bold text-secondary mb-3">Booking Details</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Car Name:</strong> <?= htmlspecialchars($booking['car_name']) ?><br>
                            <strong>Model:</strong> <?= htmlspecialchars($booking['model']) ?><br>
                            <strong>Type:</strong> <?= htmlspecialchars($booking['type']) ?><br>
                            <strong>Fuel/Seats:</strong> <?= htmlspecialchars($booking['fuel_type']) ?> | <?= htmlspecialchars($booking['seating_capacity']) ?> Seats<br>
                        </div>
                        <div class="col-md-6">
                            <strong>Start Date:</strong> <?= htmlspecialchars($booking['start_date']) ?><br>
                            <strong>End Date:</strong> <?= htmlspecialchars($booking['end_date']) ?><br>
                            <strong>Rate/Day:</strong> ₹<?= number_format($booking['rate_per_day'], 2) ?><br>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold text-success">Total Amount</h4>
                        <h4 class="fw-bold text-success">₹<?= number_format($booking['total_amount'], 2) ?></h4>
                    </div>

                    <div class="row text-center">
                        <div class="col-md-12">
                            <h5 class="fw-bold">Payment Status:</h5>
                            <?php if ($booking['payment_status'] === 'Paid'): ?>
                                <span class="badge bg-success p-2 fs-6">Paid</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark p-2 fs-6">Unpaid</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-print-none mt-4">
                <button onclick="window.print()" class="btn btn-primary btn-lg">Print Invoice</button>
                <a href="booking_history.php" class="btn btn-secondary mt-2">Back to Booking History</a>
            </div>
        </div>
    </div>
</div>

<!-- START: Footer (Content of Footer.php) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- END: Footer -->

</body>
</html>
