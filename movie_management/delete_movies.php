<?php
include('../includes/db.php');

// Delete movie
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn->query("DELETE FROM movies WHERE movie_id=$id");
}

header("Location: view_movies.php");
?>