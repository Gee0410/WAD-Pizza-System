<?php
require_once 'db.php';
require_once 'auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $errors[] = "All fields are required.";
    } elseif (strlen($new_pass) < 8) {
        $errors[] = "New password must be at least 8 characters.";
    } elseif ($new_pass !== $confirm_pass) {
        $errors[] = "New passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($current_pass, $user['password'])) {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_pass, $user_id);
            
            if ($update_stmt->execute()) {
                $success = "Security updated successfully!";
            } else {
                $errors[] = "Update failed. Please try again.";
            }
        } else {
            $errors[] = "Current password is incorrect.";
        }
    }
}

$user_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings | Pizza Delight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">PIZZA <span>DELIGHT</span></h1>
            <div class="nav-links">
                <a href="index.php">Menu</a>
                <a href="order_history.php">History</a>
                <a href="profile.php" class="active">Profile</a>
                <a href="logout.php" class="btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 500px;">
            <div class="profile-header" style="margin-bottom: 30px;">
                <h2 class="brand-title">Profile <span>Settings</span></h2>
                <p>Manage your account security</p>
            </div>

            <div class="user-info-static" style="text-align: left; background: var(--bg-warm); padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user_info['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
            </div>

            <?php if ($errors): ?>
                <div class="alert alert-danger"><?php foreach ($errors as $e) echo "<p>$e</p>"; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="current_password" id="cur_pass" placeholder="Enter current password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('cur_pass', this)"></i>
                    </div>
                </div>
                
                <hr style="margin: 25px 0; border: none; border-top: 1px solid #eee;">
                
                <div class="form-group">
                    <label>New Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="new_password" id="new_pass" placeholder="Minimum 8 characters" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('new_pass', this)"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" id="conf_pass" placeholder="Repeat new password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('conf_pass', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Update Password</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye", "fa-eye-slash");

            }
        }
    </script>
</body>
</html>