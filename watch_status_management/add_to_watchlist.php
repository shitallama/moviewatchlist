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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'watchlist.php';
    $status = isset($_POST['status']) && in_array($_POST['status'], ['plan', 'completed']) ? $_POST['status'] : 'plan';
    $watch_date = !empty($_POST['watch_date']) ? $_POST['watch_date'] : null;
    $movie_id = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';

    if ($movie_id <= 0 && $title === '') {
        header('Location: ' . $redirect);
        exit();
    }

    $repository = new WatchStatusRepository($pdo);
    $service    = new WatchStatusService($repository);

    try {
        if ($movie_id > 0) {
            $newMovieId = $movie_id;
        } else {
            $findQuery = $pdo->prepare('SELECT movie_id FROM Movies WHERE user_id = ? AND title = ? LIMIT 1');
            $findQuery->execute([$user_id, $title]);
            $existingMovie = $findQuery->fetch(PDO::FETCH_ASSOC);

            if ($existingMovie) {
                $newMovieId = (int) $existingMovie['movie_id'];
            } else {
                $insertQuery = $pdo->prepare(
                    'INSERT INTO Movies (title, genre, release_date, rating, watched, watch_date, user_notes, user_id) VALUES (?, ?, NULL, NULL, ?, ?, NULL, ?)'
                );
                $insertQuery->execute([
                    $title,
                    'Unknown',
                    $status === 'completed' ? 1 : 0,
                    $watch_date,
                    $user_id
                ]);
                $newMovieId = (int) $pdo->lastInsertId();
            }
        }

        $existingStatus = $repository->findByMovieAndUser($newMovieId, $user_id);

        if ($existingStatus) {
            if ($status === 'completed' && $existingStatus->getWatchState() !== 'completed') {
                $existingStatus->setWatchState('completed');
                $existingStatus->setProgressPercent(100);
                $repository->update($existingStatus);
            }
        } else {
            $service->addToWatchlist($user_id, $newMovieId, $status);
        }

        if ($status === 'completed') {
            $updateQuery = $pdo->prepare('UPDATE Movies SET watched = 1, watch_date = ? WHERE movie_id = ? AND user_id = ?');
            $updateQuery->execute([$watch_date ?: date('Y-m-d'), $newMovieId, $user_id]);
        } else {
            $updateQuery = $pdo->prepare('UPDATE Movies SET watched = 0, watch_date = ? WHERE movie_id = ? AND user_id = ?');
            $updateQuery->execute([$watch_date, $newMovieId, $user_id]);
        }
    } catch (Exception $e) {
        error_log('addToWatchlist error: ' . $e->getMessage());
    }

    header('Location: ' . $redirect);
    exit();
}

header('Location: watchlist.php');
exit();
