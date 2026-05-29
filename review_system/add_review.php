<?php
include("review_db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    $movieId = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $review = isset($_POST['review']) ? trim((string) $_POST['review']) : '';
    $isRecommended = isset($_POST['is_recommended']) ? (int) $_POST['is_recommended'] : 0;
    $isRecommended = $isRecommended === 1 ? 1 : 0;

    if ($userId <= 0 || $movieId <= 0 || $rating <= 0 || $review === '') {
        echo "error";
        exit;
    }

    if ($reviewManager->hasUserReview($userId, $movieId)) {
        echo "exists";
        exit;
    }

    if ($reviewManager->addReview($userId, $movieId, $rating, $review, (bool) $isRecommended)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>

