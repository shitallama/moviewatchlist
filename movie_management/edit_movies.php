<?php
include('../includes/db.php');

$id = $_GET['id'];

// Get movie
$result = $conn->query("SELECT * FROM movies WHERE movie_id=$id");
$row = $result->fetch_assoc();

// Update movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $rating = $_POST['rating'];
    $watched = isset($_POST['watched']) ? 1 : 0;

    $conn->query("UPDATE movies SET 
        title='$title',
        genre='$genre',
        release_date='$release_date',
        rating='$rating',
        watched='$watched'
        WHERE movie_id=$id");

    header("Location: view_movies.php");
}
?>

<h2>Edit Movie</h2>

<form method="POST">
Title: <input type="text" name="title" value="<?= $row['title'] ?>"><br>
Genre: <input type="text" name="genre" value="<?= $row['genre'] ?>"><br>
Date: <input type="date" name="release_date" value="<?= $row['release_date'] ?>"><br>
Rating: <input type="number" name="rating" value="<?= $row['rating'] ?>"><br>
Watched: <input type="checkbox" name="watched" <?= $row['watched'] ? 'checked' : '' ?>><br>
<button>Update</button>
</form>