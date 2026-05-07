<?php
include("review_db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    if (updateReview($id, $rating, $review)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>