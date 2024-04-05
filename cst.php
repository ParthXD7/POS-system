<?php
// Include the database connection file
require_once('db.php');

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // SQL to insert data into the customers table
    $sql = "INSERT INTO customers (Name, Email, Phone) VALUES (?, ?, ?)";

    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("sss", $name, $email, $phone);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // If successful, redirect to a new page or display success message
            echo "New record created successfully. <a href='customer.php'>View Customers</a>";
        } else {
            // Handle errors with execution
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        // Handle errors with statement preparation
        echo "Error preparing the statement: " . $conn->error;
    }

    // Close connection
    $conn->close();
} else {
    // If the form wasn't submitted via POST, display an error or redirect
    echo "Invalid request.";
}
?>
