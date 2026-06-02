<?php
include('../includes/db.php');
require_once __DIR__ . '/MovieManager.php';
require_once __DIR__ . '/../admin/includes/AdminAuth.php';

session_start();

// Check login
AdminAuth::startSession();
$isAdmin = AdminAuth::isLoggedIn();

if (!isset($_SESSION['user_id']) && !$isAdmin) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$showAll = $isAdmin || (isset($_GET['all']) && $_GET['all'] === '1');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';
$watched = isset($_GET['watched']) && $_GET['watched'] !== '' ? (int)$_GET['watched'] : null;

$movieRepository = new MovieRepository($pdo);
$result = $movieRepository->find($user_id, $showAll, $search, $genre, $watched);

$movieIds = array_filter(array_map(fn($movie) => $movie->movie_id, $result), fn($id) => $id !== null);
$reviewsByMovie = $movieRepository->getReviewsByMovieIds($movieIds);
$avgRatingByMovie = [];

foreach ($reviewsByMovie as $movieId => $movieReviews) {
    $sum = 0;
    foreach ($movieReviews as $review) {
        $sum += intval($review['rating']);
    }
    $avgRatingByMovie[$movieId] = count($movieReviews) ? round($sum / count($movieReviews)) : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Movie List | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/manage.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/watchlist.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="manage-container">
    <div class="manage-header">
        <h2>My Movie Collection</h2>
        <p>Manage and track your personal movie library</p>
    </div>

    <!-- SEARCH + FILTER FORM -->
    <div class="filter-section">
        <form class="filter-form" method="GET">
            <div class="form-group">
                <label for="search">Search Movies</label>
                <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Enter movie title...">
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($_GET['genre'] ?? '') ?>" placeholder="Filter by genre...">
            </div>
            <button type="submit" class="btn-primary">Filter</button>
        </form>
    </div>

    <div class="action-links">
        <a href="add_movies.php" class="btn-primary">Add New Movie</a>
    </div>

    <table class="manage-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Genre</th>
                <th>Release Date</th>
                <th>Watch Date</th>
                <th>Rating</th>
                <th>Notes</th>
                <th>Reviews</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($result as $movie): ?>
            <tr>
                <td><?= htmlspecialchars($movie->title) ?></td>
                <td><?= htmlspecialchars($movie->genre) ?></td>
                <td><?= htmlspecialchars($movie->release_date) ?: 'N/A' ?></td>
                <td><?= htmlspecialchars($movie->watch_date) ?: 'N/A' ?></td>
                <td>
                    <?php if (!empty($avgRatingByMovie[$movie->movie_id])): ?>
                        <span class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $avgRatingByMovie[$movie->movie_id] ? 'filled' : '' ?>">&#9733;</span>
                            <?php endfor; ?>
                        </span>
                    <?php else: ?>
                        Not rated
                    <?php endif; ?>
                </td>
                <td>
                    <?= htmlspecialchars($movie->user_notes ?: 'No notes') ?>
                </td>
                <td>
                    <?php if (!empty($reviewsByMovie[$movie->movie_id])): ?>
                        <div class="movie-review-list">
                            <?php foreach ($reviewsByMovie[$movie->movie_id] as $review): ?>
                                <div class="movie-review-item">
                                    <p><?= htmlspecialchars($review['review_text']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <span class="no-reviews">No reviews yet</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="actions">
                        <a href="edit_movies.php?id=<?= $movie->movie_id ?>" class="act edit">Edit</a>
                        <a href="../review_system/review_page.php?movie_id=<?= $movie->movie_id ?>" class="act ghost">Reviews</a>
                        <form method="POST" action="../watch_status_management/add_to_watchlist.php" class="inline-form">
                            <input type="hidden" name="movie_id" value="<?= $movie->movie_id ?>">
                            <input type="hidden" name="redirect" value="../movie_management/view_movies.php">
                            <button type="submit" class="act add">Add to Watchlist</button>
                        </form>
                        <a href="delete_movies.php?id=<?= $movie->movie_id ?>" class="act del" onclick="return confirm('Are you sure you want to delete this movie?')">Delete</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if(empty($result)): ?>
    <div class="empty-state">
        <?php if ($showAll): ?>
            <p>No movies were found in the database.</p>
        <?php else: ?>
            <p>No movies found for your account. <a href="?all=1">Show all movies</a> or insert movies with your current user_id.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>