 <?php
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

// Fetch some statistics for the dashboard
try {
    // Get total movies count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Movies");
    $totalMovies = $stmt->fetch()['count'];
    
    // Get top rated movies (assuming you have reviews table)
    $stmt = $pdo->query("
        SELECT m.*, AVG(r.rating) as avg_rating, COUNT(r.review_id) as review_count 
        FROM Movies m 
        LEFT JOIN Review r ON m.movie_id = r.movie_id 
        GROUP BY m.movie_id 
        ORDER BY avg_rating DESC 
        LIMIT 6
    ");
    $topMovies = $stmt->fetchAll();
    
    // Get recent movies
    $stmt = $pdo->query("SELECT * FROM Movies ORDER BY movie_id DESC LIMIT 8");
    $recentMovies = $stmt->fetchAll();
    
    // Get genres count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM genres");
    $totalGenres = $stmt->fetch()['count'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Review");
    $totalReviews = $stmt->fetch()['count'];

    $communitySql = "
        SELECT
            m.movie_id,
            m.title,
            m.genre,
            m.user_id,
            u.username,
            AVG(r.rating) AS avg_rating,
            COUNT(r.review_id) AS review_count,
            SUBSTRING_INDEX(
                GROUP_CONCAT(r.review_text ORDER BY r.created_at DESC SEPARATOR '|||'),
                '|||',
                1
            ) AS latest_review
        FROM Movies m
        JOIN Users u ON m.user_id = u.user_id
        LEFT JOIN Review r ON r.movie_id = m.movie_id
    ";

    $communitySql .= " GROUP BY m.movie_id ";

    $communitySql .= " ORDER BY MAX(r.created_at) DESC, m.movie_id DESC LIMIT 3 ";

    $stmt = $pdo->query($communitySql);
    $communityMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $totalMovies = 0;
    $topMovies = [];
    $recentMovies = [];
    $totalGenres = 0;
    $totalReviews = 0;
    $communityMovies = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>CineList</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/indexstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php require_once 'includes/header.php'; ?>
<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome to CineList</h1>
        <p>Your ultimate destination for movie management, reviews, and tracking</p>
        <?php if(!$isLoggedIn): ?>
            <div class="hero-buttons">
                <a href="Login/register.php" class="btn-primary">Get Started</a>
                <a href="Login/login.php" class="btn-secondary">Login</a>
            </div>
        <?php else: ?>
            <div class="hero-buttons">
                <a href="movie_management/view_movies.php" class="btn-primary">Browse Movies</a>
                <a href="watch_status_management/watchlist.php" class="btn-secondary">My Watchlist</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="stats-section">
    <div class="stats-container">
        <div class="stat-card">
            <img class="icon" src="assets/icons/film.svg" alt="" aria-hidden="true">
            <div class="stat-info">
                <h3><?php echo $totalMovies; ?></h3>
                <p>Total Movies</p>
            </div>
        </div>
        <div class="stat-card">
            <img class="icon" src="assets/icons/tag.svg" alt="" aria-hidden="true">
            <div class="stat-info">
                <h3><?php echo $totalGenres; ?></h3>
                <p>Genres</p>
            </div>
        </div>
        <div class="stat-card">
            <img class="icon" src="assets/icons/star.svg" alt="" aria-hidden="true">
            <div class="stat-info">
                <h3><?php echo $totalReviews; ?></h3>
                <p>User Reviews</p>
            </div>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <h2 class="section-title">Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img class="icon" src="assets/icons/video.svg" alt="" aria-hidden="true">
                </div>
                <h3>Movie Management</h3>
                <p>Manage your movie collection - Add, edit, delete and browse movies.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <img class="icon" src="assets/icons/eye.svg" alt="" aria-hidden="true">
                </div>
                <h3>Watch Status</h3>
                <p>Track your viewing progress - Mark movies as watched or to-watch.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <img class="icon" src="assets/icons/tag.svg" alt="" aria-hidden="true">
                </div>
                <h3>Genre Management</h3>
                <p>Organize movies by genres - Create, edit and categorize genres.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <img class="icon" src="assets/icons/star.svg" alt="" aria-hidden="true">
                </div>
                <h3>Review & Rating</h3>
                <p>Rate and review movies - Average ratings, comments and feedback system.</p>
            </div>
        </div>
    </div>
</div>

<div class="community-section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-kicker">Community</span>
                <h2 class="section-title">What others are watching</h2>
            </div>
            <p class="section-subtitle">See recent movies and quick impressions from other users.</p>
            <a class="reviews-link" href="review_system/all_reviews.php">View all community</a>
        </div>

        <?php if (empty($communityMovies)): ?>
            <p class="empty-state">No community activity yet. Check back soon for new reviews.</p>
        <?php else: ?>
            <div class="community-grid">
                <?php foreach ($communityMovies as $movie): ?>
                    <?php
                        $reviewSnippet = trim((string) $movie['latest_review']);
                        if ($reviewSnippet === '') {
                            $reviewSnippet = 'No reviews yet. Be the first to share your thoughts.';
                        }
                        if (strlen($reviewSnippet) > 140) {
                            $reviewSnippet = substr($reviewSnippet, 0, 137) . '...';
                        }
                        $reviewCount = (int) $movie['review_count'];
                        $avgRating = $reviewCount > 0 ? number_format((float) $movie['avg_rating'], 1) : null;
                    ?>
                    <article class="community-card">
                        <div class="community-meta">
                            <span class="community-genre"><?php echo htmlspecialchars($movie['genre']); ?></span>
                            <span class="community-user">Added by <?php echo htmlspecialchars($movie['username']); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <div class="community-rating">
                            <?php if ($avgRating !== null): ?>
                                <span class="rating-value"><?php echo $avgRating; ?></span>
                                <span class="rating-count"><?php echo $reviewCount; ?> reviews</span>
                            <?php else: ?>
                                <span class="rating-count">No ratings yet</span>
                            <?php endif; ?>
                        </div>
                        <p class="community-review">“<?php echo htmlspecialchars($reviewSnippet); ?>”</p>
                        <div class="community-actions">
                            <?php if ($isLoggedIn): ?>
                                <a class="community-link" href="review_system/review_page.php?movie_id=<?php echo (int) $movie['movie_id']; ?>">Read reviews</a>
                            <?php else: ?>
                                <a class="community-link" href="Login/login.php">Login to review</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require 'includes/footer.php';?>
</html>