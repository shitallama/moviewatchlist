<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "moviewatchlist");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert category
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $sql = "INSERT INTO categories (name, description, created_at, is_active)
            VALUES ('$name', '$description', CURDATE(), 1)";

    $conn->query($sql);

    // Redirect after insert
    header("Location: view_category.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h2 class="section-title">Add Category</h2>

    <form method="POST">
        <input type="text" name="name" placeholder="Category Name" required><br><br>

        <textarea name="description" placeholder="Description"></textarea><br><br>

        <button type="submit" name="submit" class="btn-view">Add Category</button>
    </form>
</div>

</body>
</html>