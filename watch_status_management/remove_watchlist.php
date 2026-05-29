<?php
session_start();
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_id'])) {
    $user_id = $_SESSION['user_id'];
    $status_id = (int) $_POST['status_id'];

    // Initialize service
    $repository = new WatchStatusRepository($pdo);
    $service = new WatchStatusService($repository);

    try {
        $watchStatus = $repository->findById($status_id, $user_id);
        if ($watchStatus) {
            $movie_id = $watchStatus->getMovieId();
            $service->removeFromWatchlist($status_id, $user_id);

            $updateStatement = $pdo->prepare("UPDATE Movies SET watched = 0, watch_date = NULL WHERE movie_id = ? AND user_id = ?");
            $updateStatement->execute([$movie_id, $user_id]);
        }
    } catch (Exception $e) {
        // Log error if needed
    }
}

header('Location: watchlist.php');
exit();
