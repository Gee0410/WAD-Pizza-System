<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $item = [
        'id' => $data['id'],
        'name' => $data['name'],
        'size' => $data['size'],
        'price' => $data['price'],
        'qty' => 1
    ];

    // Simple cart logic: check if exists, then add
    $_SESSION['cart'][] = $item;
    echo json_encode(['status' => 'success', 'cart_count' => count($_SESSION['cart'])]);
}

if ($action === 'get') {
    $total = array_sum(array_column($_SESSION['cart'], 'price'));
    echo json_encode(['items' => $_SESSION['cart'], 'total' => $total]);
}
?>