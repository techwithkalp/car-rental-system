<?php
session_start();
include '../config.php';

// માત્ર admin માટે
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// આજની તારીખ
$today = date('Y-m-d');

// Auto-return cars: booking પૂર્ણ થયા પછી
$update_stmt = $conn->prepare("
    UPDATE cars c
    JOIN bookings b ON c.id = b.car_id
    SET c.availability = 'Available', b.booking_status = 'Completed'
    WHERE b.booking_status = 'Confirmed' AND b.end_date < ?
");
$update_stmt->bind_param("s", $today);
$update_stmt->execute();
$update_stmt->close();

// Success message session માં
$_SESSION['message'] = "Auto-return check completed. Expired bookings updated.";

// Redirect back to manage cars page
header("Location: manage_cars.php");
exit();
?>
