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
            <div class="col s12 m6">
                <div class="chart-container" style="position: relative; height:40vh; width:80vw">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
            <div class="col s12 m6">
                <div class="chart-container" style="position: relative; height:40vh; width:80vw">
                    <canvas id="myPieChart2"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
            <div class="col s12">
                <h5>Billing Details</h5>
                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Details</th>
                            <th>Total Amount</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    include 'db.php'; // Include your database connection
    $query = "SELECT * FROM billing";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
            echo "<td><pre>" . json_encode(json_decode($row['details']), JSON_PRETTY_PRINT) . "</pre></td>";
            echo "<td>" . number_format($row['total_amount'], 2) . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "<td><a href='edit.php?id=" . $row['id'] . "' class='btn-small blue'><i class='material-icons'>edit</i></a></td>";
            echo "<td><a href='delete.php?id=" . $row['id'] . "' class='btn-small red'><i class='material-icons'>delete</i></a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No data found</td></tr>";
    }
    $conn->close();
    ?>
</tbody>

                </table>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Point of Sale System. All rights reserved.</p>
    </footer>

    <script>
        
        <?php
        include 'db.php'; // Include your database connection

        $customersResult = $conn->query("SELECT COUNT(*) AS total_customers FROM customers");
        $productsResult = $conn->query("SELECT COUNT(*) AS total_items FROM products");
        $billsResult = $conn->query("SELECT COUNT(*) AS total_bills FROM billing");
        $amountResult = $conn->query("SELECT SUM(total_amount) AS total_amount FROM billing");

        $totalCustomers = $customersResult->fetch_assoc()['total_customers'] ?? 0;
        $totalItems = $productsResult->fetch_assoc()['total_items'] ?? 0;
        $totalBills = $billsResult->fetch_assoc()['total_bills'] ?? 0;
        $totalAmount = $amountResult->fetch_assoc()['total_amount'] ?? 0;

        $conn->close(); // Close the database connection
        ?>

        const data1 = {
            labels: ['Total Users', 'Total Items'],
            datasets: [{
                label: 'POS Data',
                data: [<?php echo $totalCustomers; ?>, <?php echo $totalItems; ?>],
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                borderColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)'],
                borderWidth: 1
            }]
        };

        const data2 = {
            labels: ['Total Bills', 'Total Amount'],
            datasets: [{
                label: 'Billing Data',
                data: [<?php echo $totalBills; ?>, <?php echo $totalAmount; ?>],
                backgroundColor: ['rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
                borderColor: ['rgb(255, 206, 86)', 'rgb(75, 192, 192)'],
                borderWidth: 1
            }]
        };

        const config1 = {
            type: 'pie',
            data: data1,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Point of Sale Data' }
                }
            }
        };

        const config2 = {
            type: 'pie',
            data: data2,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Billing Data Overview' }
                }
            }
        };

        new Chart(document.getElementById('myPieChart'), config1);
        new Chart(document.getElementById('myPieChart2'), config2);
    </script>
</body>
</html>
