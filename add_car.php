<?php
session_start();
include '../config.php';

// Redirect to login if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';

if (isset($_POST['add_car'])) {
    $car_name = $_POST['car_name'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $fuel_type = $_POST['fuel_type'];
    $seating_capacity = $_POST['seating_capacity'];
    $rate_per_day = $_POST['rate_per_day'];
    $location = $_POST['location'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $target = "../assets/images/" . basename($image);

        // Optional: validate image type
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $message = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Insert car into database
                $conn->query("INSERT INTO cars (car_name, model, type, fuel_type, seating_capacity, rate_per_day, location, image) 
                             VALUES ('$car_name', '$model', '$type', '$fuel_type', '$seating_capacity', '$rate_per_day', '$location', '$image')");
                $message = "Car added successfully!";
            } else {
                $message = "Failed to upload image.";
            }
        }
    } else {
        $message = "Please select an image.";
    }
}
?>

<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .add-car-card {
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
    .btn-success {
        background-color: #1abc9c;
        border-color: #1abc9c;
        transition: background-color 0.3s ease;
    }
    .btn-success:hover {
        background-color: #16a085;
        border-color: #16a085;
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="add-car-card">
                <h2 class="text-center mb-4">Add Car</h2>
                <?php if ($message) { ?>
                    <div class="alert alert-info text-center"><?php echo $message; ?></div>
                <?php } ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-person-circle"></i>
                        <input type="text" name="car_name" class="form-control" placeholder="Car Name" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-tag-fill"></i>
                        <input type="text" name="model" class="form-control" placeholder="Model" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-speedometer"></i>
                        <input type="text" name="type" class="form-control" placeholder="Type (e.g., Sedan, SUV)" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-fuel-pump-fill"></i>
                        <input type="text" name="fuel_type" class="form-control" placeholder="Fuel Type" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-people-fill"></i>
                        <input type="number" name="seating_capacity" class="form-control" placeholder="Seating Capacity" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-currency-rupee"></i>
                        <input type="number" name="rate_per_day" class="form-control" placeholder="Rate/Day (₹)" required>
                    </div>
                    <div class="form-group mb-3 form-control-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                        <input type="text" name="location" class="form-control" placeholder="Location" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">Car Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="add_car" class="btn btn-success">Add Car</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
