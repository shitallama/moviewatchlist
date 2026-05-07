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
        $stmt = $this->pdo->prepare("SELECT movie_id, title, watched, watch_date FROM Movies WHERE user_id = ? ORDER BY watched ASC, title ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleWatchStatus(int $id, int $currentStatus, $user_id): bool {
        $newStatus = $currentStatus ? 0 : 1;
        $watchDate = $newStatus ? date('Y-m-d') : null;

        $stmt = $this->pdo->prepare("UPDATE Movies SET watched = ?, watch_date = ? WHERE movie_id = ? AND user_id = ?");
        return $stmt->execute([$newStatus, $watchDate, $id, $user_id]);
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
