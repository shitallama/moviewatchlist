<?php
$basePath = '../';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;
if ($movie_id <= 0) {
    echo 'Invalid movie selected.';
    exit();
}

require_once __DIR__ . '/../includes/db.php';
$movieStmt = $pdo->prepare('SELECT title FROM Movies WHERE movie_id = ?');
$movieStmt->execute([$movie_id]);
$movie = $movieStmt->fetch(PDO::FETCH_ASSOC);
if (!$movie) {
    echo 'Movie not found.';
    exit();
}
$movieTitle = $movie['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Movie Reviews | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/review_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<section class="review-page">
    <div class="review-panel">
        <div class="review-header">
            <div>
                <span class="badge">Reviews</span>
                <h2>Review: <?= htmlspecialchars($movieTitle) ?></h2>
            </div>
            <div>
                <a href="../movie_management/view_movies.php" class="review-back-button">← Back to movie list</a>
            </div>
            <p class="review-subtitle">Share your rating and feedback for this movie.</p>
        </div>

        <form id="reviewForm" class="review-form">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
            <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie_id) ?>">

            <label for="rating">Rating</label>
            <select id="rating" name="rating" required>
                <option value="">Select</option>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐</option>
                <option value="3">3 ⭐</option>
                <option value="4">4 ⭐</option>
                <option value="5">5 ⭐</option>
            </select>

            <label for="review">Review</label>
            <textarea id="review" name="review" placeholder="Write your review..." required></textarea>

            <label class="recommend-toggle" for="is_recommended">
                <input type="checkbox" id="is_recommended" name="is_recommended" value="1">
                Recommend this movie to others
            </label>

            <button type="submit">Submit Review</button>
        </form>

        <div id="reviewList" class="review-list"></div>
    </div>
</section>

<?php require_once $basePath . 'includes/footer.php'; ?>
<script src="<?php echo $basePath; ?>assets/js/review_page.js"></script>
</body>
</html>
