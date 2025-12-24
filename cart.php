<?php
require_once 'db.php';
require_once 'auth.php';

// Ensure the user is logged in
requireLogin();

$cart_items = [];
$grand_total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $id_list = implode(',', array_map('intval', $ids));

    // Fetch fresh data from DB to prevent price tampering
    $query = "SELECT * FROM pizzas WHERE id IN ($id_list)";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $qty = $_SESSION['cart'][$id];
        $subtotal = $row['price'] * $qty;
        $grand_total += $subtotal;

        $row['quantity'] = $qty;
        $row['subtotal'] = $subtotal;
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | Pizza Delight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">PIZZA <span>DELIGHT</span></h1>
        <div class="nav-links">
            <a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Menu</a>
            
            <a href="order_history.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'order_history.php') ? 'active' : ''; ?>">History</a>
            
            <a href="cart.php" class="cart-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cart.php') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> 
                Cart <span id="cart-count"><?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></span>
            </a>
            <a href="profile.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                  <i class="fas fa-user-circle"></i> Profile
                </a>
            <span class="user-greet">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="btn-outline">Logout</a>
        </div>
    </div>
</nav>

    <main class="container">
        <div class="cart-header">
            <h2>Your Selection</h2>
            <p>Review your gourmet choices before checkout</p>
        </div>

        <?php if (empty($cart_items)): ?>
    <div class="empty-cart-container">
        <div class="empty-cart-card">
            <div class="icon-circle">
                <i class="fas fa-pizza-slice"></i>
            </div>
            <h2>Your cart is craving pizza!</h2>
            <p>It looks like you haven't added any of our gourmet crusts to your selection yet.</p>
            <a href="index.php" class="btn-primary btn-wide">
                <i class="fas fa-utensils"></i> Explore Our Menu
            </a>
        </div>
    </div>
<?php else: ?>
            <div class="cart-content">
                <div class="cart-table-container">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td class="product-cell">
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                </td>
                                <td>RM <?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="20" 
                                           class="qty-input" 
                                           onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                </td>
                                <td class="subtotal-cell">RM <?php echo number_format($item['subtotal'], 2); ?></td>
                                <td>
                                    <a href="remove_from_cart.php?id=<?php echo $item['id']; ?>" class="remove-link">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary-card">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="summary-subtotal">RM <?php echo number_format($grand_total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span class="text-free">FREE</span>
                    </div>
                    <hr>
                    <div class="summary-row total">
                        <span>Total Amount ： </span>
                        <span class="total-price" id="summary-total">RM <?php echo number_format($grand_total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn-primary checkout-btn">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
    async function updateQuantity(pizzaId, newQty) {
        if (newQty < 1) return;

        const formData = new FormData();
        formData.append('pizza_id', pizzaId);
        formData.append('quantity', newQty);

        try {
            const response = await fetch('update_cart.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();

            if (data.status === 'success') {
                // For a simple university project, location.reload() is the cleanest 
                // way to refresh all totals (subtotal, grand total) automatically.
                location.reload(); 
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error updating cart:', error);
        }
    }
    </script>
</body>
</html>