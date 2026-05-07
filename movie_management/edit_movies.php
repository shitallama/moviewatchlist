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

<div class="manage-container">
    <div class="manage-header">
        <h2>Edit Movie</h2>
        <p>Update movie information in your collection</p>
    </div>

    <form class="manage-form" method="POST">
        <div class="form-group">
            <label for="title">Movie Title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($row['genre']) ?>" required>
        </div>

        <div class="form-group">
            <label for="release_date">Release Date</label>
            <input type="date" id="release_date" name="release_date" value="<?= htmlspecialchars($row['release_date']) ?>">
        </div>

        <div class="form-group">
            <label for="rating">Rating (1-5)</label>
            <input type="number" id="rating" name="rating" value="<?= htmlspecialchars($row['rating']) ?>" min="1" max="5">
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" id="watched" name="watched" <?= $row['watched'] ? 'checked' : '' ?>>
                <label for="watched">Mark as watched</label>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Update Movie</button>
            <a href="view_movies.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>