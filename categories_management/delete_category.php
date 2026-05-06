<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "moviewatchlist");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $sql = "DELETE FROM categories WHERE category_id=$id";
    $conn->query($sql);
}

// Redirect back to view page
header("Location: view_category.php");
?>