<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Additional CSS for styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        .product {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product h2 {
            margin-top: 0;
        }
        .product p {
            margin: 10px 0;
        }
        .add-to-cart-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .remove-from-cart-btn {
            background-color: #f44336;
        }
        #cart {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.5s ease-out;
            transform: translateX(100%);
        }
        #cart.show {
            transform: translateX(0);
        }
        #cart-toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product List</h1>
        <div id="product-list">
            <?php
            // Database connection parameters
            require_once('db.php');

            // SQL query to fetch data from the "products" table
            $sql = "SELECT * FROM products";

            // Execute SQL query
            $result = $conn->query($sql);

            // Check if there are any rows returned
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    // Display product information
                    echo "<div class='product'>";
                    echo "<h2>" . $row["name"] . "</h2>";
                    echo "<p>Price: $" . $row["price"] . "</p>";
                    echo "<p>Quantity: " . $row["quantity"] . "</p>";
                    echo "<button class='add-to-cart-btn' onclick='addToCart(" . json_encode($row) . ")'>Add to Cart</button>";
                    echo "</div>";
                }
            } else {
                echo "<p>No products found.</p>";
            }

            // Close database connection
            $conn->close();
            ?>
        </div>
        <button id="cart-toggle-btn" onclick="toggleCart()">Cart</button>
        <div id="cart">
            <h2>Cart</h2>
            <ul id="cart-items"></ul>
            <p id="total-bill">Total Bill: $0</p>
            <button onclick="clearCart()">Clear Cart</button>
        </div>
    </div>

    <script>
        // Cart object to store products
        var cart = {
            items: [],
            total: 0,

            // Function to add product to the cart
            addProduct: function(product) {
                var existingProduct = this.items.find(item => item.id === product.id);
                if (existingProduct) {
                    existingProduct.quantity++;
                } else {
                    this.items.push({ id: product.id, name: product.name, price: product.price, quantity: 1 });
                }
                this.total += parseFloat(product.price);
                this.updateCart();
                document.getElementById('cart').classList.add('show');
            },

            // Function to remove a product from the cart
            removeProduct: function(index) {
                var removed = this.items.splice(index, 1)[0];
                this.total -= parseFloat(removed.price) * removed.quantity;
                this.updateCart();
            },

            // Function to update the cart UI
            updateCart: function() {
                var cartList = document.getElementById('cart-items');
                var totalBill = document.getElementById('total-bill');

                // Clear previous cart items
                cartList.innerHTML = '';

                // Update cart items
                this.items.forEach(function(item, index) {
                    var li = document.createElement('li');
                    li.textContent = item.name + ' - $' + item.price + ' x ' + item.quantity;
                    var removeBtn = document.createElement('button');
                    removeBtn.textContent = 'Remove';
                    removeBtn.classList.add('remove-from-cart-btn');
                    removeBtn.onclick = function() {
                        cart.removeProduct(index);
                    };
                    li.appendChild(removeBtn);
                    cartList.appendChild(li);
                });

                // Update total bill
                totalBill.textContent = 'Total Bill: $' + cart.total.toFixed(2);
            },

            // Function to clear the cart
            clearCart: function() {
                this.items = [];
                this.total = 0;
                this.updateCart();
            }
        };

        // Function to add product to the cart
        function addToCart(product) {
            cart.addProduct(product);
        }

        // Function to toggle the visibility of the cart
        function toggleCart() {
            var cartDiv = document.getElementById('cart');
            cartDiv.classList.toggle('show');
        }

        // Function to clear the cart
        function clearCart() {
            cart.clearCart();
            document.getElementById('cart').classList.remove('show');
        }
    </script>
</body>
</html>
