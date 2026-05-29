<?php
// connect to main database file
include(__DIR__ . "/../includes/db.php");

class ReviewManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function addReview(int $userId, int $movieId, int $rating, string $review): bool {
        $query = "INSERT INTO Review (user_id, movie_id, rating, review_text) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$userId, $movieId, $rating, $review]);
    }

    public function getReviews(int $movieId): array {
        $query = "
            SELECT r.*, u.username
            FROM Review r
            JOIN Users u ON r.user_id = u.user_id
            WHERE r.movie_id = ?
            ORDER BY r.created_at DESC, r.review_id DESC
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$movieId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateReview(int $id, int $userId, int $rating, string $review): bool {
        $query = "UPDATE Review SET rating = ?, review_text = ?, updated_at = CURRENT_TIMESTAMP WHERE review_id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$rating, $review, $id, $userId]);
    }

    public function deleteReview(int $id, int $userId): bool {
        $query = "DELETE FROM Review WHERE review_id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id, $userId]);
    }

    public function hasUserReview(int $userId, int $movieId): bool {
        $query = "SELECT 1 FROM Review WHERE user_id = ? AND movie_id = ? LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $movieId]);
        return (bool) $stmt->fetchColumn();
    }
}

$reviewManager = new ReviewManager($pdo);
?>