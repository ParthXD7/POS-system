<?php
session_start();
header('Content-Type: application/json');

// Assuming you have already included your database connection and initialization here

// Retrieve productId and quantity from AJAX POST request
$productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$response = [];

if ($productId > 0 && $quantity >= 0) {
    // Assuming $_SESSION['cart'] is already initialized and structured as per your requirement
    if ($quantity == 0) {
        // Remove item from cart if quantity is 0
        unset($_SESSION['cart'][$productId]);
    } else {
        // Update item quantity in cart
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['qty'] = $quantity;
        }
    }

    // Calculate new total amount
    $newTotal = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $newTotal += $item['qty'] * $item['price'];
    }

    // Prepare response
    $response = [
        'success' => true,
        'newTotal' => number_format($newTotal, 2),
    ];
} else {
    // Handle error, e.g., product ID or quantity is missing or invalid
    $response = [
        'success' => false,
        'error' => 'Invalid product ID or quantity.'
    ];
}

// Send the response back to the AJAX request
echo json_encode($response);
?>
