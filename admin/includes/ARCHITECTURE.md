# Admin Panel - Object-Oriented Architecture

## Overview

The admin panel now follows an object-oriented design pattern similar to the UserManager class. This provides a clean, maintainable, and extensible architecture.

## Class Structure

### 1. **AdminAuth Class** (`admin/includes/AdminAuth.php`)
Handles admin authentication and session management

```php
AdminAuth::authenticate($username, $password)      // Verify credentials
AdminAuth::setSession($username)                   // Create admin session
AdminAuth::isLoggedIn()                            // Check if logged in
AdminAuth::getUsername()                           // Get current admin username
AdminAuth::logout()                                // End session
AdminAuth::requireLogin()                          // Redirect if not logged in
```

### 2. **AdminManager Class** (`admin/includes/AdminManager.php`)
Handles all admin dashboard data operations

```php
// Initialize
$adminManager = new AdminManager($pdo);

// Statistics
$adminManager->getTotalUsers()
$adminManager->getTotalMovies()
$adminManager->getTotalGenres()
$adminManager->getTotalReviews()
$adminManager->getDashboardStats()
$adminManager->getDashboardData()

// Data Retrieval
$adminManager->getAllUsers($limit)
$adminManager->getAllMovies($limit)
$adminManager->getAllGenres()
$adminManager->getTopRatedMovies($limit)
$adminManager->getRecentUsers($limit)
$adminManager->getRecentMovies($limit)
$adminManager->getUserById($userId)
$adminManager->getMovieById($movieId)

// Search & Filter
$adminManager->searchUsers($searchTerm)
$adminManager->searchMovies($searchTerm)
$adminManager->getMoviesByGenre($genre)
$adminManager->getMoviesByMinimumRating($minRating)
$adminManager->getUnratedMovies()
```

## File Organization

```
admin/
├── includes/
│   ├── AdminAuth.php                  # Authentication
│   ├── AdminManager.php               # Data Management (NEW)
│   ├── AdminManager_README.md         # Detailed Documentation (NEW)
│   ├── AdminManager_EXAMPLES.php      # Usage Examples (NEW)
│   └── admin_config.php               # Configuration
├── login.php                          # Admin login page
├── dashboard.php                      # Admin dashboard (Updated)
├── logout.php                         # Admin logout
├── index.php                          # Directory redirect
├── README.md                          # Admin documentation
├── QUICKSTART.php                     # Quick start guide
└── .htaccess                          # Security rules
```

## How It Works

### Before (Direct Database Queries)

```php
// Old way - direct database queries in dashboard.php
$stmt = $pdo->query("SELECT COUNT(*) as count FROM Users");
$totalUsers = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM Movies");
$totalMovies = $stmt->fetch()['count'];
```

### After (Object-Oriented)

```php
// New way - using AdminManager class
$adminManager = new AdminManager($pdo);
$totalUsers = $adminManager->getTotalUsers();
$totalMovies = $adminManager->getTotalMovies();
```

## Benefits

### 1. **Maintainability**
- All database logic centralized in one class
- Easy to update queries in one place
- Consistent error handling

### 2. **Reusability**
- Use AdminManager in any admin page
- Create new admin pages easily
- Share logic across multiple pages

### 3. **Testability**
- Easier to unit test
- Mock AdminManager for testing
- Verify data without database

### 4. **Scalability**
- Add new methods easily
- Extend AdminManager for custom functionality
- Organize code logically

### 5. **Security**
- All queries use prepared statements
- SQL injection prevention
- Error handling prevents data exposure

## Usage Pattern

### Using AdminManager in Dashboard

```php
<?php
require_once '../admin/includes/AdminManager.php';
require_once '../includes/db.php';

// Initialize
$adminManager = new AdminManager($pdo);

// Get data
$stats = $adminManager->getDashboardStats();
$users = $adminManager->getAllUsers();
$movies = $adminManager->getAllMovies();

// Use in HTML
echo "Total Users: " . $stats['total_users'];
?>
```

### Creating a New Admin Page

```php
<?php
require_once '../admin/includes/AdminAuth.php';
require_once '../admin/includes/AdminManager.php';
require_once '../includes/db.php';

// Verify admin is logged in
AdminAuth::requireLogin();

// Initialize manager
$adminManager = new AdminManager($pdo);

// Get specific data
$topMovies = $adminManager->getTopRatedMovies(10);
$actionMovies = $adminManager->getMoviesByGenre('Action');

// Build your page...
?>
```

