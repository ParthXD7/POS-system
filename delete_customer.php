<?php
require_once('db.php');

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);

    $sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Customer deleted successfully.";
    } else {
        echo "An error occurred.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
