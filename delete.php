<?php
include 'db.php'; // include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Delete query
    $query = "DELETE FROM billing WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Record deleted successfully.";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    // Redirect or display a message
    header('Location: index.php'); // Redirect to home after delete
    exit();
} else {
    $id = $_GET['id'];
?>
<form method="post" action="delete.php">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    Are you sure you want to delete this record? <br>
    <input type="submit" value="Delete Record">
</form>
<?php
}
?>
