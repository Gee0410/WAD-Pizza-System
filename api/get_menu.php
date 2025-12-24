<?php
include 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT * FROM pizzas");
    $pizzas = $stmt->fetchAll();
    echo json_encode($pizzas);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>