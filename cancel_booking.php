<?php
session_start();
include '../config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['id'] ?? 0); // Ensure ID is an integer and is present

if ($booking_id > 0) {
    // Check if the booking exists, belongs to the logged-in user, and is 'Pending'
    $stmt = $conn->prepare("SELECT car_id FROM bookings WHERE id = ? AND user_id = ? AND booking_status = 'Pending'");
    if ($stmt) {
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $booking = $result->fetch_assoc();
            $car_id = $booking['car_id'];

            // Start a transaction to ensure both updates succeed or fail together
            $conn->begin_transaction();

            try {
                // 1. Update booking status to 'Cancelled'
                $update_booking_stmt = $conn->prepare("UPDATE bookings SET booking_status='Cancelled', payment_status='Refunded' WHERE id=?");
                $update_booking_stmt->bind_param("i", $booking_id);
                $update_booking_stmt->execute();

                // 2. Update car availability status back to 'Available'
                $update_car_stmt = $conn->prepare("UPDATE cars SET availability='Available' WHERE id=?");
                $update_car_stmt->bind_param("i", $car_id);
                $update_car_stmt->execute();
                
                // Commit the transaction
                $conn->commit();
                
                // Set a success message for the next page
                $_SESSION['message'] = "Booking ID #{$booking_id} has been successfully cancelled.";

            } catch (mysqli_sql_exception $e) {
                // Rollback the transaction on failure
                $conn->rollback();
                $_SESSION['message'] = "An error occurred while cancelling the booking. Please try again.";
            }

            $update_booking_stmt->close();
            $update_car_stmt->close();

        } else {
            $_SESSION['message'] = "Booking not found, already cancelled, or you do not have permission to cancel it.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Database query preparation failed.";
    }
} else {
    $_SESSION['message'] = "Invalid booking ID.";
}

// Redirect back to booking history
header("Location: booking_history.php");
exit;
