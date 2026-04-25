<?php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get user ID from GET or POST
$user_id = null;

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $user_id = intval($_POST['id']);
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = intval($_GET['id']);
}

if ($user_id !== null) {
    // Optional: check if user exists before deleting
    $check = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result();
    $user = $result->fetch_assoc();
    $check->close();

    if (!$user) {
        $_SESSION['message'] = "User not found.";
        header("Location: manage_users.php");
        exit();
    }

    // Delete user's image if exists
    if (!empty($user['image']) && file_exists("../assets/images/" . $user['image'])) {
        unlink("../assets/images/" . $user['image']);
    }

    // Delete user from database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete user. Please try again.";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid user ID.";
}

// Redirect back to the manage users page
header("Location: manage_users.php");
exit();
