<?php
include('../includes/db.php');
require_once __DIR__ . '/MovieManager.php';

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';
$movieRepository = new MovieRepository($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = (int)$_SESSION['user_id'];

if (!$id) {
    echo "No movie ID provided.";
    exit();
}

$movie = $movieRepository->getById($id, $user_id);

if (!$movie) {
    echo "Movie not found or access denied.";
    exit();
}

// Update movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie->title = trim($_POST['title']);
    $movie->genre = trim($_POST['genre']);
    $movie->release_date = $_POST['release_date'] ?: null;
    $movie->watch_date = $_POST['watch_date'] ?: null;
    $movie->user_notes = trim($_POST['user_notes']) ?: null;
    $movie->watched = isset($_POST['watched']) ? 1 : 0;

    $movieRepository->update($movie);

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
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($movie->title) ?>" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($movie->genre) ?>" required>
        </div>

        <div class="form-group">
            <label for="release_date">Release Date</label>
            <input type="date" id="release_date" name="release_date" value="<?= htmlspecialchars($movie->release_date) ?>">
        </div>

        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" id="watched" name="watched" value="1" <?= $movie->watched ? 'checked' : '' ?>>
                Watched
            </label>
        </div>

        <div class="form-group">
            <label for="watch_date">Watch Date</label>
            <input type="date" id="watch_date" name="watch_date" value="<?= htmlspecialchars($movie->watch_date) ?>">
        </div>

        <div class="form-group">
            <label for="user_notes">Notes</label>
            <textarea id="user_notes" name="user_notes" rows="4"><?= htmlspecialchars($movie->user_notes) ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Update Movie</button>
            <a href="view_movies.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>