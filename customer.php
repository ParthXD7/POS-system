<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <title>Customer Details Form</title>
</head>
<body>

<header>
        <div class="navbar-fixed">
            <nav class="teal lighten-2">
                <div class="nav-wrapper">
                    <a href="#!" class="brand-logo center">Customer</a>
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

<main>
    <div class="container">
        <h2 class="header">Enter Customer Details</h2>
        <form action="cst.php" method="post" class="col s12">
            <div class="row">
                <div class="input-field col s6">
                    <input placeholder="Enter name" id="name" type="text" class="validate" name="name">
                    <label for="name">Name</label>
                </div>
                <div class="input-field col s6">
                    <input placeholder="Enter email" id="email" type="email" class="validate" name="email">
                    <label for="email">Email</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input placeholder="Enter phone number" id="phone" type="tel" class="validate" name="phone">
                    <label for="phone">Phone</label>
                </div>
            </div>

            <button class="btn waves-effect waves-light" type="submit" name="action">Submit
                <i class="material-icons right">send</i>
            </button>
        </form>

        <h2 class="header">Search Customers</h2>
        <form action="" method="get" class="col s12">
            <div class="row">
                <div class="input-field col s12">
                    <input placeholder="Enter name or phone" id="searchTerm" type="text" class="validate" name="searchTerm">
                    <label for="searchTerm">Search (Name or Phone)</label>
                </div>
            </div>
            <button class="btn waves-effect waves-light" type="submit" name="action">Search
                <i class="material-icons right">search</i>
            </button>
        </form>
    </div>
</main>
<div class="container">
    <h4 class="header">Customer List</h4>

    <?php
    // Your existing PHP code for fetching the data
    require_once('db.php');
    $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
    $sql = "SELECT id, Name, Email, Phone FROM customers WHERE Name LIKE ? OR Phone LIKE ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $likeTerm = '%' . $searchTerm . '%';
        $stmt->bind_param("ss", $likeTerm, $likeTerm);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<table class='striped responsive-table'><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Edit</th><th>Delete</th></tr></thead><tbody>";
                
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["Name"]) . "</td>
                            <td>" . htmlspecialchars($row["Email"]) . "</td>
                            <td>" . htmlspecialchars($row["Phone"]) . "</td>
                            <td><a href='edit_customer.php?id=" . $row["id"] . "'>Edit</a></td>
                            <td><a href='delete_customer.php?id=" . $row["id"] . "' class='confirm-delete' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>
                          </tr>";
                }
                
                echo "</tbody></table>";
            } else {
                echo "<p>No records matching your query were found.</p>";
            }
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }
    $conn->close();
    ?>

</div>
<footer class="page-footer teal darken-2">
    <div class="container">
        © 2024 Customer Management System
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>

