<?php
// connect to main database file
include(__DIR__ . "/../includes/db.php");

// ADD REVIEW
function addReview($user_id, $movie_id, $rating, $review) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO Review (user_id, movie_id, rating, review_text) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$user_id, $movie_id, $rating, $review]);
}

// GET REVIEWS
function getReviews($movie_id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM Review WHERE movie_id = ?");
    $stmt->execute([$movie_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// UPDATE REVIEW
function updateReview($id, $rating, $review) {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE Review SET rating = ?, review_text = ?, updated_at = CURRENT_TIMESTAMP WHERE review_id = ?");
    return $stmt->execute([$rating, $review, $id]);
}

// DELETE REVIEW
function deleteReview($id) {
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM Review WHERE review_id = ?");
    return $stmt->execute([$id]);
}
?>