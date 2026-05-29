<?php
include("review_db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUserId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$movie_id = isset($_GET['movie_id']) ? (int) $_GET['movie_id'] : 0;

if ($movie_id <= 0) {
    exit;
}

$reviews = $reviewManager->getReviews($movie_id);

foreach ($reviews as $row) {
    $reviewTextJson = json_encode($row['review_text'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $isOwner = $currentUserId > 0 && (int) $row['user_id'] === $currentUserId;
    $username = isset($row['username']) ? $row['username'] : 'Unknown user';
    $isRecommended = isset($row['is_recommended']) && (int) $row['is_recommended'] === 1;
    echo "<div class='review'>";
    echo "<strong>Rating: " . $row['rating'] . " ⭐</strong>";
    echo "<div class='review-meta'>";
    echo "<span class='review-user'>" . htmlspecialchars($username) . "</span>";
    if ($isRecommended) {
        echo "<span class='review-recommendation'>Recommended</span>";
    }
    echo "</div>";
    echo "<p>" . htmlspecialchars($row['review_text']) . "</p>";
    echo "<small>" . $row['created_at'] . "</small>";
    if ($isOwner) {
        echo "<div class='review-actions'>";
        echo "<button class='review-edit' onclick='editReview(" . $row['review_id'] . ", " . $row['rating'] . ", " . $reviewTextJson . ", " . ($isRecommended ? 1 : 0) . ")'>Edit</button>";
        echo "<button class='review-delete' onclick='deleteReview(" . $row['review_id'] . ")'>Delete</button>";
        echo "</div>";
    }
    echo "</div>";
}
?>