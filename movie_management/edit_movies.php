<?php
include('../includes/db.php');

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get movie
$stmt = $pdo->prepare("SELECT * FROM movies WHERE movie_id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "Movie not found or access denied.";
    exit();
}

// Update movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $rating = $_POST['rating'];
    $watched = isset($_POST['watched']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE movies SET 
        title = ?,
        genre = ?,
        release_date = ?,
        rating = ?,
        watched = ?
        WHERE movie_id = ? AND user_id = ?");
    $stmt->execute([$title, $genre, $release_date, $rating, $watched, $id, $user_id]);

    header("Location: view_movies.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Edit Movie | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/manage.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<h2>Edit Movie</h2>

<form method="POST">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>"><br>
    Genre: <input type="text" name="genre" value="<?= htmlspecialchars($row['genre']) ?>"><br>
    Date: <input type="date" name="release_date" value="<?= htmlspecialchars($row['release_date']) ?>"><br>
    Rating: <input type="number" name="rating" value="<?= htmlspecialchars($row['rating']) ?>"><br>
    Watched: <input type="checkbox" name="watched" <?= $row['watched'] ? 'checked' : '' ?>><br>
    <button>Update</button>
</form>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>