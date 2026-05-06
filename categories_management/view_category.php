<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "moviewatchlist");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Categories</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h2 class="section-title">Categories List</h2>

    <a href="add_category.php" class="btn-view"> Add Category</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Created Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['category_id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <?php echo ($row['is_active'] == 1) ? "Active" : "Inactive"; ?>
            </td>
            <td>
                <a href="edit_category.php?id=<?php echo $row['category_id']; ?>">Edit</a> |
                <a href="delete_category.php?id=<?php echo $row['category_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>

    </table>
</div>

</body>
</html>