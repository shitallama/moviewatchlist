
<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Fetch some statistics for the dashboard
try {
    // Get total movies count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM movies");
    $totalMovies = $stmt->fetch()['count'];
    
    // Get top rated movies (assuming you have reviews table)
    $stmt = $pdo->query("
        SELECT m.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count 
        FROM movies m 
        LEFT JOIN reviews r ON m.id = r.movie_id 
        GROUP BY m.id 
        ORDER BY avg_rating DESC 
        LIMIT 6
    ");
    $topMovies = $stmt->fetchAll();
    
    // Get recent movies
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC LIMIT 8");
    $recentMovies = $stmt->fetchAll();
    
    // Get genres count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM genres");
    $totalGenres = $stmt->fetch()['count'];
    
} catch(PDOException $e) {
    $totalMovies = 0;
    $topMovies = [];
    $recentMovies = [];
    $totalGenres = 0;
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
<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome to MovieHub</h1>
        <p>Your ultimate destination for movie management, reviews, and tracking</p>
        <?php if(!$isLoggedIn): ?>
            <div class="hero-buttons">
                <a href="Login/register.php" class="btn-primary">Get Started</a>
                <a href="Login/login.php" class="btn-secondary">Login</a>
            </div>
        <?php else: ?>
            <div class="hero-buttons">
                <a href="movies.php" class="btn-primary">Browse Movies</a>
                <a href="watchlist.php" class="btn-secondary">My Watchlist</a>
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
            <img class="icon" src="assets/icons/users.svg" alt="" aria-hidden="true">
            <div class="stat-info">
                <h3>5+</h3>
                <p>Team Members</p>
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
                <h3>100+</h3>
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
                    <img class="icon" src="assets/icons/shield.svg" alt="" aria-hidden="true">
                </div>
                <h3>User Management</h3>
                <p>Complete user management system - Register, login, profile management and more.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <img class="icon" src="assets/icons/video.svg" alt="" aria-hidden="true">
                </div>
                <h3>Movie Management</h3>
                <p>Full CRUD operations for movies - Add, edit, delete and browse movies.</p>
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
                    <img class="icon" src="assets/icons/star-half.svg" alt="" aria-hidden="true">
                </div>
                <h3>Review & Rating</h3>
                <p>Rate and review movies - Average ratings, comments and feedback system.</p>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($topMovies)): ?>
<div class="top-rated-section">
    <div class="container">
        <h2 class="section-title">Top Rated Movies</h2>
        <div class="movies-slider">
            <?php foreach($topMovies as $movie): ?>
            <div class="movie-card">
                <div class="movie-poster">
                    <img src="images/placeholder.jpg" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <div class="movie-rating">
                        <img class="icon icon-warning" src="assets/icons/star-warning.svg" alt="" aria-hidden="true">
                        <span><?php echo number_format($movie['avg_rating'] ?? 0, 1); ?></span>
                    </div>
                </div>
                <div class="movie-info">
                    <h4><?php echo htmlspecialchars($movie['title']); ?></h4>
                    <p><?php echo $movie['year'] ?? 'N/A'; ?></p>
                    <a href="movie-details.php?id=<?php echo $movie['id']; ?>" class="btn-view">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require 'includes/footer.php';?>
</html>