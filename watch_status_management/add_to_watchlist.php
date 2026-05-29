<?php
// watch_status_management/add_to_watchlist.php
session_start();
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'])) {
    $user_id  = $_SESSION['user_id'];
    $movie_id = (int) $_POST['movie_id'];
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'watchlist.php';

    $repository = new WatchStatusRepository($pdo);
    $service    = new WatchStatusService($repository);

    try {
        $service->addToWatchlist($user_id, $movie_id);
    } catch (Exception $e) {
        error_log('addToWatchlist error: ' . $e->getMessage());
    }

    header('Location: ' . $redirect);
    exit();
}

header('Location: watchlist.php');
exit();