## Comparison with UserManager

| Aspect | UserManager | AdminManager |
|--------|------------|-------------|
| Purpose | User operations | Admin dashboard data |
| Location | `includes/UserManager.php` | `admin/includes/AdminManager.php` |
| Methods | ~8 methods | ~15 methods |
| PDO Usage | Prepared statements | Prepared statements |
| Error Handling | Returns false/null | Returns empty/null/0 |
| Access Level | Regular users | Admin only |

## Method Categories

### AdminManager Methods by Category

**Statistics (4 methods)**
- Get counts of users, movies, genres, reviews

**Data Retrieval (8 methods)**
- Get all data, recent data, top data

**Search & Filter (5 methods)**
- Search users/movies, filter by genre/rating

## Performance Optimization

### Current Approach
- Methods query database directly
- Each method is independent
- Good for flexibility

### Future Optimization Ideas
1. Add caching for statistics
2. Batch queries for better performance
3. Add pagination helpers
4. Implement query result caching

## Security Features

1. **Prepared Statements**
   ```php
   $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE username = :username");
   $stmt->execute(['username' => $username]);
   ```

2. **Error Suppression**
   ```php
   try {
       // Query
   } catch (PDOException $e) {
       return []; // Safe return
   }
   ```

3. **Authentication Integration**
   ```php
   AdminAuth::requireLogin(); // Before using AdminManager
   ```

## Integration Examples

### Example 1: List Top 10 Rated Movies

```php
$adminManager = new AdminManager($pdo);
$topMovies = $adminManager->getTopRatedMovies(10);

foreach ($topMovies as $movie) {
    echo $movie['title'] . " - " . round($movie['avg_rating'], 1) . " stars";
}
```

### Example 2: Search and Display

```php
$adminManager = new AdminManager($pdo);
$searchTerm = $_GET['search'] ?? '';

if ($searchTerm) {
    $results = $adminManager->searchMovies($searchTerm);
    foreach ($results as $movie) {
        echo $movie['title'];
    }
}
```

### Example 3: Filter by Genre

```php
$adminManager = new AdminManager($pdo);
$genre = $_GET['genre'] ?? 'Action';
$movies = $adminManager->getMoviesByGenre($genre);

foreach ($movies as $movie) {
    echo $movie['title'] . " (" . $movie['genre'] . ")";
}
```

## Documentation Files

- **AdminManager_README.md** - Complete class documentation
- **AdminManager_EXAMPLES.php** - Code examples
- **admin/README.md** - Admin panel overview
- **admin/QUICKSTART.php** - Quick reference guide

## Migration Notes

The dashboard has been updated to use AdminManager:
- ✅ All direct database queries replaced
- ✅ Error handling implemented
- ✅ Same functionality preserved
- ✅ CSS already separated to external file

## Next Steps for Expanding Admin Panel

1. **User Management Page**
   ```php
   $users = $adminManager->getAllUsers();
   // Display with edit/delete options
   ```

2. **Movie Management Page**
   ```php
   $movies = $adminManager->getAllMovies();
   // Display with edit/delete options
   ```

3. **Analytics Page**
   ```php
   $stats = $adminManager->getDashboardStats();
   $topMovies = $adminManager->getTopRatedMovies(5);
   // Display charts and graphs
   ```

4. **Advanced Search Page**
   ```php
   $results = $adminManager->searchMovies($_GET['q']);
   $filtered = $adminManager->getMoviesByGenre($_GET['genre']);
   ```

## Troubleshooting

### Issue: "AdminManager class not found"
**Solution**: Ensure the require statement is correct
```php
require_once 'admin/includes/AdminManager.php';
```

### Issue: "Property $pdo is null"
**Solution**: Ensure PDO is initialized before creating AdminManager
```php
require_once 'includes/db.php'; // Must come first
$adminManager = new AdminManager($pdo);
```

### Issue: No data displayed
**Solution**: Check database connection and ensure tables exist
```php
$totalUsers = $adminManager->getTotalUsers();
if ($totalUsers === 0) {
    echo "Check database connection";
}
```

## Summary

The AdminManager class provides a clean, maintainable, and extensible way to handle admin dashboard data operations. By following object-oriented principles similar to UserManager, the admin panel is now more professional and easier to maintain.

For detailed method documentation, see **AdminManager_README.md**.
