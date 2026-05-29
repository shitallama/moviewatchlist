<?php
include("review_db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($userId <= 0 || $id <= 0) {
        echo "error";
        exit;
    }

    if ($reviewManager->deleteReview($id, $userId)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>