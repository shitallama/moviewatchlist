<?php
include('../includes/db.php');

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';

// Add movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $rating = $_POST['rating'];
    $watched = isset($_POST['watched']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    // Validation
    if (empty($title) || empty($genre)) {
        echo "All fields required!";
    } elseif ($rating < 1 || $rating > 5) {
        echo "Rating must be 1-5";
    } else {
        $stmt = $pdo->prepare("INSERT INTO movies (title, genre, release_date, rating, watched, user_id)
                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $genre, $release_date, $rating, $watched, (int)$user_id]);
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

        <div class="form-group">
            <label for="rating">Rating (1-5)</label>
            <input type="number" id="rating" name="rating" min="1" max="5">
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" id="watched" name="watched">
                <label for="watched">Mark as watched</label>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Add Movie</button>
            <a href="view_movies.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>