<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['booking_id'] ?? 0);
$message = '';
$success = false;

// If booking_id not provided
if (!$booking_id) {
    echo "<!doctype html><html><body><div style='margin:40px;font-family:Arial, sans-serif;'><h4>Invalid booking session.</h4><p>Your booking session has expired or is invalid. Please book a car again.</p><a href='search_cars.php'>Go Back</a></div></body></html>";
    exit();
}

// Load booking
$stmt = $conn->prepare("SELECT b.*, c.car_name, c.rate_per_day FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.id = ? AND b.user_id = ? LIMIT 1");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    $_SESSION['message'] = "Booking not found or not authorized.";
    header('Location: booking_history.php');
    exit();
}

// Only allow payment if admin approved
if ($booking['booking_status'] !== 'Approved') {
    $_SESSION['message'] = "This booking is not yet approved. Please wait for admin approval.";
    header('Location: booking_history.php');
    exit();
}

// If already paid
if ($booking['payment_status'] === 'Paid') {
    $_SESSION['message'] = "Payment already made for this booking.";
    header('Location: booking_history.php');
    exit();
}

// Handle Payment Submission
if (isset($_POST['pay'])) {
    $payment_method = $_POST['payment_method'] ?? '';

    if ($payment_method === 'Card') {
        $card_number = trim($_POST['card_number'] ?? '');
        $expiry      = trim($_POST['expiry'] ?? '');
        $cvv         = trim($_POST['cvv'] ?? '');
        if ($card_number === '' || $expiry === '' || $cvv === '') {
            $message = "Please fill in complete card details.";
        } else {
            $payment_status = 'Paid';
            $success = true;
        }
    } elseif ($payment_method === 'UPI') {
        $upi_id = trim($_POST['upi_id'] ?? '');
        if ($upi_id === '') {
            $message = "Please provide a valid UPI ID.";
        } else {
            $payment_status = 'Paid';
            $success = true;
        }
    } else {
        $message = "Invalid payment method.";
    }

    if ($success) {
        $conn->begin_transaction();
        try {
            // Update booking
            $stmt = $conn->prepare("UPDATE bookings SET payment_method = ?, payment_status = ?, booking_status = 'Confirmed', notification_flag = 0 WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ssii", $payment_method, $payment_status, $booking_id, $user_id);
            $stmt->execute();
            $stmt->close();

            // Update car to Booked
            $update_car = $conn->prepare("UPDATE cars SET availability = 'Booked' WHERE id = ?");
            $update_car->bind_param("i", $booking['car_id']);
            $update_car->execute();
            $update_car->close();

            $conn->commit();

            $_SESSION['message'] = "Payment successful! Your booking has been confirmed.";
            header('Location: booking_history.php');
            exit();

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $message = "Database error during payment. Please try again.";
        }
    }
}

include 'Header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm p-4">
                <h3 class="text-center mb-4">Make Payment — <?= htmlspecialchars($booking['car_name']) ?></h3>

                <?php if ($message): ?>
                    <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?> text-center">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <p><strong>Dates:</strong> <?= htmlspecialchars($booking['start_date']) ?> to <?= htmlspecialchars($booking['end_date']) ?></p>
                        <p><strong>Total Amount:</strong> ₹<?= number_format($booking['total_amount'], 2) ?></p>
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

                    <div id="card_fields" style="display:none;">
                        <div class="mb-3">
                            <label>Card Number</label>
                            <input type="text" name="card_number" class="form-control" placeholder="XXXX-XXXX-XXXX-XXXX">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Expiry Date</label>
                                <input type="month" name="expiry" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>CVV</label>
                                <input type="password" name="cvv" class="form-control" maxlength="4">
                            </div>
                        </div>
                    </div>

                    <div id="upi_fields" style="display:none;">
                        <div class="mb-3">
                            <label>UPI ID</label>
                            <input type="text" name="upi_id" class="form-control" placeholder="example@upi">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="pay" class="btn btn-success btn-lg">Pay Now</button>
                        <a href="booking_history.php" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePaymentFields(){
    var m = document.getElementById('payment_method').value;
    document.getElementById('card_fields').style.display = (m === 'Card') ? 'block' : 'none';
    document.getElementById('upi_fields').style.display = (m === 'UPI') ? 'block' : 'none';
}
</script>

<?php include 'Footer.php'; ?>
