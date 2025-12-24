<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Please login first']));
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$total = array_sum(array_column($cart, 'price'));

// 1. Insert into Orders Table
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, address, phone) VALUES (?, ?, ?, ?)");
$stmt->execute([$userId, $total, $data['address'], $data['phone']]);
$orderId = $conn->lastInsertId();

// 2. Insert Items into order_items Table
$itemStmt = $conn->prepare("INSERT INTO order_items (order_id, pizza_name, size, price) VALUES (?, ?, ?, ?)");
foreach ($cart as $item) {
    $itemStmt->execute([$orderId, $item['name'], $item['size'], $item['price']]);
}

// 3. Clear Cart
$_SESSION['cart'] = [];

echo json_encode(['status' => 'success', 'order_id' => $orderId]);
?>