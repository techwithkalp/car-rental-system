<?php
session_start();
include '../config.php';

$message = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile   = mysqli_real_escape_string($conn, $_POST['mobile']);
    $city     = mysqli_real_escape_string($conn, $_POST['city']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($res->num_rows > 0) {
        $message = "Email already exists.";
    } else {
        $insert = $conn->query("INSERT INTO users(username,email,mobile,city,password) 
                                 VALUES('$username','$email','$mobile','$city','$password')");
        if ($insert) {
            $message = "Registration Successful. You can now login.";
        } else {
            $message = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
            min-height: 100vh;
        }

        .register-card {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
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
            animation: bounceIn 1s ease-in-out;
        }
        
        @keyframes bounceIn {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
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

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            border-radius: 6px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
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
            <div class="card register-card">
                <div class="card-body">
                    <div class="card-header-icon text-center">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <h3 class="card-title text-center mb-4">Join Us Today!</h3>

                    <?php if ($message) {
                        $alertClass = (strpos($message, 'Successful') !== false) ? 'alert-success' : 'alert-danger';
                        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
                        echo htmlspecialchars($message);
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                        echo '</div>';
                    } ?>

                    <form method="POST" class="mt-3">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="mobile" class="form-control" placeholder="Mobile" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="city" class="form-control" placeholder="City" required>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                    </form>

                    <p class="text-center mt-3">Already have an account? <a href="index.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>