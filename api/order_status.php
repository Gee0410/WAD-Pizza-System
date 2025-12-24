<?php
include 'db.php';
$orderId = $_GET['id'];
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();
echo json_encode(['status' => $order['status']]);
?>