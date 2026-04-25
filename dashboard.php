<!-- dashboard.php -->
<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$notification = '';
$booking_id = null;

// Check for any approved bookings with notification flag
$query = "SELECT * FROM bookings WHERE user_id = ? AND notification_flag = 1 AND booking_status = 'Approved'";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    $booking_id = $booking['id'];
    $notification = "Your booking has been approved! Click below to proceed with payment.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'Header.php'; ?>

<div class="container mt-4">
    <?php if ($notification): ?>
        <div class="alert alert-success text-center">
            <p class="mb-2"><?= htmlspecialchars($notification); ?></p>
            <a href="notification.php?booking_id=<?= $booking_id; ?>" class="btn btn-primary">Proceed to Payment</a>
        </div>
    <?php endif; ?>

    <h2 class="mb-3">Welcome, <span class="text-primary"><?= htmlspecialchars($_SESSION['username']); ?>!</span></h2>
    <p class="lead text-secondary">Manage your car rentals, search for new vehicles, and check your booking history below.</p>

    <div class="row mt-5">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100 text-white bg-primary">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-4">Search Cars 🚗</h5>
                    <p class="card-text">Find cars by model, type, or location for your next trip.</p>
                    <a href="search_cars.php" class="btn btn-light mt-auto fw-bold">Start New Search</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100 text-white bg-success">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-4">Booking History 📅</h5>
                    <p class="card-text">View details of your previous, current, and upcoming rentals.</p>
                    <a href="booking_history.php" class="btn btn-light mt-auto fw-bold">View History</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100 text-white bg-secondary">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-4">Profile Settings ⚙️</h5>
                    <p class="card-text">Update your personal details, password, and manage preferences.</p>
                    <a href="profile.php" class="btn btn-light mt-auto fw-bold">Manage Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
