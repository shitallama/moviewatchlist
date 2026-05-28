<?php
$basePath = '../';
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

try {
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
        GROUP BY m.movie_id
        ORDER BY MAX(r.created_at) DESC, m.movie_id DESC
    ";

    $stmt = $pdo->query($communitySql);
    $communityMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $communityMovies = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>All Reviews | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/indexstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body data-current-user-id="<?php echo $currentUserId ? (int) $currentUserId : ''; ?>">
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="community-section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-kicker">Community</span>
                <h2 class="section-title">All community activity</h2>
            </div>
            <p class="section-subtitle">Browse every shared movie and review, then sort how you like.</p>
            <div class="community-controls">
                <div class="community-search">
                    <input type="search" class="community-search-input" placeholder="Search movies, genres, or users" aria-label="Search community">
                    <button type="button" class="community-search-btn">Search</button>
                </div>
                <div class="community-sort" role="tablist" aria-label="Community sorting">
                    <button class="sort-pill" type="button" data-sort="all" role="tab" aria-selected="false">All</button>
                    <button class="sort-pill" type="button" data-sort="highest" role="tab" aria-selected="false">Highest rated</button>
                    <button class="sort-pill" type="button" data-sort="most" role="tab" aria-selected="false">Most reviewed</button>
                    <button class="sort-pill" type="button" data-sort="latest" role="tab" aria-selected="false">Latest added</button>
                    <button class="sort-pill" type="button" data-sort="lowest" role="tab" aria-selected="false">Lowest rated</button>
                </div>
                <button class="toggle-mine" type="button" data-toggle-mine aria-pressed="true" <?php echo $currentUserId ? '' : 'disabled'; ?>>Hide my posts</button>
            </div>
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
                    <article
                        class="community-card"
                        data-movie-id="<?php echo (int) $movie['movie_id']; ?>"
                        data-rating="<?php echo $avgRating !== null ? $avgRating : ''; ?>"
                        data-reviews="<?php echo $reviewCount; ?>"
                        data-owner="<?php echo (int) $movie['user_id']; ?>"
                    >
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
                                <a class="community-link" href="<?php echo $basePath; ?>review_system/review_page.php?movie_id=<?php echo (int) $movie['movie_id']; ?>">Read reviews</a>
                            <?php else: ?>
                                <a class="community-link" href="<?php echo $basePath; ?>Login/login.php">Login to review</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
<script src="<?php echo $basePath; ?>assets/js/all_reviews.js"></script>
</body>
</html>
