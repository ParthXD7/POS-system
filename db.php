<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pos"; // Make sure the database name is correct

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
