<?php
session_start();
include '../config.php';

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include 'header.php';

// Build the query dynamically
$sql = "SELECT b.*, u.username, c.car_name, c.model 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN cars c ON b.car_id = c.id
        WHERE 1=1";

$params = [];
$types = "";

if (isset($_GET['status']) && $_GET['status'] != 'All') {
    $sql .= " AND b.booking_status = ?";
    $params[] = &$_GET['status'];
    $types .= "s";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = '%' . $_GET['search'] . '%';
    $sql .= " AND (u.username LIKE ? OR c.car_name LIKE ?)";
    $params[] = &$search_query;
    $params[] = &$search_query;
    $types .= "ss";
}

$sql .= " ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $bookings = $stmt->get_result();
} else {
    $bookings = false;
    echo "<div class='alert alert-danger'>Database query preparation failed.</div>";
}
?>

<div class="container mt-5">
    <div class="card shadow rounded p-4">
        <h2 class="mb-4 text-primary">Manage Bookings</h2>

        <!-- Filter and Search Form -->
        <form method="GET" class="row g-3 mb-4 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by User or Car Name"
                       value="<?= htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="All" <?= ($_GET['status'] ?? 'All') == 'All' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="Pending" <?= ($_GET['status'] ?? '') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?= ($_GET['status'] ?? '') == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="Cancelled" <?= ($_GET['status'] ?? '') == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Car</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Booking Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings && $bookings->num_rows > 0): ?>
                        <?php while ($row = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['car_name'] . " (" . $row['model'] . ")"); ?></td>
                                <td><?= $row['start_date']; ?></td>
                                <td><?= $row['end_date']; ?></td>
                                <td>₹<?= $row['total_amount']; ?></td>
                                <td>
                                    <?php if ($row['payment_status'] == 'Paid'): ?>
                                        <span class="badge bg-success">Done</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Not Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        if ($row['booking_status'] == 'Pending') {
                                            $status_class = "bg-warning text-dark";
                                        } elseif ($row['booking_status'] == 'Approved') {
                                            $status_class = "bg-success";
                                        } elseif ($row['booking_status'] == 'Cancelled') {
                                            $status_class = "bg-danger";
                                        } else {
                                            $status_class = "bg-secondary";
                                        }
                                    ?>
                                    <span class="badge <?= $status_class ?>"><?= $row['booking_status']; ?></span>
                                </td>
                                <td>
                                    <?php if ($row['payment_status'] == 'Paid'): ?>
                                        <span class="text-success fw-bold">Done</span>
                                    <?php elseif ($row['booking_status'] == 'Pending'): ?>
                                        <a href="update_booking_status.php?id=<?= $row['id']; ?>&status=Approved"
                                           class="btn btn-success btn-sm">Approve</a>
                                        <a href="update_booking_status.php?id=<?= $row['id']; ?>&status=Cancelled"
                                           class="btn btn-danger btn-sm">Cancel</a>
                                    <?php else: ?>
                                        <span class="text-muted">No Action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
