<?php
require_once('db.php');

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the update form submission
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = "UPDATE customers SET Name=?, Email=?, Phone=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $phone, $id);
    $stmt->execute();

    echo "Customer updated successfully.";
    $stmt->close();
    $conn->close();
    // Redirect or display a message
} else if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    // Display the edit form with existing data
    $id = trim($_GET['id']);

    $sql = "SELECT * FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Display the form with values
        echo "<form action='edit_customer.php' method='post'>
                <input type='hidden' name='id' value='" . $row['id'] . "'/>
                Name: <input type='text' name='name' value='" . $row['Name'] . "'/><br/>
                Email: <input type='email' name='email' value='" . $row['Email'] . "'/><br/>
                Phone: <input type='text' name='phone' value='" . $row['Phone'] . "'/><br/>
                <input type='submit' value='Update'/>
              </form>";
    } else {
        echo "Customer not found.";
    }
    $stmt->close();
    $conn->close();
}
?>
