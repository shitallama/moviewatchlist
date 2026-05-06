<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "moviewatchlist");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get category ID
$id = $_GET['id'];

// Fetch existing data
$result = $conn->query("SELECT * FROM categories WHERE category_id=$id");
$row = $result->fetch_assoc();

// Update category
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $is_active = $_POST['is_active'];

    $sql = "UPDATE categories 
            SET name='$name', description='$description', is_active='$is_active'
            WHERE category_id=$id";

    $conn->query($sql);

    header("Location: view_category.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h2 class="section-title">Edit Category</h2>

    <form method="POST">
        <input type="text" name="name" value="<?php echo $row['name']; ?>" required><br><br>

        <textarea name="description"><?php echo $row['description']; ?></textarea><br><br>

        <label>Status:</label>
        <select name="is_active">
            <option value="1" <?php if($row['is_active']==1) echo "selected"; ?>>Active</option>
            <option value="0" <?php if($row['is_active']==0) echo "selected"; ?>>Inactive</option>
        </select><br><br>

        <button type="submit" name="update" class="btn-view">Update</button>
    </form>
</div>

</body>
</html>