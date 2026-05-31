<?php
// watch_status_management/remove_watchlist.php
session_start();
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_id'])) {
    $user_id   = $_SESSION['user_id'];
    $status_id = (int) $_POST['status_id'];

    $repository = new WatchStatusRepository($pdo);
    $service    = new WatchStatusService($repository);

    try {
        $watchStatus = $repository->findById($status_id, $user_id);
        if ($watchStatus) {
            $movie_id = $watchStatus->getMovieId();

            // Remove from WatchStatus table
            $service->removeFromWatchlist($status_id, $user_id);

            // Reset watched flag on the Movies table
            $stmt = $pdo->prepare("UPDATE Movies SET watched = 0, watch_date = NULL WHERE movie_id = ? AND user_id = ?");
            $stmt->execute([$movie_id, $user_id]);
        }
    } catch (Exception $e) {
        error_log('removeFromWatchlist error: ' . $e->getMessage());
    }
}

header('Location: watchlist.php');
exit();
