<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';
require_once $basePath . 'admin/includes/AdminManager.php';
require_once $basePath . 'includes/db.php';

// Check if admin is logged in
AdminAuth::requireLogin();

$adminUsername = AdminAuth::getUsername();

// Initialize AdminManager
$adminManager = new AdminManager($pdo);

// Fetch statistics and data using AdminManager
try {
    $totalUsers = $adminManager->getTotalUsers();
    $totalMovies = $adminManager->getTotalMovies();
    $totalGenres = $adminManager->getTotalGenres();
    $totalReviews = $adminManager->getTotalReviews();
    
    $allUsers = $adminManager->getAllUsers();
    $allMovies = $adminManager->getAllMovies();
    $allGenres = $adminManager->getAllGenres();
    
} catch(Exception $e) {
    $totalUsers = 0;
    $totalMovies = 0;
    $totalGenres = 0;
    $totalReviews = 0;
    $allUsers = [];
    $allMovies = [];
    $allGenres = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="admin-title">
                <i class="fas fa-crown"></i>
                <h1>Admin Dashboard</h1>
            </div>
            <div class="admin-user-info">
                <span>Logged in as: <strong><?php echo htmlspecialchars($adminUsername); ?></strong></span>
                <a href="<?php echo $basePath; ?>admin/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value"><?php echo $totalUsers; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Movies</div>
                <div class="stat-value"><?php echo $totalMovies; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Genres</div>
                <div class="stat-value"><?php echo $totalGenres; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Reviews</div>
                <div class="stat-value"><?php echo $totalReviews; ?></div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="table-card">
            <h2><i class="fas fa-users"></i> Users Management</h2>
            
            <div class="search-filter-bar">
                <div class="search-group">
                    <label for="userSearch">Search Users</label>
                    <input type="text" id="userSearch" placeholder="Search by username or email...">
                </div>
            </div>

            <?php if (!empty($allUsers)): ?>
                <table class="admin-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $user): ?>
                            <tr class="user-row" data-username="<?php echo strtolower(htmlspecialchars($user['username'])); ?>" data-email="<?php echo strtolower(htmlspecialchars($user['email'])); ?>">
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="result-count">
                    <span id="userCount"><?php echo count($allUsers); ?></span> user(s) found
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No users yet</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Movies Section -->
        <div class="table-card">
            <h2><i class="fas fa-film"></i> Movies Management</h2>
            
            <div class="search-filter-bar">
                <div class="search-group">
                    <label for="movieSearch">Search Movies</label>
                    <input type="text" id="movieSearch" placeholder="Search by title...">
                </div>
                <div class="search-group">
                    <label for="genreFilter">Filter by Genre</label>
                    <select id="genreFilter">
                        <option value="">All Genres</option>
                        <?php foreach ($allGenres as $genre): ?>
                            <option value="<?php echo strtolower(htmlspecialchars($genre['genre'])); ?>">
                                <?php echo htmlspecialchars($genre['genre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="search-group">
                    <label for="ratingFilter">Filter by Rating</label>
                    <select id="ratingFilter">
                        <option value="">All Ratings</option>
                        <option value="5">⭐⭐⭐⭐⭐ 5.0 Stars</option>
                        <option value="4">⭐⭐⭐⭐ 4.0+ Stars</option>
                        <option value="3">⭐⭐⭐ 3.0+ Stars</option>
                        <option value="2">⭐⭐ 2.0+ Stars</option>
                        <option value="1">⭐ 1.0+ Stars</option>
                        <option value="0">Not Rated</option>
                    </select>
                </div>
            </div>

            <?php if (!empty($allMovies)): ?>
                <table class="admin-table" id="moviesTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Release Date</th>
                            <th>Rating</th>
                            <th>Reviews</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allMovies as $movie): ?>
                            <tr class="movie-row" 
                                data-title="<?php echo strtolower(htmlspecialchars($movie['title'])); ?>"
                                data-genre="<?php echo strtolower(htmlspecialchars($movie['genre'] ?? '')); ?>"
                                data-rating="<?php echo round($movie['avg_rating'] ?? 0); ?>">
                                <td><strong><?php echo htmlspecialchars($movie['title']); ?></strong></td>
                                <td>
                                    <span style="background-color: #e8f4f8; color: #0277bd; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">
                                        <?php echo htmlspecialchars($movie['genre'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td><?php echo $movie['release_date'] ? date('M d, Y', strtotime($movie['release_date'])) : 'N/A'; ?></td>
                                <td>
                                    <?php if ($movie['avg_rating']): ?>
                                        <div class="rating-badge">
                                            <?php 
                                                $rating = round($movie['avg_rating'] * 2) / 2;
                                                for ($i = 0; $i < floor($rating); $i++) {
                                                    echo '<span class="rating-star"><i class="fas fa-star"></i></span>';
                                                }
                                                if ($rating - floor($rating) >= 0.5) {
                                                    echo '<span class="rating-star"><i class="fas fa-star-half-alt"></i></span>';
                                                }
                                            ?>
                                            <?php echo number_format($movie['avg_rating'], 1); ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #95a5a6; font-style: italic;">Not Rated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="background-color: #fff3e0; color: #e65100; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">
                                        <?php echo $movie['review_count']; ?> review<?php echo $movie['review_count'] !== 1 ? 's' : ''; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="result-count">
                    <span id="movieCount"><?php echo count($allMovies); ?></span> movie(s) found
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No movies yet</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <h2><i class="fas fa-link"></i> Quick Links</h2>
            <div class="quick-links-grid">
                <a href="<?php echo $basePath; ?>movie_management/view_movies.php" class="quick-link">
                    <i class="fas fa-clapperboard"></i> Manage Movies
                </a>
                <a href="<?php echo $basePath; ?>genres_management/view_genre.php" class="quick-link">
                    <i class="fas fa-list"></i> Manage Genres
                </a>
                <a href="<?php echo $basePath; ?>index.php" class="quick-link">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        // User Search Functionality
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const userRows = document.querySelectorAll('.user-row');
            let visibleCount = 0;

            userRows.forEach(row => {
                const username = row.getAttribute('data-username');
                const email = row.getAttribute('data-email');
                
                if (username.includes(searchTerm) || email.includes(searchTerm)) {
                    row.classList.remove('hidden-row');
                    visibleCount++;
                } else {
                    row.classList.add('hidden-row');
                }
            });

            document.getElementById('userCount').textContent = visibleCount;
        });

        // Movie Search Functionality
        document.getElementById('movieSearch').addEventListener('input', filterMovies);
        document.getElementById('genreFilter').addEventListener('change', filterMovies);
        document.getElementById('ratingFilter').addEventListener('change', filterMovies);

        function filterMovies() {
            const searchTerm = document.getElementById('movieSearch').value.toLowerCase().trim();
            const genreFilter = document.getElementById('genreFilter').value.toLowerCase().trim();
            const ratingFilter = document.getElementById('ratingFilter').value;
            const movieRows = document.querySelectorAll('.movie-row');
            let visibleCount = 0;

            movieRows.forEach(row => {
                const title = row.getAttribute('data-title');
                const genre = row.getAttribute('data-genre');
                const rating = parseInt(row.getAttribute('data-rating'));

                // Check search term
                let matchesSearch = title.includes(searchTerm);

                // Check genre filter
                let matchesGenre = !genreFilter || genre === genreFilter;

                // Check rating filter
                let matchesRating = true;
                if (ratingFilter !== '') {
                    const filterRating = parseInt(ratingFilter);
                    if (filterRating === 0) {
                        matchesRating = rating === 0;
                    } else {
                        matchesRating = rating >= filterRating;
                    }
                }

                // Show/hide row based on all filters
                if (matchesSearch && matchesGenre && matchesRating) {
                    row.classList.remove('hidden-row');
                    visibleCount++;
                } else {
                    row.classList.add('hidden-row');
                }
            });

            document.getElementById('movieCount').textContent = visibleCount;
        }

        // Add smooth transitions
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 12px 35px rgba(0, 0, 0, 0.15)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
            });
        });
    </script>

</body>
</html>
