<?php
session_start();
include '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $user_id = $_SESSION['user_id'];
    $booking_id = intval($_POST['booking_id']);

    // Optional: Verify that the booking belongs to the logged-in user and is pending payment
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND payment_status='Pending'");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update booking to remove notification or mark as seen
        // Option 1: Delete the booking completely (not recommended)
        // $del_stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
        // $del_stmt->bind_param("i", $booking_id);

        // Option 2: Mark notification as deleted (recommended)
        $del_stmt = $conn->prepare("UPDATE bookings SET payment_status='Cancelled' WHERE id=?");
        $del_stmt->bind_param("i", $booking_id);

        if ($del_stmt->execute()) {
            $_SESSION['message'] = "Notification removed successfully.";
        } else {
            $_SESSION['message'] = "Failed to remove notification. Try again.";
        }
        $del_stmt->close();
    } else {
        $_SESSION['message'] = "Invalid booking or already paid.";
    }

    $stmt->close();
}

// Redirect back to previous page
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "dashboard.php"));
exit();
