<?php
session_start();
// 1. Include the database configuration first
include '../config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare and execute a parameterized query to prevent SQL injection
$stmt = $conn->prepare("SELECT b.*, c.car_name, c.model, c.type 
                         FROM bookings b 
                         JOIN cars c ON b.car_id = c.id 
                         WHERE b.user_id = ? 
                         ORDER BY b.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close(); // Close the statement after getting results

// Get and clear any session message for user feedback
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// 2. Include the Header file AFTER all necessary PHP variables and logic are set
include 'Header.php'; 
?>

<div class="container mt-5 pt-5">
    <h2 class="mb-4">Your Booking History</h2>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($bookings->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Car Name</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Amount (₹)</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $bookings->fetch_assoc()): ?>
                        <?php
                            $payment_badge = '';
                            if ($row['payment_status'] === 'Paid') {
                                $payment_badge = '<span class="badge bg-success">Paid</span>';
                            } else {
                                $payment_badge = '<span class="badge bg-warning text-dark">Unpaid</span>';
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['car_name']) ?></td>
                            <td><?= htmlspecialchars($row['model']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= htmlspecialchars($row['start_date']) ?></td>
                            <td><?= htmlspecialchars($row['end_date']) ?></td>
                            <td>₹<?= number_format($row['total_amount'], 2) ?></td>
                            <td><?= $payment_badge ?></td>
                            <td>
                                <a href="invoice.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-info btn-sm">Invoice</a>
                                <a href="cancel_booking.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">Cancel</a>
                                <?php if ($row['payment_status'] === 'Unpaid'): ?>
                                    <a href="payment.php?booking_id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-success btn-sm mt-1">Pay Now</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">
            You don't have any bookings yet. <a href="search_cars.php" class="alert-link">Start your search now!</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'Footer.php'; ?>