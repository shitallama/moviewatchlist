<?php
include("review_db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $review = isset($_POST['review']) ? trim((string) $_POST['review']) : '';

    if ($userId <= 0 || $id <= 0 || $rating <= 0 || $review === '') {
        echo "error";
        exit;
    }

    if ($reviewManager->updateReview($id, $userId, $rating, $review)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>