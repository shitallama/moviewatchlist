# AdminManager Class Documentation

## Overview

The `AdminManager` class handles all admin-related database operations for the CineList admin dashboard. It follows the same object-oriented design pattern as the `UserManager` class and uses prepared statements for security.

## Location

```
admin/includes/AdminManager.php
```

## Basic Usage

### Initialize the AdminManager

```php
require_once 'admin/includes/AdminManager.php';
require_once 'includes/db.php';

$adminManager = new AdminManager($pdo);
```

### Get Statistics

```php
$totalUsers = $adminManager->getTotalUsers();
$totalMovies = $adminManager->getTotalMovies();
$totalGenres = $adminManager->getTotalGenres();
$totalReviews = $adminManager->getTotalReviews();

// Or get all stats at once
$stats = $adminManager->getDashboardStats();
// Returns: ['total_users' => int, 'total_movies' => int, ...]
```

## Available Methods

### Statistics Methods

| Method | Return Type | Description |
|--------|-------------|-------------|
| `getTotalUsers()` | int | Get total number of users |
| `getTotalMovies()` | int | Get total number of movies |
| `getTotalGenres()` | int | Get total number of genres |
| `getTotalReviews()` | int | Get total number of reviews |
| `getDashboardStats()` | array | Get all statistics in one call |
| `getDashboardData()` | array | Get complete dashboard data (stats + all data) |

### Data Retrieval Methods

| Method | Parameters | Return Type | Description |
|--------|-----------|------------|-------------|
| `getAllUsers($limit)` | limit?: int | array | Get all users, optionally limited |
| `getAllMovies($limit)` | limit?: int | array | Get all movies with ratings, optionally limited |
| `getAllGenres()` | - | array | Get all distinct genres |
| `getTopRatedMovies($limit)` | limit?: int (default: 5) | array | Get top-rated movies |
| `getRecentUsers($limit)` | limit?: int (default: 5) | array | Get recently joined users |
| `getRecentMovies($limit)` | limit?: int (default: 5) | array | Get recently added movies |
| `getUserById($userId)` | userId: int | array\|null | Get specific user by ID |
| `getMovieById($movieId)` | movieId: int | array\|null | Get specific movie by ID |

### Search Methods

| Method | Parameters | Return Type | Description |
|--------|-----------|------------|-------------|
| `searchUsers($searchTerm)` | searchTerm: string | array | Search users by username or email |
| `searchMovies($searchTerm)` | searchTerm: string | array | Search movies by title |
| `getMoviesByGenre($genre)` | genre: string | array | Get all movies of a specific genre |
| `getMoviesByMinimumRating($minRating)` | minRating: float | array | Get movies with minimum average rating |
| `getUnratedMovies()` | - | array | Get movies with no ratings yet |

## Usage Examples

### Example 1: Dashboard Statistics

```php
$adminManager = new AdminManager($pdo);

// Get all statistics
$stats = $adminManager->getDashboardStats();

echo "Users: " . $stats['total_users'];
echo "Movies: " . $stats['total_movies'];
echo "Genres: " . $stats['total_genres'];
echo "Reviews: " . $stats['total_reviews'];
```

### Example 2: Display All Users

```php
$users = $adminManager->getAllUsers();

foreach ($users as $user) {
    echo $user['username'] . " (" . $user['email'] . ")";
    echo "Joined: " . date('M d, Y', strtotime($user['created_at']));
}
```

### Example 3: Display Top Rated Movies

```php
$topMovies = $adminManager->getTopRatedMovies(10);

foreach ($topMovies as $movie) {
    $rating = round($movie['avg_rating'], 1) ?? 'Not Rated';
    echo $movie['title'] . " - Rating: " . $rating;
    echo "Reviews: " . $movie['review_count'];
}
```

### Example 4: Search Functionality

```php
// Search users
$results = $adminManager->searchUsers('john');

// Search movies
$results = $adminManager->searchMovies('Avengers');

// Filter by genre
$actionMovies = $adminManager->getMoviesByGenre('Action');

// Filter by minimum rating
$highRated = $adminManager->getMoviesByMinimumRating(4.0);
```

### Example 5: Get Complete Dashboard Data

```php
$dashboardData = $adminManager->getDashboardData();

// Access different data types
$stats = $dashboardData['stats'];
$allUsers = $dashboardData['all_users'];
$allMovies = $dashboardData['all_movies'];
$genres = $dashboardData['all_genres'];
$topMovies = $dashboardData['top_movies'];
$recentUsers = $dashboardData['recent_users'];
$recentMovies = $dashboardData['recent_movies'];
```

