<?php
session_start();
require_once('tcpdf/tcpdf.php'); // Ensure TCPDF is correctly included
require_once('db.php'); // Your database connection file

// Fetch products from the database
$products = [];
$query = "SELECT id, name, price FROM products";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[$row['id']] = $row;
    }
}
$totalAmount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalAmount += $item['qty'] * $item['price'];
    }
}

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product'];
    $quantity = $_POST['quantity'];
    
    if(isset($products[$productId])) {
        $product = $products[$productId];
        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = ['name' => $product['name'], 'qty' => $quantity, 'price' => $product['price']];
        } else {
            $_SESSION['cart'][$productId]['qty'] += $quantity;
        }
    }
}
// Handle removal of items from the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
  $productId = $_POST['remove'];
  unset($_SESSION['cart'][$productId]);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
  foreach ($_POST['quantities'] as $productId => $quantity) {
      if ($quantity == 0) {
          unset($_SESSION['cart'][$productId]);
      } else {
          $_SESSION['cart'][$productId]['qty'] = $quantity;
      }
  }
}
// Generate and save bill, then generate PDF
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_bill'])) {
    $phoneNumber = $_POST['phone'];
    $customerName = ''; // Variable to store the customer's name

    // Adjusted to fetch the customer's name as well
    $customerCheckQuery = "SELECT id, Name FROM customers WHERE Phone = ?";
    $stmt = $conn->prepare($customerCheckQuery);
    $stmt->bind_param("s", $phoneNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insert new customer (with a placeholder name if necessary)
        // Adjust according to how you'd like to handle unnamed customers
        $customerName = "Unknown";
        $insertCustomerQuery = "INSERT INTO customers (Name, Email, Phone) VALUES (?, '', ?)";
        $stmt = $conn->prepare($insertCustomerQuery);
        $stmt->bind_param("ss", $customerName, $phoneNumber);
        $stmt->execute();
        $customerId = $conn->insert_id;
    } else {
        $customerRow = $result->fetch_assoc();
        $customerId = $customerRow['id'];
        $customerName = $customerRow['Name']; // Now you have the customer's name
    }
    $stmt->close();

    // Insert order details here based on your schema

    // Proceed to generate PDF with customer's name and phone number
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Store Name');
    $pdf->SetTitle('Customer Bill');
    $pdf->AddPage();
    $html = "<h2>Bill Details</h2><h3>Customer: {$customerName} - {$phoneNumber}</h3><table border=\"1\" cellpadding=\"6\"><tr><th>Product Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";
    $grandTotal = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $total = $item['qty'] * $item['price'];
        $grandTotal += $total;
        $html .= "<tr><td>{$item['name']}</td><td>{$item['qty']}</td><td>\${$item['price']}</td><td>\$" . number_format($total, 2) . "</td></tr>";
    }
    $html .= "<tr><td colspan='3' style='text-align:right;'>Grand Total:</td><td>\$" . number_format($grandTotal, 2) . "</td></tr></table>";
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('bill.pdf', 'I');

    unset($_SESSION['cart']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Bill</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; }
        .form-control { margin-bottom: 10px; }
        input[type="number"], select { width: 100%; padding: 8px; margin: 10px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { width: 100%; background-color: #4CAF50; color: white; padding: 14px 20px; margin: 8px 0; border: none; border-radius: 4px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
    </style>
        <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="number"], select {
            width: 60%;
            padding: 8px;
            margin: 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"], button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #45a049;
        }
        .remove-btn {
            background-color: #FF6347;
            color: white;
        }
        .remove-btn:hover {
            background-color: #FF4500;
        }
    </style>
</head>
<body>
<h2>Add Products to Bill</h2>
<form action="bill.php" method="post">
<div class="form-control">
        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" title="Please enter a 10-digit phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
    </div>
    <div class="form-control">
        <label for="product">Product:</label>
        <select id="product" name="product">
            <?php foreach ($products as $id => $product): ?>
            <option value="<?php echo $id; ?>"><?php echo $product['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-control">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="1" min="1">
    </div>
    <input type="submit" name="add_to_cart" value="Add to Cart">
    <input type="submit" name="generate_bill" value="Generate Bill">
</form>
<!-- Start of form for updating and removing items from the cart -->
<form action="bill.php" method="post">
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>
                            <input type="number" name="quantities[<?php echo $id; ?>]" value="<?php echo $item['qty']; ?>" min="1" onchange="updateQuantity(<?php echo $id; ?>, this.value)">

                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['qty'] * $item['price'], 2); ?></td>
                        <td>
                            <!-- Change button to input type="submit" for remove action -->
                            <input type="submit" name="remove" value="<?php echo $id; ?>" class="remove-btn" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Your cart is empty.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Before the Update Cart button -->
<?php if (!empty($_SESSION['cart'])): ?>
<div style="text-align: right; margin-top: 20px;">
    <strong>Total Amount: $<?php echo number_format($totalAmount, 2); ?></strong>
</div>
<input type="submit" name="update_cart" value="Update Cart" style="margin-top: 10px;">
<?php endif; ?>

</form>
<!-- Add this JavaScript function inside the <head> tag or at the end of your <body> tag -->
<script>
function updateQuantity(productId, newQuantity) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_cart_quantity.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        // Handle the response here
        if (this.status == 200) {
            var response = JSON.parse(this.responseText);
            // Update the total amount on the page
            document.getElementById('totalAmount').innerText = 'Total Amount: $' + response.newTotal;
        }
    };
    xhr.send('productId=' + productId + '&quantity=' + newQuantity);
}
</script>

</body>

</html>
