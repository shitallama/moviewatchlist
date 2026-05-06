<?php
include("review_db.php");

$movie_id = $_GET['movie_id'];

$reviews = getReviews($movie_id);

foreach ($reviews as $row) {
    echo "<div class='review'>";
    echo "<strong>Rating: " . $row['rating'] . " ⭐</strong>";
    echo "<p>" . htmlspecialchars($row['review_text']) . "</p>";
    echo "<small>" . $row['created_at'] . "</small>";
    echo "<button onclick=\"editReview(" . $row['review_id'] . ", " . $row['rating'] . ", '" . addslashes($row['review_text']) . "')\">Edit</button>";
    echo "<button onclick=\"deleteReview(" . $row['review_id'] . ")\">Delete</button>";
    echo "</div>";
}
?>