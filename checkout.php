<?php
require_once 'db.php';
require_once 'auth.php';
requireLogin();

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$grand_total = 0;
$cart_details = [];

// Fetch cart items for summary
$ids = array_keys($_SESSION['cart']);
$id_list = implode(',', array_map('intval', $ids));
$result = $conn->query("SELECT * FROM pizzas WHERE id IN ($id_list)");

while ($row = $result->fetch_assoc()) {
    $qty = $_SESSION['cart'][$row['id']];
    $grand_total += ($row['price'] * $qty);
    $row['qty'] = $qty;
    $cart_details[] = $row;
}

// Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cust_name = trim(htmlspecialchars($_POST['cust_name']));
    $cust_email = trim(htmlspecialchars($_POST['cust_email']));
    $phone = trim(htmlspecialchars($_POST['phone']));
    $address = trim(htmlspecialchars($_POST['address']));

    $conn->begin_transaction();
    try {
        // Insert order with delivery details
        $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_email, phone_number, delivery_address, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssd", $user_id, $cust_name, $cust_email, $phone, $address, $grand_total);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Insert items
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, pizza_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        foreach ($cart_details as $item) {
            $item_stmt->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
            $item_stmt->execute();
        }

        $conn->commit();
        unset($_SESSION['cart']);
        header("Location: order_success.php?id=" . $order_id);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Failed to place order. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Details | Pizza Delight</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">PIZZA <span>DELIGHT</span></h1>
        
        <div class="nav-links">
            <span class="secure-label">
                <i class="fas fa-lock"></i> Secure Checkout
            </span>
            
            <a href="cart.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Return to Cart
            </a>
        </div>
    </div>
</nav>

    <div class="auth-wrapper" style="align-items: flex-start; padding-top: 50px;">
        <div class="checkout-container" style="display: flex; gap: 30px; max-width: 1000px; width: 100%;">
            
            <div class="auth-card" style="flex: 2; text-align: left;">
                <h2 class="brand-title">Delivery <span>Summary</span></h2>
                <p class="auth-subtitle">Where should we send your pizza?</p>

                <form action="checkout.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="cust_name" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="cust_email"  placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="e.g. 012-3456789" required>
                    </div>
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="4" style="width:100%; border: 1.5px solid #ddd; border-radius:8px; padding:10px;" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Confirm & Place Order</button>
                </form>
            </div>

            <div class="auth-card" style="flex: 1; height: fit-content;">
                <h3>Your Order</h3>
                <hr style="margin: 15px 0;">
                <?php foreach ($cart_details as $item): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px;">
                        <span><?php echo $item['qty']; ?>x <?php echo $item['name']; ?></span>
                        <span>RM <?php echo number_format($item['price'] * $item['qty'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div style="display: flex; justify-content: space-between; font-weight: 800; color: var(--primary);">
                    <span>Total</span>
                    <span>RM <?php echo number_format($grand_total, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>