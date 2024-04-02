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
    <!-- Additional meta tags -->
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
    <!-- Embedded styles moved to style.css -->
</head>
<body>
    <a href="index.html" class="back-to-home">Back to Home</a>
    <header>
        <h1>Add Product</h1>
    </header>
    <main>
        <form action="product.php" method="post">
            <!-- Form fields for name, price, quantity, and SKU -->
            <input type="text" id="name" name="name" placeholder="Product Name"><br>
            <input type="number" id="price" name="price" step="0.01" placeholder="Product Price"><br>
            <input type="number" id="quantity" name="quantity" placeholder="Quantity"><br>
            <input type="text" id="sku" name="sku" placeholder="SKU"><br>
            <input type="submit" value="Add Product">
        </form>
        
        <!-- Search form -->
        <form action="product.php" method="get">
            <input type="text" name="search" placeholder="Search products" value="<?php echo $search; ?>">
            <input type="submit" value="Search">
        </form>

        <!-- Products list -->
        <?php
        if ($result->num_rows > 0) {
            echo "<ul>";
            while($row = $result->fetch_assoc()) {
                echo "<li>" . $row["name"] . " - $" . $row["price"] . " - Quantity: " . $row["quantity"] . " - SKU: " . $row["sku"] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "No products found";
        }
        ?>
    </main>
</body>
</html>
