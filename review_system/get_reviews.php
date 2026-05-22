<?php
include("review_db.php");

$movie_id = $_GET['movie_id'];

$reviews = $reviewManager->getReviews($movie_id);

foreach ($reviews as $row) {
    $reviewTextJson = json_encode($row['review_text'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    echo "<div class='review'>";
    echo "<strong>Rating: " . $row['rating'] . " ⭐</strong>";
    echo "<p>" . htmlspecialchars($row['review_text']) . "</p>";
    echo "<small>" . $row['created_at'] . "</small>";
    echo "<button onclick='editReview(" . $row['review_id'] . ", " . $row['rating'] . ", " . $reviewTextJson . ")'>Edit</button>";
    echo "<button onclick='deleteReview(" . $row['review_id'] . ")'>Delete</button>";
    echo "</div>";
}
?>