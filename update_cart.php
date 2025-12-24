<?php
session_start();
header('Content-Type: application/json');

// Ensure the request is valid and user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    
    if (isset($_POST['pizza_id']) && isset($_POST['quantity'])) {
        $pizza_id = intval($_POST['pizza_id']);
        $new_qty = intval($_POST['quantity']);

        // Check if cart exists and quantity is valid
        if (isset($_SESSION['cart']) && $new_qty >= 1 && $new_qty <= 20) {
            
            if (isset($_SESSION['cart'][$pizza_id])) {
                $_SESSION['cart'][$pizza_id] = $new_qty;
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Quantity updated successfully'
                ]);
                exit;
            }
        }
    }
}

// Return error if something goes wrong
echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request or quantity'
]);
exit;