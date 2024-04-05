<?php
require_once('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute and close
    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully'); window.location.href='product.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='product.php';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Invalid request'); window.location.href='product.php';</script>";
}

$conn->close();
?>
