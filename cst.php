<!DOCTYPE html>
<html>
<head>
    <title>Customer Details Form</title>
</head>
<body>
    <h2>Customer Details Form</h2>
    <form action="cst.php" method="post">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name"><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br><br>
        
        <label for="phone">Phone:</label><br>
        <input type="text" id="phone" name="phone"><br><br>
        
        <input type="submit" value="Submit">
    </form>
</body>
</html>

<?php
// Include the database connection file
require_once('db.php');

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // SQL to insert data into database
    $sql = "INSERT INTO customers (Name, Email, Phone) VALUES (?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("sss", $name, $email, $phone);
        
        // Execute statement
        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
