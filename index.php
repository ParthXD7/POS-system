<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale System</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>

<body>
    <header>
        <div class="navbar-fixed">
            <nav class="teal lighten-2">
                <div class="nav-wrapper">
                    <a href="#!" class="brand-logo center">Point Of Sale</a>
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
    
    <div class="container">
        <div class="row">
            <div class="col s12 m6 offset-m3">
                <div class="chart-container" style="position: relative; height:40vh; width:80vw">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2024 Point of Sale System. All rights reserved.</p>
    </footer>

    <script>
        <?php
        include 'db.php'; // Include your database connection

        // SQL queries to get the total count of customers and products
        $customersResult = $conn->query("SELECT COUNT(*) AS total_customers FROM customers");
        $productsResult = $conn->query("SELECT COUNT(*) AS total_items FROM products");

        $totalCustomers = 0;
        $totalItems = 0;

        if ($customersResult->num_rows > 0) {
            $row = $customersResult->fetch_assoc();
            $totalCustomers = $row['total_customers'];
        }

        if ($productsResult->num_rows > 0) {
            $row = $productsResult->fetch_assoc();
            $totalItems = $row['total_items'];
        }
        
        $conn->close(); // Close the database connection
        ?>

        // Embed PHP variables in JavaScript
        const data = {
            labels: ['Total Users', 'Total Items'],
            datasets: [{
                label: 'POS Data',
                data: [<?php echo $totalCustomers; ?>, <?php echo $totalItems; ?>], // Dynamic data from PHP
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                borderColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)'],
                borderWidth: 1
            }]
        };
        
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: false,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Point of Sale Data' }
                }
            },
        };

        const myPieChart = new Chart(
            document.getElementById('myPieChart'),
            config
        );


        
    </script>
</body>
</html>