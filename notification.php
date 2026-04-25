<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['booking_id'] ?? 0);

if (!$booking_id) {
    header("Location: dashboard.php");
    exit();
}

// Fetch booking details
$stmt = $conn->prepare("
    SELECT b.*, c.car_name, c.model, c.rate_per_day 
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    header("Location: dashboard.php");
    exit();
}

// Reset notification flag so it won’t keep showing
$update = $conn->prepare("UPDATE bookings SET notification_flag = 0 WHERE id = ?");
$update->bind_param("i", $booking_id);
$update->execute();
$update->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'Header.php'; ?>

<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h3 class="text-center mb-4">Booking Approved ✅</h3>
        
        <p><strong>Car:</strong> <?= htmlspecialchars($booking['car_name']); ?> (<?= htmlspecialchars($booking['model']); ?>)</p>
        <p><strong>Start Date:</strong> <?= htmlspecialchars($booking['start_date']); ?></p>
        <p><strong>End Date:</strong> <?= htmlspecialchars($booking['end_date']); ?></p>
        <p><strong>Total Amount:</strong> ₹<?= number_format($booking['total_amount'], 2); ?></p>

        <div class="text-center mt-4">
            <a href="payment.php?booking_id=<?= $booking['id']; ?>" class="btn btn-success btn-lg">Proceed to Payment</a>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include 'Footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
