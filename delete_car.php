<?php
session_start();
include '../config.php';

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get car ID
$car_id = null;
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $car_id = intval($_POST['id']);
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $car_id = intval($_GET['id']);
}

if ($car_id !== null) {
    // Check availability (only delete if Available)
    $check_car = $conn->prepare("SELECT availability FROM cars WHERE id = ?");
    $check_car->bind_param("i", $car_id);
    $check_car->execute();
    $result = $check_car->get_result();
    $car = $result->fetch_assoc();
    $check_car->close();

    if ($car && $car['availability'] !== 'Available') {
        $_SESSION['message'] = "Cannot delete the car. It is currently booked.";
        header("Location: manage_cars.php");
        exit();
    }

    // Delete car
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Car deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete the car. Please try again.";
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid car ID.";
}

header("Location: manage_cars.php");
exit();
?>
