<?php
session_start();
include '../config.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Auto-return cars if booking end date has passed
$today = date('Y-m-d');
$conn->query("
    UPDATE bookings b
    JOIN cars c ON b.car_id = c.id
    SET b.booking_status='Completed', c.availability='Available'
    WHERE b.booking_status='Confirmed' AND b.end_date < '$today'
");

// Get and clear any session message for feedback
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Build search query
$sql = "SELECT * FROM cars WHERE availability='Available'";
$params = [];
$types = "";

// Apply search filters
if (isset($_POST['search'])) {
    $model = $_POST['model'] ?? '';
    $location = $_POST['location'] ?? '';

    if (!empty($model)) {
        $sql .= " AND model LIKE ?";
        $params[] = '%' . $model . '%';
        $types .= "s";
    }

    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = '%' . $location . '%';
        $types .= "s";
    }
}

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $cars = $stmt->get_result();
} else {
    $cars = false;
    echo "<div class='alert alert-danger'>Database query failed.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search Cars</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card-img-top { height: 200px; object-fit: cover; }
.availability-badge { position: absolute; top: 10px; right: 10px; font-size: 0.9rem; }
</style>
</head>
<body class="bg-light">

<?php include 'Header.php'; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Find Your Perfect Ride</h2>

    <?php if ($message): ?>
        <div class="alert alert-success text-center alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="card p-4 mb-5 shadow-sm">
        <form method="POST">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="model" class="form-label">Car Model</label>
                    <input type="text" name="model" id="model" class="form-control" placeholder="e.g., 2024" value="<?= htmlspecialchars($_POST['model'] ?? '') ?>">
                </div>
                <div class="col-md-5">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" placeholder="e.g., Ahmedabad" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Car Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if ($cars && $cars->num_rows > 0): ?>
            <?php while ($row = $cars->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        <?php
                        $imagePath = "../assets/images/" . $row['image'];
                        if (!file_exists($imagePath) || empty($row['image'])) {
                            $imagePath = "https://placehold.co/600x400/E5E7EB/9CA3AF?text=No+Image";
                        }
                        ?>
                        <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($row['car_name']) ?>">
                        <span class="badge rounded-pill bg-success availability-badge">
                            <?= htmlspecialchars($row['availability']) ?>
                        </span>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($row['car_name']); ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($row['model']); ?> | <?= htmlspecialchars($row['type']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <h4 class="text-success mb-0">₹<?= number_format($row['rate_per_day'], 2); ?> <small class="text-muted fw-normal">/ day</small></h4>
                                <a href="book_car.php?car_id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    No available cars found matching your search.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'Footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
