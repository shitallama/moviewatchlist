<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = $_SESSION['user_id'];
    $movie_id = (int) $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM WatchStatus WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$user_id, $movie_id]);
}

header('Location: watchlist.php');
exit();
