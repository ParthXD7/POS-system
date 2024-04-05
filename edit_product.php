<?php
require_once('db.php');

// Check for an ID and that it's a valid GET request
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    // Fetch the product for editing
    $id = trim($_GET['id']);

    // Prepare the select statement
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Set initial product data
        $name = $row['name'];
        $price = $row['price'];
        $quantity = $row['quantity'];
        $sku = $row['sku'];
    } else {
        // Redirect if the product doesn't exist
        echo "<script>alert('Product does not exist.'); window.location.href='product.php';</script>";
        exit();
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    // Handle the update operation
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $sku = $_POST['sku'];

    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, quantity = ?, sku = ? WHERE id = ?");
    $stmt->bind_param("sdisi", $name, $price, $quantity, $sku, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully.'); window.location.href='product.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
    exit();
} else {
    // Redirect if accessed without a valid product ID
    echo "<script>alert('Invalid access.'); window.location.href='product.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Product</h2>
        <form action="edit_product.php" method="post" class="col s12">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="row">
                <div class="input-field col s12">
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
                    <label for="name">Name</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($price); ?>">
                    <label for="price">Price</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>">
                    <label for="quantity">Quantity</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($sku); ?>">
                    <label for="sku">SKU</label>
                </div>
            </div>

            <button class="btn waves-effect waves-light" type="submit" name="action">Update Product
                <i class="material-icons right">update</i>
            </button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        // Initialize Materialize components
        document.addEventListener('DOMContentLoaded', function() {
            M.updateTextFields(); // Ensure labels float correctly
        });
    </script>
</body>
</html>
