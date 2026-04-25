<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Fetch user function
function fetchUser($conn, $uid) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $mobile   = $_POST['mobile'];
    $city     = $_POST['city'];

    $oldUser = fetchUser($conn, $uid);
    $image = $oldUser['image'];

    try {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file_info = getimagesize($_FILES['image']['tmp_name']);
            if ($file_info === false) {
                throw new Exception("Uploaded file is not an image.");
            }

            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $extension;
            $target = "../assets/images/" . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                if (!empty($oldUser['image']) && file_exists("../assets/images/" . $oldUser['image'])) {
                    unlink("../assets/images/" . $oldUser['image']);
                }
                $image = $image_name;
            } else {
                throw new Exception("Image upload failed.");
            }
        }

        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, mobile=?, city=?, image=? WHERE id=?");
        $stmt->bind_param("sssssi", $username, $email, $mobile, $city, $image, $uid);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Profile updated successfully!";
        $_SESSION['messageType'] = "success";
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['messageType'] = "danger";
    }

    header("Location: profile.php");
    exit;
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}

$user = fetchUser($conn, $uid);
$profileImage = !empty($user['image']) && file_exists("../assets/images/" . $user['image'])
    ? "../assets/images/" . $user['image']
    : "https://placehold.co/150x150/CCCCCC/000000?text=User";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ddd;
        }
    </style>
</head>
<body class="bg-light">

    <?php include 'Header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Update Profile</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($messageType) ?> text-center">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card p-4 shadow-sm mx-auto" style="max-width: 500px;">
            <form method="POST" enctype="multipart/form-data">
                <div class="text-center mb-3">
                    <img src="<?= htmlspecialchars($profileImage) ?>" class="profile-img" alt="Profile Image">
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($user['mobile']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>City</label>
                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Profile Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="d-grid">
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
