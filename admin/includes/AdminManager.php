<?php
/**
 * AdminManager Class
 * Handles all admin-related database operations with prepared statements
 */
class AdminManager {
    private $pdo;

    /**
     * Constructor
     * @param PDO $pdo Database connection object
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get total count of users
     * @return int Total number of users
     */
    public function getTotalUsers() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get total count of movies
     * @return int Total number of movies
     */
    public function getTotalMovies() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Movies");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get total count of genres
     * @return int Total number of genres
     */
    public function getTotalGenres() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM genres");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get total count of reviews
     * @return int Total number of reviews
     */
    public function getTotalReviews() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Review");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get all users with pagination
     * @param int $limit Limit results (optional)
     * @return array Array of user data
     */
    public function getAllUsers($limit = null) {
        try {
            $query = "SELECT user_id, username, email, created_at FROM Users ORDER BY created_at DESC";
            if ($limit !== null) {
                $query .= " LIMIT :limit";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->query($query);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all movies with rating information
     * @param int $limit Limit results (optional)
     * @return array Array of movie data with ratings
     */
    public function getAllMovies($limit = null) {
        try {
            $query = "
                SELECT m.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                FROM Movies m 
                LEFT JOIN Review r ON m.movie_id = r.movie_id 
                GROUP BY m.movie_id 
                ORDER BY m.movie_id DESC
            ";
            if ($limit !== null) {
                $query .= " LIMIT :limit";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->query($query);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all distinct genres
     * @return array Array of genre names
     */
    public function getAllGenres() {
        try {
            $stmt = $this->pdo->query("
                SELECT DISTINCT genre FROM Movies 
                WHERE genre IS NOT NULL AND genre != '' 
                ORDER BY genre
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all reviews with user and movie info
     * @param int|null $limit Limit results (optional)
     * @return array Array of review data
     */
    public function getAllReviews($limit = null) {
        try {
            $query = "
                SELECT r.review_id, r.rating, r.review_text, r.created_at,
                       u.username, m.title
                FROM Review r
                JOIN Users u ON r.user_id = u.user_id
                JOIN Movies m ON r.movie_id = m.movie_id
                ORDER BY r.created_at DESC
            ";
            if ($limit !== null) {
                $query .= " LIMIT :limit";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->query($query);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get top rated movies
     * @param int $limit Number of movies to return
     * @return array Array of top-rated movies
     */
    public function getTopRatedMovies($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                FROM Movies m 
                LEFT JOIN Review r ON m.movie_id = r.movie_id 
                GROUP BY m.movie_id 
                ORDER BY avg_rating DESC, review_count DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get recent users
     * @param int $limit Number of users to return
     * @return array Array of recent users
     */
    public function getRecentUsers($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT user_id, username, email, created_at 
                FROM Users 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get recent movies
     * @param int $limit Number of movies to return
     * @return array Array of recent movies
     */
    public function getRecentMovies($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM Movies 
                ORDER BY movie_id DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get user by ID with full details
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public function getUserById($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT user_id, username, email, is_active, is_admin, created_at 
                FROM Users 
                WHERE user_id = :user_id 
                LIMIT 1
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Check if username or email is already in use by another user
     * @param string $username Username
     * @param string $email Email
     * @param int $userId Exclude this user ID from the check
     * @return array|null Existing user data or null
     */
    public function checkUserExistsExclude($username, $email, $userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT user_id FROM Users 
                WHERE (username = :username OR email = :email) AND user_id != :user_id 
                LIMIT 1
            ");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'user_id' => (int)$userId,
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Update a user profile from the admin panel
     * @param int $userId User ID
     * @param string $username Username
     * @param string $email Email address
     * @param int $isActive Whether the account is active
     * @param int $isAdmin Whether the user has admin privileges
     * @return bool True if successful, false otherwise
     */
    public function updateUser($userId, $username, $email, $isActive, $isAdmin) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE Users 
                SET username = :username, email = :email, is_active = :is_active, is_admin = :is_admin 
                WHERE user_id = :user_id
            ");
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'is_active' => (int)$isActive,
                'is_admin' => (int)$isAdmin,
                'user_id' => (int)$userId,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Permanently delete a user
     * @param int $userId User ID
     * @return bool True if successful, false otherwise
     */
    public function deleteUser($userId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Users WHERE user_id = :user_id");
            return $stmt->execute(['user_id' => (int)$userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get movie by ID with rating information
     * @param int $movieId Movie ID
     * @return array|null Movie data or null if not found
     */
    public function getMovieById($movieId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                FROM Movies m 
                LEFT JOIN Review r ON m.movie_id = r.movie_id 
                WHERE m.movie_id = :movie_id 
                GROUP BY m.movie_id
            ");
            $stmt->execute(['movie_id' => $movieId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get dashboard statistics
     * @return array Associative array with all statistics
     */
    public function getDashboardStats() {
        return [
            'total_users' => $this->getTotalUsers(),
            'total_movies' => $this->getTotalMovies(),
            'total_genres' => $this->getTotalGenres(),
            'total_reviews' => $this->getTotalReviews(),
        ];
    }

    /**
     * Get all dashboard data
     * @return array Complete dashboard data
     */
    public function getDashboardData() {
        return [
            'stats' => $this->getDashboardStats(),
            'all_users' => $this->getAllUsers(),
            'all_movies' => $this->getAllMovies(),
            'all_genres' => $this->getAllGenres(),
            'top_movies' => $this->getTopRatedMovies(),
            'recent_users' => $this->getRecentUsers(),
            'recent_movies' => $this->getRecentMovies(),
        ];
    }

    /**
     * Search users by username or email
     * @param string $searchTerm Search term
     * @return array Array of matching users
     */
    public function searchUsers($searchTerm) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT user_id, username, email, created_at 
                FROM Users 
                WHERE username LIKE :search OR email LIKE :search 
                ORDER BY created_at DESC
            ");
            $searchPattern = '%' . $searchTerm . '%';
            $stmt->execute(['search' => $searchPattern]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Search movies by title
     * @param string $searchTerm Search term
     * @return array Array of matching movies with ratings
     */
    public function searchMovies($searchTerm) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                FROM Movies m 
                LEFT JOIN Review r ON m.movie_id = r.movie_id 
                WHERE m.title LIKE :search 
                GROUP BY m.movie_id 
                ORDER BY m.movie_id DESC
            ");
            $searchPattern = '%' . $searchTerm . '%';
            $stmt->execute(['search' => $searchPattern]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get movies by genre
     * @param string $genre Genre name
     * @return array Array of movies in that genre
     */
    public function getMoviesByGenre($genre) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                FROM Movies m 
                LEFT JOIN Review r ON m.movie_id = r.movie_id 
                WHERE m.genre = :genre 
                GROUP BY m.movie_id 
                ORDER BY avg_rating DESC
            ");
            $stmt->execute(['genre' => $genre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get movies with minimum rating
     * @param float $minRating Minimum average rating
     * @return array Array of movies with minimum rating
     */
    public function getMoviesByMinimumRating($minRating) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                FROM Movies m 
                LEFT JOIN Review r ON m.movie_id = r.movie_id 
                GROUP BY m.movie_id 
                HAVING AVG(r.rating) >= :min_rating OR AVG(r.rating) IS NULL
                ORDER BY avg_rating DESC
            ");
            $stmt->execute(['min_rating' => (float)$minRating]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get unrated movies
     * @return array Array of movies with no ratings
     */
    public function getUnratedMovies() {
        try {
            $stmt = $this->pdo->query("
                SELECT m.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                FROM Movies m 
                LEFT JOIN Review r ON m.movie_id = r.movie_id 
                GROUP BY m.movie_id 
                HAVING AVG(r.rating) IS NULL
                ORDER BY m.movie_id DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Deactivate a user account
     * @param int $userId User ID to deactivate
     * @return bool True if successful, false otherwise
     */
    public function deactivateUser($userId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE Users 
                SET is_active = 0 
                WHERE user_id = :user_id
            ");
            return $stmt->execute(['user_id' => (int)$userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Reactivate a user account
     * @param int $userId User ID to reactivate
     * @return bool True if successful, false otherwise
     */
    public function reactivateUser($userId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE Users 
                SET is_active = 1 
                WHERE user_id = :user_id
            ");
            return $stmt->execute(['user_id' => (int)$userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a review by ID
     * @param int $reviewId Review ID to delete
     * @return bool True if successful, false otherwise
     */
    public function deleteReview($reviewId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM Review 
                WHERE review_id = :review_id
            ");
            return $stmt->execute(['review_id' => (int)$reviewId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all reviews with full user and movie info for moderation
     * @return array Array of reviews for moderation
     */
    public function getAllReviewsForModeration() {
        try {
            $stmt = $this->pdo->query("
                SELECT r.review_id, r.rating, r.review_text, r.is_recommended, r.created_at,
                       u.user_id, u.username, m.movie_id, m.title
                FROM Review r
                JOIN Users u ON r.user_id = u.user_id
                JOIN Movies m ON r.movie_id = m.movie_id
                ORDER BY r.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all active users with status
     * @return array Array of users with their active status
     */
    public function getAllUsersWithStatus() {
        try {
            $stmt = $this->pdo->query("
                SELECT user_id, username, email, is_active, is_admin, created_at 
                FROM Users 
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            return [];
        }
    }
}
