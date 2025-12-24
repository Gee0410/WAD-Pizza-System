<?php
require_once 'db.php';
require_once 'auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Updated SQL to include delivery details
$query = "SELECT o.id as order_id, o.total_amount, o.status, o.created_at, 
                 o.customer_name, o.phone_number, o.delivery_address,
                 oi.quantity, oi.price_at_purchase, p.name as pizza_name 
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN pizzas p ON oi.pizza_id = p.id
          WHERE o.user_id = ?
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = $row['order_id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'info' => [
                'total' => $row['total_amount'],
                'status' => $row['status'],
                'date' => $row['created_at'],
                'cust_name' => $row['customer_name'],
                'phone' => $row['phone_number'],
                'address' => $row['delivery_address']
            ],
            'items' => []
        ];
    }
    $orders[$orderId]['items'][] = [
        'name' => $row['pizza_name'],
        'qty' => $row['quantity'],
        'price' => $row['price_at_purchase']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History | Pizza Delight</title>
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
        <div class="menu-header">
            <h2>Your Pizza Journey</h2>
            <p>Track your past orders and delivery details</p>
        </div>

       <?php if (empty($orders)): ?>
    <div class="empty-history-container">
        <div class="empty-history-card">
            <div class="status-icon">
                <i class="fas fa-history"></i>
            </div>
            <h2>No Orders Yet</h2>
            <p>Your pizza journey hasn't started! Browse our menu to discover our hand-tossed gourmet selections.</p>
            <a href="index.php" class="btn-primary btn-explore">
                <i class="fas fa-pizza-slice"></i> Start Your First Order
            </a>
        </div>
    </div>
<?php else: ?>
            <div class="history-list">
                <?php foreach ($orders as $id => $order): ?>
                    <div class="history-card">
                        <div class="history-card-header">
                            <div>
                                <span class="order-id">Order #<?php echo $id; ?></span>
                                <span class="order-date"><?php echo date('M d, Y h:i A', strtotime($order['info']['date'])); ?></span>
                            </div>
                            <span class="order-status status-<?php echo strtolower(str_replace(' ', '-', $order['info']['status'])); ?>">
                                <?php echo $order['info']['status']; ?>
                            </span>
                        </div>

                        <div class="history-grid">
                            <div class="history-items-section">
                                <h4>Items Ordered</h4>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="history-item">
                                        <span><?php echo $item['qty']; ?>x <?php echo htmlspecialchars($item['name']); ?></span>
                                        <span>RM <?php echo number_format($item['price'] * $item['qty'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="history-delivery-section">
                                <h4>Delivery To</h4>
                                <p><strong><i class="fas fa-user"></i></strong> <?php echo htmlspecialchars($order['info']['cust_name']); ?></p>
                                <p><strong><i class="fas fa-phone"></i></strong> <?php echo htmlspecialchars($order['info']['phone']); ?></p>
                                <p><strong><i class="fas fa-map-marker-alt"></i></strong> <?php echo nl2br(htmlspecialchars($order['info']['address'])); ?></p>
                            </div>
                        </div>

                        <div class="history-card-footer">
                            <strong>Total Paid: RM <?php echo number_format($order['info']['total'], 2); ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>