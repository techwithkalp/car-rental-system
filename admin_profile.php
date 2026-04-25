<?php
session_start();
include '../config.php';
// Include the updated Header.php
include 'Header.php'; 

// Check for admin session
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$profile_message = '';
$code_message = '';
$profile_message_type = '';
$code_message_type = '';

// --- 1. Fetch Current Admin Data ---
$stmt = $conn->prepare("SELECT username, email FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();
$admin_data = $admin_result->fetch_assoc();
$stmt->close();

// --- 2. Handle Profile Update (Username & Email) ---
if (isset($_POST['update_profile'])) {
    $new_username = trim($_POST['new_username']);
    $new_email = trim($_POST['new_email']);

    if (empty($new_username) || empty($new_email)) {
        $profile_message = "Username and Email cannot be empty.";
        $profile_message_type = 'danger';
    } else {
        // Prepare statement to update admin details
        $update_stmt = $conn->prepare("UPDATE admins SET username = ?, email = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $new_username, $new_email, $admin_id);

        if ($update_stmt->execute()) {
            // Update session variables immediately
            $_SESSION['username'] = $new_username;
            
            // Re-fetch data to update the form fields
            $admin_data['username'] = $new_username;
            $admin_data['email'] = $new_email;
            
            $profile_message = "Profile updated successfully!";
            $profile_message_type = 'success';
        } else {
            $profile_message = "Error updating profile: " . $conn->error;
            $profile_message_type = 'danger';
        }
        $update_stmt->close();
    }
}

// --- 3. Handle Secret Code Change ---
if (isset($_POST['change_secret_code'])) {
    $old_secret = $_POST['old_secret_code'];
    $new_secret = $_POST['new_secret_code'];
    $retype_secret = $_POST['retype_secret_code'];

    // Input Validation
    if (empty($old_secret) || empty($new_secret) || empty($retype_secret)) {
        $code_message = "All secret code fields are required.";
        $code_message_type = 'danger';
    } elseif ($new_secret !== $retype_secret) {
        $code_message = "New secret codes do not match.";
        $code_message_type = 'danger';
    } else {
        // a. Fetch current secret code from database
        $code_query = $conn->query("SELECT secret_code FROM admin_secret LIMIT 1");
        $row = $code_query->fetch_assoc();
        $current_code = $row['secret_code'] ?? '';

        // b. Check if old secret matches the current secret
        if ($old_secret !== $current_code) {
            $code_message = "Invalid Old Secret Code provided.";
            $code_message_type = 'danger';
        } else {
            // c. Update the secret code
            // Note: We assume the admin_secret table always has one row (ID 1) or we use INSERT OR REPLACE.
            // Using REPLACE INTO for simplicity to ensure one code exists.
            $update_code_stmt = $conn->prepare("REPLACE INTO admin_secret (id, secret_code) VALUES (1, ?)");
            $update_code_stmt->bind_param("s", $new_secret);

            if ($update_code_stmt->execute()) {
                $code_message = "Admin Secret Code successfully updated!";
                $code_message_type = 'success';
            } else {
                $code_message = "Error updating secret code: " . $conn->error;
                $code_message_type = 'danger';
            }
            $update_code_stmt->close();
        }
    }
}
?>
<style>
    /* Custom style for clickable password icons */
    .input-group-text.cursor-pointer {
        cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-12">
        <h2 class="mb-4 text-center">Admin Profile & Security Settings</h2>
    </div>
</div>

<div class="row g-4">
    
    <!-- Profile Update Card -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> Update Personal Profile</h5>
            </div>
            <div class="card-body">
                <?php if ($profile_message): ?>
                    <div class="alert alert-<?= $profile_message_type ?>"><?= htmlspecialchars($profile_message) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="new_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="new_username" name="new_username" value="<?= htmlspecialchars($admin_data['username'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="new_email" name="new_email" value="<?= htmlspecialchars($admin_data['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="update_profile" class="btn btn-success mt-3"><i class="bi bi-check-circle-fill me-2"></i> Save Profile Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Secret Code Change Card -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-shield-lock-fill me-2"></i> Change Admin Secret Code</h5>
            </div>
            <div class="card-body">
                <?php if ($code_message): ?>
                    <div class="alert alert-<?= $code_message_type ?>"><?= htmlspecialchars($code_message) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Old Secret Code with Toggle -->
                    <div class="mb-3">
                        <label for="old_secret_code" class="form-label">Old Secret Code</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="old_secret_code" name="old_secret_code" required>
                            <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('old_secret_code')">
                                <i class="bi bi-eye-slash-fill"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- New Secret Code with Toggle -->
                    <div class="mb-3">
                        <label for="new_secret_code" class="form-label">New Secret Code</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_secret_code" name="new_secret_code" required>
                            <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('new_secret_code')">
                                <i class="bi bi-eye-slash-fill"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Re-type New Secret Code with Toggle -->
                    <div class="mb-3">
                        <label for="retype_secret_code" class="form-label">Re-type New Secret Code</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="retype_secret_code" name="retype_secret_code" required>
                            <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('retype_secret_code')">
                                <i class="bi bi-eye-slash-fill"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="change_secret_code" class="btn btn-danger mt-3"><i class="bi bi-key me-2"></i> Update Secret Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer include should be outside the page-content div -->
</div> 
<?php include 'Footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript for Password Toggle -->
<script>
    /**
     * Toggles the visibility of a password input field and changes the eye icon.
     * @param {string} inputId - The ID of the input field to toggle (e.g., 'old_secret_code').
     */
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        // Find the <i> tag which is nested inside the span.input-group-text right after the input
        const icon = document.querySelector(`#${inputId} + .input-group-text i`);
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye-slash-fill");
            icon.classList.add("bi-eye-fill"); // Open eye
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-fill");
            icon.classList.add("bi-eye-slash-fill"); // Closed eye
        }
    }
</script>
</body>
</html>
