<?php
require_once('db.php');

// Get product ID from the AJAX request
$productId = $_GET['id'];

// SQL query to fetch product details by ID
$sql = "SELECT name, price FROM products WHERE id = $productId";

// Execute SQL query
$result = $conn->query($sql);

// Check if product exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $product = [
        'id' => $productId,
        'name' => $row['name'],
        'price' => $row['price'],
        'quantity' => 1
    ];
    // Return product details as JSON
    echo json_encode($product);
} else {
    // Return null if product not found
    echo json_encode(null);
}

// Close database connection
$conn->close();
?>
