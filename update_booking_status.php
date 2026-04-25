<?php
session_start();
include '../config.php';

// Only admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    $_SESSION['error'] = "Invalid request.";
    header('Location: manage_bookings.php');
    exit();
}

$booking_id = intval($_GET['id']);
$new_status = $_GET['status'];

$allowed = ['Pending', 'Approved', 'Cancelled'];
if (!in_array($new_status, $allowed)) {
    $_SESSION['error'] = "Invalid status.";
    header('Location: manage_bookings.php');
    exit();
}

if ($new_status === 'Approved') {
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = ?, notification_flag = 1 WHERE id = ?");
    $stmt->bind_param("si", $new_status, $booking_id);
    $ok = $stmt->execute();
    $stmt->close();
    if ($ok) $_SESSION['success'] = "Booking approved and user notified.";
    else $_SESSION['error'] = "Error updating booking.";
} else {
    // Cancel or revert
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = ?, notification_flag = 0 WHERE id = ?");
    $stmt->bind_param("si", $new_status, $booking_id);
    $ok = $stmt->execute();
    $stmt->close();
    if ($ok) $_SESSION['success'] = "Booking status updated.";
    else $_SESSION['error'] = "Error updating booking.";
}

header('Location: manage_bookings.php');
exit();
