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
$stmt = $pdo->prepare("SELECT * FROM Movies WHERE movie_id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "Movie not found or access denied.";
    exit();
}

// Update movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $release_date = $_POST['release_date'] ?: null;
    $watch_date = $_POST['watch_date'] ?: null;
    $user_notes = trim($_POST['user_notes']) ?: null;
    $watched = isset($_POST['watched']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE Movies SET 
        title = ?,
        genre = ?,
        release_date = ?,
        watched = ?,
        watch_date = ?,
        user_notes = ?
        WHERE movie_id = ? AND user_id = ?");
    $stmt->execute([$title, $genre, $release_date, $watched, $watch_date, $user_notes, $id, $user_id]);

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

        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" id="watched" name="watched" value="1" <?= $row['watched'] ? 'checked' : '' ?>>
                Watched
            </label>
        </div>

        <div class="form-group">
            <label for="watch_date">Watch Date</label>
            <input type="date" id="watch_date" name="watch_date" value="<?= htmlspecialchars($row['watch_date']) ?>">
        </div>

        <div class="form-group">
            <label for="user_notes">Notes</label>
            <textarea id="user_notes" name="user_notes" rows="4"><?= htmlspecialchars($row['user_notes']) ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Update Movie</button>
            <a href="view_movies.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>