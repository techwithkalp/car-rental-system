<?php
session_start();
include '../config.php';

// User login check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$car_id = intval($_GET['car_id'] ?? 0);

if (!$car_id) {
    $_SESSION['message'] = "Invalid car selected.";
    header('Location: search_cars.php');
    exit();
}

// Get car (must be available)
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND availability = 'Available' LIMIT 1");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$car) {
    $_SESSION['message'] = "Car not available.";
    header('Location: search_cars.php');
    exit();
}

$message = '';

if (isset($_POST['confirm_booking'])) {
    $start_date = $_POST['start_date'] ?? '';
    $end_date   = $_POST['end_date'] ?? '';

    // basic validation
    if (empty($start_date) || empty($end_date)) {
        $message = "Please select start and end date.";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $message = "Start date cannot be later than end date.";
    } else {
        // compute days (inclusive)
        $days = (int) ((strtotime($end_date) - strtotime($start_date)) / (60*60*24)) + 1;
        if ($days < 1) $days = 1;
        $total_amount = $days * (float)$car['rate_per_day'];

        // Insert booking as a Pending request
        $payment_method = '';
        $payment_status = 'Pending';
        $booking_status = 'Pending';
        $notification_flag = 0;

        $ins = $conn->prepare("INSERT INTO bookings (user_id, car_id, start_date, end_date, total_amount, payment_method, payment_status, booking_status, notification_flag) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->bind_param("iissdsssi", $user_id, $car_id, $start_date, $end_date, $total_amount, $payment_method, $payment_status, $booking_status, $notification_flag);

        if ($ins->execute()) {
            $ins->close();
            $_SESSION['message'] = "Your booking request has been submitted successfully. You will be notified once approved by admin.";
            header('Location: booking_history.php');
            exit();
        } else {
            $message = "Failed to submit booking request. Please try again.";
            $ins->close();
        }
    }
}

include 'Header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h3 class="text-center mb-4">Book: <?= htmlspecialchars($car['car_name']) ?></h3>

                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="confirm_booking" class="btn btn-success btn-lg">Submit Booking Request</button>
                        <a href="search_cars.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'Footer.php'; ?>