## Data Structure Examples

### User Data Structure

```php
[
    'user_id' => 1,
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'created_at' => '2024-01-15 10:30:45'
]
```

### Movie Data Structure

```php
[
    'movie_id' => 1,
    'title' => 'Inception',
    'genre' => 'Sci-Fi',
    'release_date' => '2010-07-16',
    'rating' => 4,
    'watched' => 0,
    'watch_date' => null,
    'user_notes' => 'Great movie!',
    'user_id' => 1,
    'review_count' => 5,          // Number of reviews
    'avg_rating' => 4.2           // Average rating from reviews
]
```

### Genre Data Structure

```php
[
    'genre' => 'Action'
]
```

## Error Handling

The AdminManager class handles all database errors gracefully:

- **List methods** return empty array `[]` on error
- **Single-item methods** return `null` on error
- **Count methods** return `0` on error

This prevents the dashboard from breaking if there are database issues.

```php
// Safe to use even if database has issues
$users = $adminManager->getAllUsers();  // Returns [] if error
$movie = $adminManager->getMovieById(1); // Returns null if not found or error
$count = $adminManager->getTotalUsers(); // Returns 0 if error
```

## Security Features

1. **Prepared Statements**: All queries use prepared statements to prevent SQL injection
2. **Parameter Binding**: Values are bound separately from SQL queries
3. **Error Suppression**: Database errors don't expose sensitive information

## Performance Considerations

- **Large Datasets**: Use `limit` parameter for pagination with many users/movies
- **Search Performance**: Search methods use LIKE with pattern matching
- **Rating Calculations**: Movies include rating calculations via JOIN and aggregation
- **Caching**: Consider caching statistics for frequently accessed data

## Integration with Dashboard

The dashboard uses AdminManager like this:

```php
$adminManager = new AdminManager($pdo);

$totalUsers = $adminManager->getTotalUsers();
$totalMovies = $adminManager->getTotalMovies();
$totalGenres = $adminManager->getTotalGenres();
$totalReviews = $adminManager->getTotalReviews();

$allUsers = $adminManager->getAllUsers();
$allMovies = $adminManager->getAllMovies();
$allGenres = $adminManager->getAllGenres();
```

## Creating Custom Admin Pages

To create a new admin page using AdminManager:

```php
<?php
require_once '../includes/db.php';
require_once 'includes/AdminAuth.php';
require_once 'includes/AdminManager.php';

// Check admin is logged in
AdminAuth::requireLogin();

// Initialize manager
$adminManager = new AdminManager($pdo);

// Get required data
$data = $adminManager->getWhateverYouNeed();

// Use $data in your HTML
?>
```

## Class Architecture

```
AdminManager
├── Constructor (__construct)
├── Statistics
│   ├── getTotalUsers()
│   ├── getTotalMovies()
│   ├── getTotalGenres()
│   ├── getTotalReviews()
│   ├── getDashboardStats()
│   └── getDashboardData()
├── Data Retrieval
│   ├── getAllUsers()
│   ├── getAllMovies()
│   ├── getAllGenres()
│   ├── getTopRatedMovies()
│   ├── getRecentUsers()
│   ├── getRecentMovies()
│   ├── getUserById()
│   └── getMovieById()
└── Search & Filter
    ├── searchUsers()
    ├── searchMovies()
    ├── getMoviesByGenre()
    ├── getMoviesByMinimumRating()
    └── getUnratedMovies()
```

## Extending AdminManager

To add custom methods:

```php
class AdminManager {
    // ... existing methods ...
    
    public function getCustomData() {
        try {
            // Your custom query
            $stmt = $this->pdo->query("YOUR QUERY");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }
}
```

## Related Classes

- **AdminAuth**: Handles admin authentication and sessions
- **UserManager**: Handles regular user operations
- **admin_config.php**: Contains hardcoded admin credentials

## File Structure

```
admin/
├── includes/
│   ├── AdminAuth.php              # Authentication class
│   ├── AdminManager.php           # This class
│   ├── AdminManager_EXAMPLES.php  # Usage examples
│   ├── admin_config.php           # Admin configuration
│   └── README.md                  # This documentation
├── login.php
├── dashboard.php
└── logout.php
```
