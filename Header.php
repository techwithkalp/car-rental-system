<?php
include '../config.php';

$user = null;
$notifications = [];

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    // Fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $profileImage = !empty($user['image']) && file_exists("../assets/images/" . $user['image'])
        ? "../assets/images/" . $user['image']
        : "https://placehold.co/50x50/34495e/ecf0f1?text=User";

    // Fetch notifications (latest first)
    $stmt2 = $conn->prepare("
        SELECT b.id as booking_id, c.car_name, b.start_date 
        FROM bookings b 
        JOIN cars c ON b.car_id = c.id 
        WHERE b.user_id = ? AND b.booking_status = 'Approved' AND b.payment_status='Pending' 
        ORDER BY b.created_at DESC
    ");
    $stmt2->bind_param("i", $uid);
    $stmt2->execute();
    $notifications = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();
}

// Current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Car Rental Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Updated CSS for better functionality and responsiveness 
*/
body {
    background-color: #f0f2f5;
}
.navbar-custom { 
    background:#2c3e50; 
    padding:1rem 2rem; 
    border-bottom:2px solid #007BFF; 
}
.navbar-brand { 
    font-weight:700; 
    font-size:1.8rem; 
    color:#007BFF; 
}
.nav-link { 
    color:#fff !important; 
    padding:0.6rem 1rem; 
    font-weight:500; 
    transition:all 0.3s ease; 
}
.nav-link:hover { 
    color:#17a2b8 !important; 
}
.nav-link.active { 
    border-bottom:3px solid #007BFF; 
    font-weight:600; 
}
.profile-image { 
    width:45px; 
    height:45px; 
    border-radius:50%; 
    object-fit:cover; 
    border:2px solid #007BFF; 
    transition: transform 0.3s ease; 
}
.profile-image:hover { 
    transform:scale(1.1); 
    border-color:#17a2b8; 
}
.dropdown-menu { 
    background:#1a2935; 
    border:1px solid #007BFF; 
    min-width:280px; /* Increased min-width for better content display */
    border-radius:10px; 
    padding: 0;
}
.dropdown-item { 
    color:#fff; 
    padding:0.75rem 1rem; 
    transition: background-color 0.2s ease; 
}
.dropdown-item:hover { 
    background-color:rgba(0,123,255,0.1); 
    color:#17a2b8; 
}
.notification { 
    font-size:1.4rem; 
    color:#fff; 
}
.notification-badge { 
    position:absolute; 
    top:-5px; 
    right:10px; 
    background:red; 
    color:#fff; 
    font-size:0.75rem; 
    padding:2px 6px; 
    border-radius:50%; 
    z-index: 10;
}
.dropdown-menu-notif {
    max-height: 350px; /* Increased max-height */
    overflow-y: auto;
}
.dropdown-header {
    color: #fff;
    padding: 1rem;
    font-weight: 600;
    background: #16213e;
    border-bottom: 1px solid #007BFF;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}
.notification-item {
    padding: 1rem;
    border-bottom: 1px solid #0f3460;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.notification-item:hover {
    background-color: rgba(0,123,255,0.1);
}
.notification-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.notification-details {
    flex-grow: 1;
}
.notification-actions {
    display: flex;
    gap: 0.5rem;
}
.delete-notif {
    color: #ff4d4f;
    background: none;
    border: none;
    font-size: 1.25rem;
    padding: 0;
    line-height: 1;
    transition: color 0.2s ease;
}
.delete-notif:hover {
    color: #cc0000;
}
/* Responsive adjustments */
@media (max-width: 991.98px) {
    .notification-badge {
        right: 15px;
    }
    .navbar-toggler {
        color: #fff;
        border-color: #fff;
    }
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Car Rental</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <?php if ($user): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'search_cars.php') ? 'active' : '' ?>" href="search_cars.php">Search Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'booking_history.php') ? 'active' : '' ?>" href="booking_history.php">Booking History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'feedback.php') ? 'active' : '' ?>" href="feedback.php">Feedback</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'privacy_policy.php') ? 'active' : '' ?>" href="privacy_policy.php">Privacy & Rules</a>
                </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill notification"></i>
                            <?php if(count($notifications) > 0): ?>
                                <span class="notification-badge"><?= count($notifications) ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-notif">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <?php if(count($notifications) > 0): ?>
                                <?php foreach($notifications as $n): ?>
                                    <li>
                                        <div class="dropdown-item notification-item">
                                            <div class="notification-details">
                                                <div class="fw-bold"><?= htmlspecialchars($n['car_name']) ?></div>
                                                <div class="text-secondary">Start Date: <?= htmlspecialchars($n['start_date']) ?></div>
                                            </div>
                                            <div class="notification-actions">
                                                <form method="GET" action="payment.php" class="d-inline-block">
                                                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($n['booking_id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Pay Now</button>
                                                </form>
                                                <form method="POST" action="delete_notification.php" class="d-inline-block">
                                                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($n['booking_id']) ?>">
                                                    <button type="submit" class="btn btn-sm delete-notif" title="Dismiss Notification"><i class="bi bi-x-circle"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">No new notifications.</span></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                 
<!-- User Profile Dropdown -->
<div class="dropdown ms-auto">
  <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
    <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="User" width="40" height="40" class="rounded-circle me-2">
    <strong><?php echo htmlspecialchars($user['username'] ?? 'User'); ?></strong>
  </a>
  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark text-small shadow" aria-labelledby="userDropdown">
    <li><a class="dropdown-item" href="profile.php">Manage Profile</a></li>
    <li><a class="dropdown-item" href="booking_history.php">My Bookings</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
  </ul>
</div>


                <?php else: ?>
                    <li class="nav-item"><a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($current_page == 'register.php') ? 'active' : '' ?>" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


<script>
document.addEventListener('DOMContentLoaded', function () {
    var profileImg = document.getElementById('profile-logout');
    if (profileImg) {
        profileImg.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = 'logout.php';
        });
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>