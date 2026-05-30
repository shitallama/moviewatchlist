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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_id        = isset($_POST['status_id']) ? (int) $_POST['status_id'] : 0;
    $movie_id         = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
    $watch_state      = (isset($_POST['watch_state']) && in_array($_POST['watch_state'], $allowedStates))
                        ? $_POST['watch_state'] : 'plan';
    $progress_percent = isset($_POST['progress_percent']) ? (int) $_POST['progress_percent'] : 0;

    try {
        if ($status_id > 0) {
            $service->updateWatchStatus($status_id, $user_id, $watch_state, $progress_percent);
        } elseif ($movie_id > 0) {
            $existing = $repository->findByMovieAndUser($movie_id, $user_id);
            if ($existing) {
                $service->updateWatchStatus($existing->getStatusId(), $user_id, $watch_state, $progress_percent);
                $status_id = $existing->getStatusId();
            } else {
                $newStatus = new WatchStatus($user_id, $movie_id, $watch_state, $progress_percent);
                if ($watch_state === 'completed') {
                    $newStatus->setFinishedAt(date('Y-m-d H:i:s'));
                    $newStatus->setProgressPercent(100);
                }
                $repository->save($newStatus);
            }
        }

        if ($movie_id <= 0 && $status_id > 0) {
            $status = $repository->findById($status_id, $user_id);
            if ($status) {
                $movie_id = $status->getMovieId();
            }
        }

        if ($movie_id > 0) {
            $isCompleted = $watch_state === 'completed';
            $stmt = $pdo->prepare("UPDATE Movies SET watched = ?, watch_date = ? WHERE movie_id = ? AND user_id = ?");
            $stmt->execute([
                $isCompleted ? 1 : 0,
                $isCompleted ? date('Y-m-d') : null,
                $movie_id,
                $user_id
            ]);
        }
    } catch (Exception $e) {
        // Log error; redirect back regardless
        error_log('updateWatchStatus error: ' . $e->getMessage());
    }
}

header('Location: ' . $redirect);
exit();
