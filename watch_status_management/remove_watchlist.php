<?php
session_start();
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = $_SESSION['user_id'];
    $movie_id = (int) $_POST['id'];

    // Initialize service
    $repository = new WatchStatusRepository($pdo);
    $service = new WatchStatusService($repository);

    try {
        // Find the watch status by movie and user
        $watchStatus = $repository->findByMovieAndUser($movie_id, $user_id);
        
        if ($watchStatus) {
            $service->removeFromWatchlist($watchStatus->getStatusId(), $user_id);
        }
    } catch (Exception $e) {
        // Log error if needed
    }
}

header('Location: watchlist.php');
exit();
