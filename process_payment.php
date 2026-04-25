<?php
session_start();
include '../config.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check booking session
if (!isset($_SESSION['booking'])) {
    $_SESSION['message'] = "Booking session expired or invalid. Please book a car again.";
    header('Location: search_cars.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$booking = $_SESSION['booking'];
$car_id = $booking['car_id'];
$message = '';
$success = false;

// Fetch car info
$stmt = $conn->prepare("SELECT * FROM cars WHERE id=? LIMIT 1");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$car) {
    $_SESSION['message'] = "Car not found.";
    unset($_SESSION['booking']);
    header('Location: search_cars.php');
    exit();
}

// Handle Payment Submission
if (isset($_POST['pay'])) {
    $payment_method = $_POST['payment_method'];

    // Validate payment details
    if ($payment_method === 'Card') {
        $card_number = $_POST['card_number'] ?? '';
        $expiry      = $_POST['expiry'] ?? '';
        $cvv         = $_POST['cvv'] ?? '';
        if (!$card_number || !$expiry || !$cvv) {
            $message = "Please enter valid card details.";
        } else {
            $payment_status = "Paid";
            $success = true;
        }
    } elseif ($payment_method === 'UPI') {
        $upi_id = $_POST['upi_id'] ?? '';
        if (!$upi_id) {
            $message = "Please enter a valid UPI ID.";
        } else {
            $payment_status = "Paid";
            $success = true;
        }
    } else {
        $message = "Invalid payment method.";
    }

    // Save booking and update car availability
    if ($success) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO bookings 
                (user_id, car_id, start_date, end_date, total_amount, payment_method, payment_status, booking_status, notification_flag) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Confirmed', 0)");
            $stmt->bind_param(
                "iissdss",
                $user_id,
                $car_id,
                $booking['start_date'],
                $booking['end_date'],
                $booking['total_amount'],
                $payment_method,
                $payment_status
            );
            $stmt->execute();
            $stmt->close();

            // Update car availability
            $update_stmt = $conn->prepare("UPDATE cars SET availability='Booked' WHERE id=?");
            $update_stmt->bind_param("i", $car_id);
            $update_stmt->execute();
            $update_stmt->close();

            $conn->commit();

            // Clear booking session and redirect
            unset($_SESSION['booking']);
            $_SESSION['message'] = "Payment successful! Your booking is confirmed.";
            header("Location: booking_history.php");
            exit();

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $message = "Payment failed due to a database error. Please try again.";
        }
    }
}
?>

<?php include 'Header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm p-4">
                <h3 class="text-center mb-4">Confirm & Pay for Your Rental</h3>

                <?php if ($message): ?>
                    <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?> text-center">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <div class="card mb-4 border-primary">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-primary"><?= htmlspecialchars($car['car_name']) ?></h5>
                        <p class="mb-1"><strong>Dates:</strong> <?= $booking['start_date'] ?> to <?= $booking['end_date'] ?></p>
                        <p class="mb-1"><strong>Daily Rate:</strong> ₹<?= number_format($car['rate_per_day'], 2) ?></p>
                        <h4 class="fw-bold mt-3">Total Amount: ₹<?= number_format($booking['total_amount'], 2) ?></h4>
                    </div>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="form-control" onchange="togglePaymentFields()" required>
                            <option value="">Select Method</option>
                            <option value="Card">Card</option>
                            <option value="UPI">UPI</option>
                        </select>
                    </div>

                    <!-- Card Details -->
                    <div id="card_fields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" name="card_number" class="form-control" placeholder="XXXX-XXXX-XXXX-XXXX">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="month" name="expiry" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CVV</label>
                                <input type="password" name="cvv" class="form-control" maxlength="3">
                            </div>
                        </div>
                    </div>

                    <!-- UPI Details -->
                    <div id="upi_fields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">UPI ID</label>
                            <input type="text" name="upi_id" class="form-control" placeholder="example@upi">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" name="pay" class="btn btn-success btn-lg">Pay Now</button>
                        <a href="search_cars.php" class="btn btn-secondary">Cancel Booking</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePaymentFields() {
    var method = document.getElementById('payment_method').value;
    document.getElementById('card_fields').style.display = method === 'Card' ? 'block' : 'none';
    document.getElementById('upi_fields').style.display = method === 'UPI' ? 'block' : 'none';
}
</script>

<?php include 'Footer.php'; ?>
