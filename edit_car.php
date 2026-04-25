<?php
session_start();
include '../config.php';

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$car_result = $conn->query("SELECT * FROM cars WHERE id=$id");
if ($car_result->num_rows === 0) {
    die("Car not found.");
}
$car = $car_result->fetch_assoc();

$message = '';

if (isset($_POST['update_car'])) {
    $car_name = $_POST['car_name'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $fuel_type = $_POST['fuel_type'];
    $seating_capacity = $_POST['seating_capacity'];
    $rate_per_day = $_POST['rate_per_day'];
    $location = $_POST['location'];

    // Handle image upload if a new file is selected
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image = $_FILES['image']['name'];
        $target = "../assets/images/" . basename($image);

        // Validate image extension
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $message = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $conn->query("UPDATE cars SET car_name='$car_name', model='$model', type='$type', fuel_type='$fuel_type', seating_capacity='$seating_capacity', rate_per_day='$rate_per_day', location='$location', image='$image' WHERE id=$id");
                $message = "Car updated successfully!";
            } else {
                $message = "Failed to upload image.";
            }
        }
    } else {
        $conn->query("UPDATE cars SET car_name='$car_name', model='$model', type='$type', fuel_type='$fuel_type', seating_capacity='$seating_capacity', rate_per_day='$rate_per_day', location='$location' WHERE id=$id");
        $message = "Car updated successfully!";
    }

    // Refresh car data
    $car_result = $conn->query("SELECT * FROM cars WHERE id=$id");
    $car = $car_result->fetch_assoc();
}
?>

<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .edit-car-card {
        background-color: #ffffff;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }
    .form-control-icon {
        position: relative;
    }
    .form-control-icon .form-control {
        padding-left: 2.5rem;
    }
    .form-control-icon i {
        position: absolute;
        top: 50%;
        left: 0.75rem;
        transform: translateY(-50%);
        color: #adb5bd;
    }
    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
    }
    .car-image-preview {
        max-width: 200px;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 10px;
        margin-bottom: 20px;
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="edit-car-card">
                <h2 class="text-center mb-4">Edit Car</h2>
                <?php if ($message) { ?>
                    <div class="alert alert-info text-center"><?php echo $message; ?></div>
                <?php } ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-person-circle"></i>
                        <input type="text" name="car_name" class="form-control" placeholder="Car Name" value="<?= htmlspecialchars($car['car_name']) ?>" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-tag-fill"></i>
                        <input type="text" name="model" class="form-control" placeholder="Model" value="<?= htmlspecialchars($car['model']) ?>" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-speedometer"></i>
                        <input type="text" name="type" class="form-control" placeholder="Type (e.g., Sedan, SUV)" value="<?= htmlspecialchars($car['type']) ?>" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-fuel-pump-fill"></i>
                        <input type="text" name="fuel_type" class="form-control" placeholder="Fuel Type" value="<?= htmlspecialchars($car['fuel_type']) ?>" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-people-fill"></i>
                        <input type="number" name="seating_capacity" class="form-control" placeholder="Seating Capacity" value="<?= $car['seating_capacity'] ?>" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-currency-rupee"></i>
                        <input type="number" name="rate_per_day" class="form-control" placeholder="Rate/Day (₹)" value="<?= $car['rate_per_day'] ?>" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                        <input type="text" name="location" class="form-control" placeholder="Location" value="<?= htmlspecialchars($car['location']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">Update Car Image</label>
                        <input type="file" name="image" class="form-control">
                        <?php if ($car['image']) { ?>
                            <img src="../assets/images/<?= htmlspecialchars($car['image']) ?>" alt="Current Car Image" class="car-image-preview">
                        <?php } ?>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="update_car" class="btn btn-primary">Update Car</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
