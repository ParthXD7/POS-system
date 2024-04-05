<?php
session_start();
require_once('tcpdf/tcpdf.php'); // Ensure TCPDF is correctly included
require_once('db.php'); // Your database connection file

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
// Fetch products from the database, including quantity
$products = [];
$query = "SELECT * FROM products";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[$row['id']] = $row;
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_cart'])) {
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
    } elseif (isset($_POST['remove'])) {
        $productId = $_POST['remove'];
        unset($_SESSION['cart'][$productId]);
    } elseif (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $productId => $quantity) {
            if ($quantity == 0) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId]['qty'] = $quantity;
            }
        }
    } elseif (isset($_POST['generate_bill'])) {
        $phoneNumber = $_POST['phone'] ?? '';
        // Assuming customer's name is also sent via POST. Adjust as needed.
        $customerName = $_POST['customer_name'] ?? 'Unknown';
        if (empty($phoneNumber)) {
            // Handle the case where phone number is not provided. You might want to set an error message here.
            $error = "Please enter a phone number to generate the bill.";
        } else {
            // Your existing logic for generating the bill, assuming phone number is available....
            foreach ($_SESSION['cart'] as $productId => $item) {
                $newQty = // Fetch the current quantity from the database and subtract $item['qty']
                $updateStmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $updateStmt->bind_param("ii", $item['qty'], $productId);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

        // Check for existing customer or add new one
        $stmt = $conn->prepare("SELECT id FROM customers WHERE Phone = ?");
        $stmt->bind_param("s", $phoneNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            // Insert new customer
            $insertStmt = $conn->prepare("INSERT INTO customers (Name, Phone) VALUES (?, ?)");
            $insertStmt->bind_param("ss", $customerName, $phoneNumber);
            $insertStmt->execute();
            $customerId = $conn->insert_id;
            $insertStmt->close();
        } else {
            $customer = $result->fetch_assoc();
            $customerId = $customer['id'];
        }
        $stmt->close();
        
        // Generate PDF and reset cart
        generatePDF($customerName, $phoneNumber, $_SESSION['cart']);
        unset($_SESSION['cart']); // Clear cart after generating bill
        // Redirect or display a message here as needed
    }

}

// Calculate total amount
$totalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['qty'] * $item['price'];
}

function generatePDF($customerName, $phoneNumber, $cart) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Store Name');
    $pdf->SetTitle('Customer Bill');
    $pdf->AddPage();
    $html = "<h2>Bill Details</h2><h3>Customer: $customerName - $phoneNumber</h3><table border=\"1\" cellpadding=\"6\"><tr><th>Product Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";
    $grandTotal = 0;
    foreach ($cart as $id => $item) {
        $total = $item['qty'] * $item['price'];
        $grandTotal += $total;
        $html .= "<tr><td>{$item['name']}</td><td>{$item['qty']}</td><td>\${$item['price']}</td><td>\$" . number_format($total, 2) . "</td></tr>";
    }
    $html .= "<tr><td colspan='3' style='text-align:right;'>Grand Total:</td><td>\$" . number_format($grandTotal, 2) . "</td></tr></table>";
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('bill.pdf', 'I'); // Consider saving to a file instead of direct output
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Bill</title>
    <!-- Add your stylesheet link here -->
    <!-- Link to Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="styles.css" />

<!-- Link to jQuery (necessary for Select2) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Link to Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

</head>
<body>
<header>
        <h1>Billing</h1>
        <nav>
        <ul class="menu">
            <li><a href="index.html">Home</a></li>
            <li><a href="bill.php">Billing</a></li>
            <li><a href="customer.php">Customers</a></li>
            <li><a href="product.php">Inventory</a></li>
        </ul>
    </nav>

    </header>
    <!-- Customer Details and Product Addition Form -->
    <form action="bill.php" method="post"class="form-group">
    <label for="phone">Phone Number:</label>
<input type="tel" id="phone" name="phone">

<label for="customer_name">Customer Name:</label>
<input type="text" id="customer_name" name="customer_name" readonly>

        <label for="product">Product:</label>

        <select id="product" name="product" onchange="updateMaxQuantity()">
    <?php foreach ($products as $id => $product): ?>
        <option value="<?php echo htmlspecialchars($id); ?>"
                data-quantity="<?php echo htmlspecialchars($product['quantity']); ?>"
                <?php echo ($product['quantity'] == 0) ? 'disabled' : ''; ?>
                style="color: <?php echo ($product['quantity'] < 10 && $product['quantity'] > 0) ? 'red' : 'initial'; ?>">
            <?php echo htmlspecialchars($product['sku']  .' ' . $product['name'] . ' - Quantity: ' . $product['quantity']); ?>
        </option>
    <?php endforeach; ?>
</select>



        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="1" min="1" max="100"> <!-- Initial max value can be any placeholder -->

        <input type="submit" name="add_to_cart" value="Add to Cart">
        <input type="submit" name="generate_bill" value="Generate Bill" id="generate_bill_btn">

    </form>

    <!-- Cart Display and Item Management -->
    <?php if (!empty($_SESSION['cart'])): ?>
        <form action="bill.php" method="post" >
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>
                        <input type="number" name="quantities[<?php echo $id; ?>]" value="<?php echo $item['qty']; ?>" min="1" class="quantity-input">
                    </td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($item['qty'] * $item['price'], 2); ?></td>
                    <td>
                        <button type="submit" name="remove" value="<?php echo $id; ?>" class="remove-button">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="total-label">Total Amount:</td>
                <td>$<?php echo number_format($totalAmount, 2); ?></td>
            </tr>
        </tbody>
    </table>
    <input type="submit" name="update_cart" value="Update Cart" class="submit-button">
</form>
<footer>
        <p>&copy; 2024 Point of Sale System. All rights reserved.</p>
    </footer>
    <?php endif; ?>
    <script>
        document.getElementById('generate_bill_btn').addEventListener('click', function(event) {
    var phone = document.getElementById('phone').value;
    if (!phone) {
        alert("Please enter a phone number to generate the bill.");
        event.preventDefault(); // Prevent form submission
    }
});
$(document).ready(function() {
    $('#product').select2();
});


document.getElementById('phone').addEventListener('input', function() {
    var phone = this.value;
    if (phone.length >= 10) { // Assuming a 10-digit phone number
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'get_customer_info.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status == 200) {
                try {
                    var response = JSON.parse(this.responseText);
                    if (response.exists) {
                        document.getElementById('customer_name').value = response.name;
                    } else {
                        document.getElementById('customer_name').value = '';
                    }
                } catch (e) {
                    console.log(e);
                }
            }
        };
        xhr.send('phone=' + phone);
    }
});
</script>
</body>


</html>
