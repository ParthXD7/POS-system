<?php
include 'db.php'; // include your database connection

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $details = htmlspecialchars($_POST['details']);
    $total_amount = floatval($_POST['total_amount']);

    // Update query
    $query = "UPDATE billing SET customer_name=?, phone_number=?, details=?, total_amount=? WHERE id=?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Correctly specify the parameter types: s = string, d = double, i = integer
    $bind = $stmt->bind_param("ssdsi", $customer_name, $phone_number, $details, $total_amount, $id);
    if ($bind === false) {
        die('Bind param error: ' . $stmt->error);
    }

    // Execute the statement
    $execute = $stmt->execute();
    if ($execute) {
        echo "Record updated successfully.";
        header('Location: index.php'); // Redirect to home after successful update
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Display the edit form if not a POST request
    $id = $_GET['id'];
    $query = "SELECT * FROM billing WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
?>
<form method="post" action="edit.php">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    Customer Name: <input type="text" name="customer_name" value="<?php echo $row['customer_name']; ?>"><br>
    Phone Number: <input type="text" name="phone_number" value="<?php echo $row['phone_number']; ?>"><br>
    Details: <textarea name="details"><?php echo $row['details']; ?></textarea><br>
    Total Amount: <input type="number" step="0.01" name="total_amount" value="<?php echo $row['total_amount']; ?>"><br>
    <input type="submit" value="Update Record">
</form>
<?php
}
?>
