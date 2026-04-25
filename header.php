<?php

if(!isset($_SESSION['admin_id']) && basename($_SERVER['PHP_SELF']) != 'index.php' && basename($_SERVER['PHP_SELF']) != 'forgot_password_admin.php'){
    // Added forgot_password_admin.php to the list of allowed pages before authentication
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --navbar-bg: #2d3436; /* Dark gray */
            --primary-color: #55efc4; /* Mint green */
            --accent-color: #ffeaa7; /* Soft yellow */
            --text-color: #dfe6e9; /* Light gray */
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: var(--navbar-bg);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }
        .nav-link {
            color: var(--text-color) !important;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: var(--accent-color) !important;
        }
        .nav-link.active {
            color: var(--accent-color) !important;
            font-weight: bold;
            border-bottom: 2px solid var(--accent-color);
        }
        /* Make the main page container grow to push footer to the bottom when using footer's flex layout */
        .page-content {
            flex: 1 0 auto;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand text-nowrap" href="dashboard.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php 
                $current_page = basename($_SERVER['PHP_SELF']);
                if(isset($_SESSION['admin_id'])){ ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'manage_cars.php') ? 'active' : '' ?>" href="manage_cars.php"><i class="bi bi-car-front-fill me-1"></i> Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'manage_bookings.php') ? 'active' : '' ?>" href="manage_bookings.php"><i class="bi bi-calendar-check me-1"></i> Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'manage_users.php') ? 'active' : '' ?>" href="manage_users.php"><i class="bi bi-people-fill me-1"></i> Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'admin_feedback.php') ? 'active' : '' ?>" href="admin_feedback.php"><i class="bi bi-chat-dots-fill me-1"></i> Feedbacks</a>
                    </li>
                    <!-- NEW PROFILE LINK ADDED HERE -->
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'admin_profile.php') ? 'active' : '' ?>" href="admin_profile.php"><i class="bi bi-person-gear me-1"></i> Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4 page-content">
<!-- The remaining content will go here -->
