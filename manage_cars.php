<?php
session_start();
include '../config.php';

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Handle Return Car
if (isset($_GET['return_car_id'])) {
    $return_car_id = intval($_GET['return_car_id']);
    // Get latest active booking
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE car_id=? AND booking_status != 'Completed' ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $return_car_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($booking) {
        $update_booking = $conn->prepare("UPDATE bookings SET booking_status='Completed' WHERE id=?");
        $update_booking->bind_param("i", $booking['id']);
        $update_booking->execute();
        $update_booking->close();

        $update_car = $conn->prepare("UPDATE cars SET availability='Available' WHERE id=?");
        $update_car->bind_param("i", $return_car_id);
        $update_car->execute();
        $update_car->close();

        $_SESSION['message'] = "Car returned successfully and is now available.";
    } else {
        $_SESSION['message'] = "No active booking found for this car.";
    }

    header("Location: manage_cars.php");
    exit();
}

include 'Header.php';

// Build query to fetch cars
$sql = "SELECT * FROM cars WHERE 1=1";
$params = [];
$types = "";

if (isset($_GET['availability']) && $_GET['availability'] != 'All') {
    $sql .= " AND availability = ?";
    $params[] = &$_GET['availability'];
    $types .= "s";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = '%' . $_GET['search'] . '%';
    $sql .= " AND (car_name LIKE ? OR model LIKE ?)";
    $params[] = &$search_query;
    $params[] = &$search_query;
    $types .= "ss";
}

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $cars = $stmt->get_result();
} else {
    $cars = false;
    echo "<div class='alert alert-danger'>Failed to prepare the database query.</div>";
}

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cars</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f9; }
        .main-card { background-color: #fff; border:none; border-radius:20px; padding:3.5rem; margin-top:2rem; box-shadow:0 15px 40px rgba(0,0,0,0.1);}
        h2 { color:#34495e; font-weight:600; border-bottom:2px solid #e9ecef; padding-bottom:1rem; margin-bottom:2rem; }
        .table-responsive { border-radius: 15px; box-shadow:0 5px 15px rgba(0,0,0,0.05); overflow-x:auto; }
        .table thead th { background-color:#34495e; color:#fff; border:none; font-weight:600; }
        .table tbody tr:hover { background-color: #f8f9fa; }
        .btn-action { border-radius:8px; font-weight:500; transition: transform 0.2s ease, box-shadow 0.2s ease; margin-right:5px; }
        .btn-success { background: linear-gradient(45deg,#2ecc71,#27ae60); border:none; }
        .btn-primary { background: linear-gradient(45deg,#4a90e2,#5aa1f0); border:none; }
        .btn-danger { background: linear-gradient(45deg,#e74c3c,#c0392b); border:none; }
        .btn-action:hover { transform: translateY(-2px); box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        .status-badge { padding:5px 10px; border-radius:5px; font-weight:bold; }
        .status-badge.Available { background-color:#2ecc71;color:#1a6d3c; }
        .status-badge.Booked { background-color:#e74c3c;color:#8c2a20; }
    </style>
</head>
<body>
<div class="container">
    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Cars</h2>
            <a href="add_car.php" class="btn btn-success btn-action">Add Car</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by car name or model" 
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="col-md-3 ms-auto">
                <form method="GET">
                    <select name="availability" class="form-select" onchange="this.form.submit()">
                        <option value="All" <?= ($_GET['availability'] ?? 'All') == 'All' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="Available" <?= ($_GET['availability'] ?? '') == 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Booked" <?= ($_GET['availability'] ?? '') == 'Booked' ? 'selected' : '' ?>>Booked</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Model</th><th>Type</th><th>Rate/Day</th><th>Availability</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($cars && $cars->num_rows > 0): ?>
                        <?php while ($row = $cars->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['car_name']) ?></td>
                                <td><?= htmlspecialchars($row['model']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td>₹<?= $row['rate_per_day'] ?></td>
                                <td><span class="status-badge <?= $row['availability'] ?>"><?= $row['availability'] ?></span></td>
                                <td>
                                    <a href="edit_car.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm btn-action">Edit</a>
                                    <button class="btn btn-danger btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteModal" data-car-id="<?= $row['id'] ?>">Delete</button>
                                    <?php if ($row['availability'] == 'Booked'): ?>
                                        <a href="manage_cars.php?return_car_id=<?= $row['id'] ?>" class="btn btn-success btn-sm btn-action" onclick="return confirm('Mark this car as returned?');">Return</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No cars found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this car? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const carId = button.getAttribute('data-car-id');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        confirmDeleteBtn.href = `delete_car.php?id=${carId}`;
    });
}
</script>

</body>
</html>

<?php include 'Footer.php'; ?>
