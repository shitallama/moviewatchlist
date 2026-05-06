<?php
include("review_db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];   // later replace with session
    $movie_id = $_POST['movie_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    if (addReview($user_id, $movie_id, $rating, $review)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>

