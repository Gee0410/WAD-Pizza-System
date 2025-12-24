<?php
session_start();
require_once 'db.php';

// Generate CSRF token for security if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $user = trim(htmlspecialchars($_POST['username']));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. Validation Logic
    if (empty($user) || empty($email) || empty($pass) || empty($confirm_pass)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } elseif (strlen($pass) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } elseif ($pass !== $confirm_pass) {
        $errors[] = "Passwords do not match.";
    }

    // 2. Check if email exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) { 
            $errors[] = "This email is already registered.";
        }
        $stmt->close();
    }

    // 3. Insert User into Database
    if (empty($errors)) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        // Correct Order: username, email, password
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $email, $hashed_pass);

        if ($stmt->execute()) {
            $success = "Registration successful! Redirecting to login...";
            header("refresh:2;url=login.php"); 
        } else {
            $errors[] = "System error. Please try again later.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join the Family | Pizza Delight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2 class="brand-title">PIZZA <span>DELIGHT</span></h2>
            <p class="auth-subtitle">Create an account to start ordering</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) echo "<p><i class='fas fa-exclamation-circle'></i> $error</p>"; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo isset($user) ? $user : ''; ?>" placeholder="e.g. PizzaLover99" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" placeholder="email@example.com" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" placeholder="Min. 8 characters" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Register Now</button>
            </form>

            <p class="auth-footer">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <script>
    /**
     * Toggles visibility of password fields
     */
    function togglePassword(fieldId, icon) {
        const passwordField = document.getElementById(fieldId);
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            passwordField.type = "password";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        }
    }
    </script>
</body>
</html>