<?php
include('../includes/db.php');
require_once __DIR__ . '/MovieManager.php';
require_once __DIR__ . '/../genres_management/Genre.php';
require_once __DIR__ . '/../admin/includes/AdminAuth.php';
require_once __DIR__ . '/../admin/includes/AdminManager.php';

session_start();

// Check login
AdminAuth::startSession();
$isAdmin = AdminAuth::isLoggedIn();

if (!isset($_SESSION['user_id']) && !$isAdmin) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';
$movieRepository = new MovieRepository($pdo);
$genreRepository = new GenreRepository($pdo);
$adminManager = new AdminManager($pdo);
$genres = $genreRepository->getAll();
$adminUsers = $isAdmin ? $adminManager->getAllUsers() : [];
$error = '';

// Add movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $release_date = $_POST['release_date'] ?: null;
    $watch_date = null;
    $user_notes = null;
    $watched = 0;
    $user_id = $isAdmin ? (int)($_POST['owner_user_id'] ?? 0) : (int)$_SESSION['user_id'];

    // Validation
    if (empty($title) || empty($genre)) {
        $error = "Title and Genre are required.";
    } elseif ($isAdmin && $user_id <= 0) {
        $error = "Please select a valid owner for this movie.";
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
            if ($isAdmin) {
                header("Location: ../admin/manage_movies.php");
            } else {
                header("Location: view_movies.php");
            }
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
        <?php if ($isAdmin): ?>
            <div class="form-group">
                <label for="owner_user_id">Owner</label>
                <select id="owner_user_id" name="owner_user_id" required>
                    <option value="">Select a user</option>
                    <?php foreach ($adminUsers as $adminUser): ?>
                        <option value="<?php echo (int)$adminUser['user_id']; ?>">
                            <?php echo htmlspecialchars($adminUser['username']); ?> (<?php echo htmlspecialchars($adminUser['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="title">Movie Title</label>
            <input type="text" id="title" name="title" placeholder="Enter movie title" required>
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
            <input type="date" id="release_date" name="release_date" placeholder="YYYY-MM-DD">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Add Movie</button>
            <?php if ($isAdmin): ?>
                <a href="<?php echo $basePath; ?>admin/manage_movies.php" class="btn-secondary">Cancel</a>
            <?php else: ?>
                <a href="view_movies.php" class="btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>