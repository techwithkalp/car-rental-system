<?php
session_start();
include '../config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = '';
$message_type = '';

if (isset($_POST['reset_password'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if new passwords match
    if ($new_password !== $confirm_password) {
        $message = "New Password and Confirm Password do not match.";
        $message_type = "danger";
    } else {
        // 2. Fetch user data by email
        $res = $conn->query("SELECT id, password FROM users WHERE email='$email'");
        
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $stored_hash = $row['password'];
            $user_id = $row['id'];

            // 3. Verify the old password
            if (password_verify($old_password, $stored_hash)) {
                
                // 4. Hash the new password securely
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // 5. Update the password in the database
                $update_sql = "UPDATE users SET password = '$new_hashed_password' WHERE id = '$user_id'";
                
                if ($conn->query($update_sql) === TRUE) {
                    $message = "Password updated successfully! You can now log in.";
                    $message_type = "success";
                } else {
                    $message = "Error updating password: " . $conn->error;
                    $message_type = "danger";
                }
            } else {
                $message = "The Old Password provided is incorrect.";
                $message_type = "danger";
            }
        } else {
            $message = "No user found with that email address.";
            $message_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-green: #2a9d8f; /* Vibrant Teal/Green */
            --background-color: #f0f4f8; /* Soft Light Gray */
            --card-bg-color: #ffffff; /* Pure White */
            --text-color: #212529; /* Dark Gray */
            --border-color: #ced4da;
            --focus-glow: rgba(42, 157, 143, 0.25);
            --border-radius: 12px;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height:100vh;
        }

        .reset-card {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header-icon {
            display: block;
            margin: 0 auto 1.5rem auto;
            font-size: 3rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px rgba(42, 157, 143, 0.3);
        }

        .card-title {
            color: var(--text-color);
            font-weight: 700;
        }

        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem var(--focus-glow);
        }

        .btn-primary {
            background-color: var(--primary-green);
            border: none;
            border-radius: 6px;
            font-weight: 600;
            padding: 12px 0;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #26897e;
            transform: translateY(-2px);
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 6px;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            border-radius: 6px;
        }
        
        a {
            color: var(--primary-green);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #26897e;
            text-decoration: underline;
        }
    
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card reset-card">
                <div class="card-body">
                    <div class="card-header-icon">
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <h3 class="card-title mb-4">Reset Your Password</h3>
                    <p class="text-muted mb-4 small">Verify your email and old password to set a new one.</p>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> text-center" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="old_password" class="form-control" placeholder="Current/Old Password" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
                        </div>
                        <button type="submit" name="reset_password" class="btn btn-primary w-100 mb-4">Update Password</button>
                    </form>

                    <p class="mt-3"><a href="index.php">Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>