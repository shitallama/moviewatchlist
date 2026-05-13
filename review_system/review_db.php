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
        $query = "SELECT * FROM Review WHERE movie_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$movieId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateReview(int $id, int $rating, string $review): bool {
        $query = "UPDATE Review SET rating = ?, review_text = ?, updated_at = CURRENT_TIMESTAMP WHERE review_id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$rating, $review, $id]);
    }

    public function deleteReview(int $id): bool {
        $query = "DELETE FROM Review WHERE review_id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$id]);
    }
}

$reviewManager = new ReviewManager($pdo);
?>