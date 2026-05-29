<?php
// watch_status_management/update_status.php
// Handles the POST from the Edit modal on watchlist.php, then redirects back.

$basePath = '../';
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$user_id  = $_SESSION['user_id'];
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'watchlist.php';

$repository = new WatchStatusRepository($pdo);
$service    = new WatchStatusService($repository);

$allowedStates = ['plan', 'watching', 'completed'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['status_id'])) {
    $status_id       = (int) $_POST['status_id'];
    $watch_state     = (isset($_POST['watch_state']) && in_array($_POST['watch_state'], $allowedStates))
                        ? $_POST['watch_state'] : 'plan';
    $progress_percent = isset($_POST['progress_percent']) ? (int) $_POST['progress_percent'] : 0;

    try {
        $service->updateWatchStatus($status_id, $user_id, $watch_state, $progress_percent);
    } catch (Exception $e) {
        // Log error; redirect back regardless
        error_log('updateWatchStatus error: ' . $e->getMessage());
    }
}

header('Location: ' . $redirect);
exit();
