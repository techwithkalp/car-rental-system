<?php
session_start();
include '../config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$car_id = intval($_GET['id'] ?? 0); // Ensure ID is an integer

if ($car_id > 0) {
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $stmt->close();
} else {
    $car = false;
}

// Redirect if car ID is invalid or car not found
if (!$car) {
    $_SESSION['message'] = "Car not found or an invalid ID was provided.";
    header('Location: search_cars.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- START: Navigation Bar (Content of Header.php) -->
<?php include 'Header.php'; ?>
<!-- END: Navigation Bar -->

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <img src="https://placehold.co/600x400/E5E7EB/9CA3AF?text=Car+Image" class="img-fluid rounded" alt="Car Image Placeholder">
                        </div>
                        <div class="col-md-6">
                            <h3 class="card-title fw-bold text-primary"><?= htmlspecialchars($car['car_name']); ?></h3>
                            <p class="text-muted"><?= htmlspecialchars($car['model']); ?></p>
                            
                            <hr>
                            
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-person-fill"></i> Seating: <?= htmlspecialchars($car['seating_capacity']); ?>
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-fuel-pump-fill"></i> Fuel: <?= htmlspecialchars($car['fuel_type']); ?>
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-geo-alt-fill"></i> Location: <?= htmlspecialchars($car['location']); ?>
                                </span>
                            </div>
                            
                            <h4 class="fw-bold text-success">
                                ₹<?= number_format($car['rate_per_day'], 2); ?>
                                <small class="text-muted fw-normal">/ day</small>
                            </h4>
                            
                            <p class="mt-3">
                                **Description:**
                                <br>
                                This is a brief description of the car, its features, and any additional information a customer might need before booking.
                            </p>
                            
                            <hr>

                            <div class="d-grid gap-2">
                                <a href="book_car.php?car_id=<?= htmlspecialchars($car['id']) ?>" class="btn btn-primary btn-lg mt-3">Book This Car Now</a>
                                <a href="search_cars.php" class="btn btn-secondary mt-2">Go Back to Search</a>
                            </div>
                        </div>
                    </div>
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