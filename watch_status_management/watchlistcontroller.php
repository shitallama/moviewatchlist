<?php
// watch status management/watchlistcontroller.php
$basePath = '../';
require_once '../includes/db.php';

class WatchlistController {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getMovies($user_id) {
        $stmt = $this->pdo->prepare(
            "SELECT m.movie_id, m.title, m.watch_date, ws.watch_state, ws.progress_percent
             FROM WatchStatus ws
             JOIN Movies m ON ws.movie_id = m.movie_id
             WHERE ws.user_id = ?
             ORDER BY CASE ws.watch_state
                 WHEN 'plan' THEN 1
                 WHEN 'watching' THEN 2
                 WHEN 'completed' THEN 3
                 ELSE 4
             END, m.title ASC"
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleWatchStatus(int $movieId, int $currentStatus, $user_id): bool {
        $newState = $currentStatus ? 'plan' : 'completed';
        $progress = $newState === 'completed' ? 100 : 0;
        $finishedAt = $newState === 'completed' ? date('Y-m-d H:i:s') : null;
        $watched = $newState === 'completed' ? 1 : 0;
        $watchDate = $watched ? date('Y-m-d') : null;

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "UPDATE WatchStatus
                 SET watch_state = ?, progress_percent = ?, finished_at = ?
                 WHERE movie_id = ? AND user_id = ?"
            );
            $statusUpdated = $stmt->execute([$newState, $progress, $finishedAt, $movieId, $user_id]);

            $stmt = $this->pdo->prepare(
                "UPDATE Movies
                 SET watched = ?, watch_date = ?
                 WHERE movie_id = ? AND user_id = ?"
            );
            $movieUpdated = $stmt->execute([$watched, $watchDate, $movieId, $user_id]);

            if ($statusUpdated && $movieUpdated) {
                $this->pdo->commit();
                return true;
            }

            $this->pdo->rollBack();
            return false;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }
}

$controller = new WatchlistController($pdo);

// AJAX endpoint for status toggling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['current_status'])) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $id = (int) $_POST['id'];
    $currentStatus = (int) $_POST['current_status'];
    $success = $controller->toggleWatchStatus($id, $currentStatus, $user_id);

    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit;
}

// Optional JSON endpoint for consuming the watchlist via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $movies = $controller->getMovies($user_id);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'movies' => $movies]);
    exit;
}
