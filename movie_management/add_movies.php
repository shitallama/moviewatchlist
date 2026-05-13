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

// Add movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $release_date = $_POST['release_date'] ?: null;
    $watch_date = $_POST['watch_date'] ?: null;
    $user_notes = trim($_POST['user_notes']) ?: null;
    $watched = isset($_POST['watched']) ? 1 : 0;
    $user_id = (int)$_SESSION['user_id'];

    // Validation
    if (empty($title) || empty($genre)) {
        echo "Title and Genre are required.";
    } else {
        $movie = new Movie(
            null,
            $title,
            $genre,
            $release_date,
            $watched,
            $watch_date,
            $user_notes,
            $user_id
        );

        $movieRepository->add($movie);
        header("Location: view_movies.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Add Movie | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/manage.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="manage-container">
    <div class="manage-header">
        <h2>Add New Movie</h2>
        <p>Add a movie to your personal collection</p>
    </div>

    <form class="manage-form" method="POST">
        <div class="form-group">
            <label for="title">Movie Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" required>
        </div>

        <div class="form-group">
            <label for="release_date">Release Date</label>
            <input type="date" id="release_date" name="release_date">
        </div>

        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" id="watched" name="watched" value="1">
                Mark as Watched
            </label>
        </div>

        <div class="form-group">
            <label for="watch_date">Watch Date</label>
            <input type="date" id="watch_date" name="watch_date">
        </div>

        <div class="form-group">
            <label for="user_notes">Notes</label>
            <textarea id="user_notes" name="user_notes" rows="4" placeholder="Add your thoughts about this movie..."></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Add Movie</button>
            <a href="view_movies.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>