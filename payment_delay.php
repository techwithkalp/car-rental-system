<?php
session_start();
include '../config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['id'] ?? 0);
$message = '';

// Check if booking exists, belongs to the user, and is eligible for delay
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND booking_status = 'Pending' AND payment_status = 'Unpaid'");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    $_SESSION['message'] = "Invalid booking, or this booking is not eligible for a payment delay.";
    header('Location: booking_history.php');
    exit;
}

// Handle form submission
if (isset($_POST['request_delay'])) {
    $delay_reason = mysqli_real_escape_string($conn, $_POST['delay_reason']);
    $new_due_date = mysqli_real_escape_string($conn, $_POST['new_due_date']);
    
    // Validate that the new date is in the future
    if (strtotime($new_due_date) < time()) {
        $message = "The new due date must be a future date.";
    } else {
        $update_stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Payment Delayed', payment_delay_reason = ?, new_due_date = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $delay_reason, $new_due_date, $booking_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Payment delay request submitted successfully. We will review your request.";
            header('Location: booking_history.php');
            exit;
        } else {
            $message = "Failed to submit request. Please try again.";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Payment Delay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- START: Navigation Bar (Content of Header.php) -->
<?php include 'Header.php'; ?>
<!-- END: Navigation Bar -->

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-4 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Request Payment Delay</h3>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted text-center">
                        You are requesting a payment delay for Booking ID: <strong><?= htmlspecialchars($booking['id']) ?></strong>.
                    </p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="new_due_date" class="form-label">New Payment Due Date</label>
                            <input type="date" id="new_due_date" name="new_due_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="delay_reason" class="form-label">Reason for Delay</label>
                            <textarea id="delay_reason" name="delay_reason" rows="4" class="form-control" placeholder="Please provide a brief reason for your request..." required></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="request_delay" class="btn btn-warning">Submit Delay Request</button>
                            <a href="booking_history.php" class="btn btn-secondary mt-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- START: Footer (Content of Footer.php) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- END: Footer -->

</body>
</html>
