<?php
// Include database connection
require_once('db.php');

// Initialize variables for sorting and searching
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Handle form submission for adding products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"])) {
    // Retrieve form data
    $name = $_POST["name"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $sku = $_POST["sku"]; // SKU field

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, sku) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $name, $price, $quantity, $sku);
    
    // Execute and close
    if ($stmt->execute()) {
        echo "<script>alert('Product added successfully');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch products with optional search
if ($search !== '') {
    $sql = "SELECT * FROM products WHERE name LIKE ? OR sku LIKE ? ORDER BY " . $sort;
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} else {
    $sql = "SELECT * FROM products ORDER BY " . $sort;
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<header>
<h1>Manage Inventory</h1>
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
    <form action="product.php" method="post" class="form-group">
        <input type="text" id="name" name="name" placeholder="Product Name" required><br>
        <input type="number" id="price" name="price" step="0.01" placeholder="Product Price" required><br>
        <input type="number" id="quantity" name="quantity" placeholder="Quantity" required><br>
        <input type="text" id="sku" name="sku" placeholder="SKU" required><br>
        <input type="submit" value="Add Product">
    </form>

    <form action="product.php" method="get" class="form-group">
        <input type="text" name="search" placeholder="Search products" value="<?php echo $search; ?>">
        <input type="submit" value="Search">
    </form>

    <!-- Products list -->
    <?php
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Product Name</th><th>Price</th><th>Quantity</th><th>SKU</th><th>Edit</th><th>Delete</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["name"]) . "</td>
                    <td>$" . htmlspecialchars($row["price"]) . "</td>
                    <td>" . htmlspecialchars($row["quantity"]) . "</td>
                    <td>" . htmlspecialchars($row["sku"]) . "</td>
                    <td><a href='edit_product.php?id=" . $row["id"] . "'>Edit</a></td>
                    <td><a href='delete_product.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No products found";
    }
    ?>
</main>

<footer>
    &copy; 2024 Product Management System
</footer>

</body>
</html>
