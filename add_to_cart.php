<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $pizza_id = intval($_POST['pizza_id']);
    $qty = intval($_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Update quantity if already in cart, otherwise add new
    if (isset($_SESSION['cart'][$pizza_id])) {
        $_SESSION['cart'][$pizza_id] += $qty;
    } else {
        $_SESSION['cart'][$pizza_id] = $qty;
    }

    $total_items = array_sum($_SESSION['cart']);

    echo json_encode([
        'status' => 'success',
        'new_count' => $total_items
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized or invalid request'
    ]);
}