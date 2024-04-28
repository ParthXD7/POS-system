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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>
<body>

<header>
        <div class="navbar-fixed">
            <nav class="teal lighten-2">
                <div class="nav-wrapper">
                    <a href="#!" class="brand-logo center">Inventory</a>
                    <ul class="left hide-on-med-and-down">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="bill.php">Billing</a></li>
                        <li><a href="customer.php">Customers</a></li>
                        <li><a href="product.php">Inventory</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

<main class="container">
    <h2>Add New Product</h2>
    <form action="product.php" method="post">
        <div class="input-field">
            <input type="text" id="name" name="name" required>
            <label for="name">Product Name</label>
        </div>
        <div class="input-field">
            <input type="number" id="price" name="price" step="0.01" required>
            <label for="price">Product Price ($)</label>
        </div>
        <div class="input-field">
            <input type="number" id="quantity" name="quantity" required>
            <label for="quantity">Quantity</label>
        </div>
        <div class="input-field">
            <input type="text" id="sku" name="sku" required>
            <label for="sku">SKU</label>
        </div>
        <button class="btn waves-effect waves-light" type="submit">Add Product
            <i class="material-icons right">add</i>
        </button>
    </form>

    <h2>Search Products</h2>
    <form action="product.php" method="get">
        <div class="input-field">
            <input type="text" name="search" id="search">
            <label for="search">Search Products</label>
        </div>
        <button class="btn waves-effect waves-light" type="submit">Search
            <i class="material-icons right">search</i>
        </button>
    </form>

    <!-- Products list display -->
    <h2>Product List</h2>
    <!-- PHP logic to check and display products would go here -->
    <!-- Sample static table structure -->
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

<footer class="page-footer teal darken-2">
    <div class="container">
        &copy; 2024 Product Management System
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
    });
</script>

</body>
</html>

