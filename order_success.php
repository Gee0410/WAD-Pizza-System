<?php
require_once 'auth.php';
requireLogin();
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success | Pizza Delight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: #2e7d32; margin-bottom: 20px;"></i>
            <h2 class="brand-title">SUCCESS!</h2>
            <p class="auth-subtitle">Your order <strong>#<?php echo $order_id; ?></strong> has been placed.</p>
            <p>Our chefs are already tossing the dough. Get ready for perfection!</p>
            <br>
            <a href="index.php" class="btn-primary" style="text-decoration: none; display: inline-block;">Order More Pizza</a>
        </div>
    </div>
</body>
</html>