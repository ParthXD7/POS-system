<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Customer Details Form</title>
</head>
<body>
    <header>
        <h1>Customer Management</h1>
        <nav>
        <ul class="menu">
            <li><a href="index.html">Home</a></li>
            <li><a href="bill.php">Billing</a></li>
            <li><a href="customer.php">Customers</a></li>
            <li><a href="product.php">Inventory</a></li>
        </ul>
    </nav>

    </header>


    <main>
        <form action="cst.php" method="post" class="form-group">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name"><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email"><br><br>
            
            <label for="phone">Phone:</label><br>
            <input type="tel" id="phone" name="phone"><br><br>
            
            <input type="submit" value="Submit">
        </form>

        <form action="" method="get" class="form-group">
            <label for="searchTerm">Search (Name or Phone):</label><br>
            <input type="text" id="searchTerm" name="searchTerm" placeholder="Enter name or phone"><br><br>
            <input type="submit" value="Search">
        </form>
    </main>

    <footer>
        &copy; 2024 Customer Management System
    </footer>
</body>
</html>

<?php
// Include the database connection file
require_once('db.php');

// Check if a search term is provided
$searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
// Updated SQL to select data from database, now including the id column
$sql = "SELECT id, Name, Email, Phone FROM customers WHERE Name LIKE ? OR Phone LIKE ?";

// Prepare statement
if ($stmt = $conn->prepare($sql)) {
    $likeTerm = '%' . $searchTerm . '%';
    $stmt->bind_param("ss", $likeTerm, $likeTerm);

    // Execute statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        // Check if there are any records
        if ($result->num_rows > 0) {
            // Start table and add header
            echo "<table border='1'><tr><th>Name</th><th>Email</th><th>Phone</th><th>Edit</th><th>Delete</th></tr>";
            
            // Fetch associative array for each row and output table row
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["Name"]) . "</td>
                        <td>" . htmlspecialchars($row["Email"]) . "</td>
                        <td>" . htmlspecialchars($row["Phone"]) . "</td>
                        <td><a href='edit_customer.php?id=" . $row["id"] . "'>Edit</a></td>
                        <td><a href='delete_customer.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>
                      </tr>";
            }
            
            // End table
            echo "</table>";
        } else {
            echo "No records matching your query were found.";
        }
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

?>
