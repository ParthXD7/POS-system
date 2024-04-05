<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
        .container { margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Customer</h2>
    <?php
    require_once('db.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process the update form submission...
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

    // Check for errors in input data (optional)
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    // Add more validation as necessary...

    if (empty($errors)) {
        // Prepare the SQL statement
        $sql = "UPDATE customers SET Name=?, Email=?, Phone=? WHERE id=?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "Error preparing statement: " . htmlspecialchars($conn->error);
        } else {
            // Bind parameters and execute the statement
            $stmt->bind_param("sssi", $name, $email, $phone, $id);
            if ($stmt->execute()) {
                echo "Customer updated successfully.";

                // Optional: Redirect to another page after success
                 header("Location: customer.php");
                 exit;
            } else {
                echo "Error updating record: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<p>Error: $error</p>";
        }
    }

    $conn->close();
    } else if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
        // Display the edit form with existing data...
        $id = trim($_GET['id']);

        $sql = "SELECT * FROM customers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
    ?>
    <form action="edit_customer.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
        
        <div class="input-field">
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['Name']); ?>">
            <label for="name">Name</label>
        </div>
        
        <div class="input-field">
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>">
            <label for="email">Email</label>
        </div>
        
        <div class="input-field">
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($row['Phone']); ?>">
            <label for="phone">Phone</label>
        </div>
        
        <button type="submit" class="btn waves-effect waves-light">Update Customer
            <i class="material-icons right">send</i>
        </button>
    </form>
    <?php
        } else {
            echo "Customer not found.";
        }
        $stmt->close();
        $conn->close();
    }
    ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.updateTextFields();
    });
</script>

</body>
</html>
