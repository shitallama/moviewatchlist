<?php
/**
 * AdminManager Class - Usage Examples
 * 
 * This file demonstrates how to use the AdminManager class
 * in your admin pages.
 */

// Example 1: Basic Usage
/*
require_once '../includes/db.php';
require_once 'includes/AdminManager.php';

$adminManager = new AdminManager($pdo);

// Get statistics
$totalUsers = $adminManager->getTotalUsers();
$totalMovies = $adminManager->getTotalMovies();
$totalGenres = $adminManager->getTotalGenres();
$totalReviews = $adminManager->getTotalReviews();
*/

// Example 2: Get all data
/*
$dashboardData = $adminManager->getDashboardData();
echo $dashboardData['stats']['total_users'];
echo $dashboardData['total_users'];
*/

// Example 3: Get specific data
/*
// Get all users
$allUsers = $adminManager->getAllUsers();

// Get all users limited to 10
$recentUsers = $adminManager->getAllUsers(10);

// Get all movies with ratings
$allMovies = $adminManager->getAllMovies();

// Get top 10 rated movies
$topMovies = $adminManager->getTopRatedMovies(10);

// Get all genres
$genres = $adminManager->getAllGenres();
*/

// Example 4: Search functionality
/*
// Search users
$searchResults = $adminManager->searchUsers('john');

// Search movies
$movieResults = $adminManager->searchMovies('Avengers');

// Get movies by genre
$actionMovies = $adminManager->getMoviesByGenre('Action');

// Get movies with rating >= 4.0
$topRated = $adminManager->getMoviesByMinimumRating(4.0);

// Get unrated movies
$unrated = $adminManager->getUnratedMovies();
*/

// Example 5: Individual lookups
/*
// Get specific user details
$user = $adminManager->getUserById(1);

// Get specific movie details
$movie = $adminManager->getMovieById(1);
*/

/**
 * Available AdminManager Methods
 * 
 * Statistics Methods:
 * - getTotalUsers()                      Returns: int
 * - getTotalMovies()                     Returns: int
 * - getTotalGenres()                     Returns: int
 * - getTotalReviews()                    Returns: int
 * - getDashboardStats()                  Returns: array
 * - getDashboardData()                   Returns: array
 * 
 * Data Retrieval Methods:
 * - getAllUsers($limit = null)           Returns: array
 * - getAllMovies($limit = null)          Returns: array
 * - getAllGenres()                       Returns: array
 * - getTopRatedMovies($limit = 5)        Returns: array
 * - getRecentUsers($limit = 5)           Returns: array
 * - getRecentMovies($limit = 5)          Returns: array
 * - getUserById($userId)                 Returns: array|null
 * - getMovieById($movieId)               Returns: array|null
 * 
 * Search Methods:
 * - searchUsers($searchTerm)             Returns: array
 * - searchMovies($searchTerm)            Returns: array
 * - getMoviesByGenre($genre)             Returns: array
 * - getMoviesByMinimumRating($minRating) Returns: array
 * - getUnratedMovies()                   Returns: array
 */

/**
 * Error Handling
 * 
 * The AdminManager class handles all database errors internally.
 * If an error occurs, methods return:
 * - Empty array [] for list methods
 * - null for single-item methods
 * - 0 for count methods
 * 
 * This prevents errors from breaking the dashboard.
 */

/**
 * Performance Notes
 * 
 * - Methods use prepared statements for SQL injection prevention
 * - getAllMovies() and getAllUsers() can return large datasets
 * - Use limit parameter for pagination
 * - Search methods use LIKE with % wildcards
 * - Ratings are calculated using LEFT JOIN and AVG/COUNT functions
 */

/**
 * Example: Using AdminManager in a custom admin page
 */

/*
<?php
require_once '../includes/db.php';
require_once 'includes/AdminManager.php';

// Initialize AdminManager
$adminManager = new AdminManager($pdo);

// Get all data needed for the page
$stats = $adminManager->getDashboardStats();
$users = $adminManager->getAllUsers(50);
$movies = $adminManager->getAllMovies(50);

// Display the data
echo "Total Users: " . $stats['total_users'];
echo "Total Movies: " . $stats['total_movies'];

// Iterate through users
foreach ($users as $user) {
    echo $user['username'] . " - " . $user['email'];
}

// Iterate through movies
foreach ($movies as $movie) {
    echo $movie['title'] . " - Rating: " . round($movie['avg_rating'], 1);
}
?>
*/
