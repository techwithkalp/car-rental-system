<?php
session_start();
include '../config.php';

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Validate and sanitize inputs
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Ensure the status is one of the allowed values
if ($id > 0 && in_array($status, ['Approved', 'Cancelled', 'Pending'])) {
    
    // Start a transaction for data integrity
    $conn->begin_transaction();
    
    try {
        // Prepare the statement to update the booking status
        $update_booking_stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
        if ($update_booking_stmt) {
            $update_booking_stmt->bind_param("si", $status, $id);
            $update_booking_stmt->execute();
            $update_booking_stmt->close();
        } else {
            throw new Exception("Failed to prepare update booking statement.");
        }

        // If the booking is cancelled, also update the car's availability
        if ($status == 'Cancelled') {
            // Get the car_id from the booking
            $car_id = null;
            $get_car_id_stmt = $conn->prepare("SELECT car_id FROM bookings WHERE id = ?");
            if ($get_car_id_stmt) {
                $get_car_id_stmt->bind_param("i", $id);
                $get_car_id_stmt->execute();
                $result = $get_car_id_stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $car_id = $row['car_id'];
                }
                $get_car_id_stmt->close();
            } else {
                throw new Exception("Failed to prepare get car ID statement.");
            }

            // Update the car's availability if a car_id was found
            if ($car_id) {
                $update_car_stmt = $conn->prepare("UPDATE cars SET availability='Available' WHERE id=?");
                if ($update_car_stmt) {
                    $update_car_stmt->bind_param("i", $car_id);
                    $update_car_stmt->execute();
                    $update_car_stmt->close();
                } else {
                    throw new Exception("Failed to prepare update car statement.");
                }
            }
        }
        
        // Commit the transaction if all queries were successful
        $conn->commit();
    
    } catch (Exception $e) {
        // Rollback on any failure
        $conn->rollback();
        // Optionally log or display the error
        // echo "Error: " . $e->getMessage();
    }
}

// Redirect back to manage bookings, regardless of outcome
header("Location: manage_bookings.php");
exit();
?>
