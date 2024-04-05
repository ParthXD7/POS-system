<?php
require_once('db.php'); // Include your DB connection

if (isset($_POST['phone'])) {
    $phone = $_POST['phone'];
    $response = ['name' => '', 'exists' => false];

    $stmt = $conn->prepare("SELECT Name FROM customers WHERE Phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $response = ['name' => $row['Name'], 'exists' => true];
    }
    
    $stmt->close();
    echo json_encode($response);
    exit;
}
?>
