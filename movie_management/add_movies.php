<?php
include('../includes/db.php');
require_once __DIR__ . '/MovieManager.php';
require_once __DIR__ . '/../genres_management/Genre.php';

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';
$movieRepository = new MovieRepository($pdo);
$genreRepository = new GenreRepository($pdo);
$genres = $genreRepository->getAll();
$error = '';

// Add movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $release_date = $_POST['release_date'] ?: null;
    $watch_date = null;
    $user_notes = null;
    $watched = 0;
    $user_id = (int)$_SESSION['user_id'];

    // Validation
    if (empty($title) || empty($genre)) {
        $error = "Title and Genre are required.";
    } else {
        try {
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
        } catch (Exception $e) {
            $error = 'Unable to add movie: ' . $e->getMessage();
        }
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

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form class="manage-form" method="POST">
        <div class="form-group">
            <label for="title">Movie Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre</label>
            <select id="genre" name="genre" required>
                <option value="">Select a genre</option>
                <?php foreach ($genres as $genreItem): ?>
                    <?php if ((int) $genreItem->is_active === 1): ?>
                        <option value="<?php echo htmlspecialchars($genreItem->name); ?>">
                            <?php echo htmlspecialchars($genreItem->name); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <a class="btn-secondary add-genre-link" href="<?php echo $basePath; ?>genres_management/add_genre.php">Add Genres</a>
        </div>

        <div class="form-group">
            <label for="release_date">Release Date</label>
            <input type="date" id="release_date" name="release_date">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Add Movie</button>
            <a href="view_movies.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>