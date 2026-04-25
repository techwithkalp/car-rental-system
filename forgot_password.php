<?php
session_start();
include '../config.php'; // Include database connection

$message = '';
$message_type = '';

if (isset($_POST['reset_password'])) {
    $email          = trim($_POST['email']);
    $old_password   = $_POST['old_password']; // Required for your specific security check
    $new_password   = $_POST['new_password'];
    $retype_password = $_POST['retype_password'];
    $secret_code    = $_POST['secret_code'];

    // 1. Basic validation (New passwords match)
    if ($new_password !== $retype_password) {
        $message = "New passwords do not match.";
        $message_type = 'danger';
    } 
    
    // 2. Complex Password validation (optional but recommended)
    else if (strlen($new_password) < 8) {
        $message = "New password must be at least 8 characters long.";
        $message_type = 'danger';
    }

    // 3. Check Admin Secret Code
    if ($message_type === '') {
        $code_query = $conn->query("SELECT secret_code FROM admin_secret LIMIT 1");
        $row = $code_query->fetch_assoc();
        $valid_code = $row['secret_code'] ?? '';

        if ($secret_code !== $valid_code || empty($valid_code)) {
            $message = "Invalid Admin Secret Code.";
            $message_type = 'danger';
        }
    }

    // 4. Validate Email and Old Password
    if ($message_type === '') {
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $message = "Email not found in admin records.";
            $message_type = 'danger';
        } else {
            $admin = $result->fetch_assoc();
            $stmt->close();

            // Verify the 'Old Password' (your custom requirement for password change)
            if (!password_verify($old_password, $admin['password'])) {
                $message = "The 'Old Password' you entered is incorrect.";
                $message_type = 'danger';
            } else {
                // All checks passed. Proceed to update password.
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                $update_stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_password_hash, $admin['id']);

                if ($update_stmt->execute()) {
                    $message = "Password successfully updated! You can now log in with your new password.";
                    $message_type = 'success';
                    // Optional: redirect to login page after success
                    // header("Location: index.php?reset_success=1");
                    // exit();
                } else {
                    $message = "Failed to update password. Database error.";
                    $message_type = 'danger';
                }
                $update_stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f4f7f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            background-color: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            padding: 3.5rem;
            transition: transform 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-5px);
        }
        .form-control-icon {
            position: relative;
        }
        .form-control-icon .form-control {
            padding-left: 3rem;
            border-radius: 12px;
            border: 1px solid #dee2e6;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control-icon .form-control:focus {
            border-color: #5aa1f0;
            box-shadow: 0 0 0 0.25rem rgba(90, 161, 240, 0.25);
        }
        .form-control-icon i {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 1.2rem;
        }
        .btn-primary {
            background: linear-gradient(45deg, #4a90e2, #5aa1f0);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 144, 226, 0.4);
        }
        .alert {
            border-radius: 10px;
        }
        .link-text {
            color: #4a90e2;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .link-text:hover {
            color: #3484d3;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="login-card">
                <h3 class="text-center mb-5">Admin Password Reset</h3>

                <?php if ($message): ?>
                    <div class="alert alert-<?= $message_type ?> text-center"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group mb-4 form-control-icon">
                        <i class="bi bi-envelope-fill"></i>
                        <input type="email" name="email" class="form-control" placeholder="Admin Email ID" required>
                    </div>
                    
                    <div class="form-group mb-4 form-control-icon">
                        <i class="bi bi-key-fill"></i>
                        <input type="password" name="old_password" class="form-control" placeholder="Current/Old Password (Required)" required>
                        <!-- <small class="text-muted">This is an extra security layer.</small> -->
                    </div>
                    
                    

                    <div class="form-group mb-4 form-control-icon">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" name="new_password" class="form-control" placeholder="New Password (min 8 chars)" required>
                    </div>
                    
                    <div class="form-group mb-4 form-control-icon">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" name="retype_password" class="form-control" placeholder="Re-type New Password" required>
                    </div>
                    
                    <div class="form-group mb-4 form-control-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                        <input type="text" name="secret_code" class="form-control" placeholder="Admin Secret Code" required>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" name="reset_password" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>

                <p class="mt-4 text-center">Remembered your login? <a href="index.php" class="link-text">Go to Login</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>