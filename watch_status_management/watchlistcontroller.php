<?php
// watch_status_management/watchlistcontroller.php
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

session_start();

class WatchlistController {
    private $service;

    public function __construct(WatchStatusService $service) {
        $this->service = $service;
    }

    public function getMovies($user_id) {
        return $this->service->getWatchlist($user_id);
    }

    public function toggleWatchStatus(int $movieId, int $currentStatus, $user_id): bool {
        try {
            return $this->service->toggleWatchStatus($movieId, $user_id, $currentStatus);
        } catch (Exception $e) {
            error_log('toggleWatchStatus error: ' . $e->getMessage());
            return false;
        }
    }
}

$repository = new WatchStatusRepository($pdo);
$service    = new WatchStatusService($repository);
$controller = new WatchlistController($service);

// ── AJAX: toggle watched status ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['current_status'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }

    $user_id       = $_SESSION['user_id'];
    $id            = (int) $_POST['id'];
    $currentStatus = (int) $_POST['current_status'];
    $success       = $controller->toggleWatchStatus($id, $currentStatus, $user_id);

    echo json_encode(['success' => $success]);
    exit;
}

// ── AJAX: get watchlist as JSON ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $movies  = $controller->getMovies($user_id);
    echo json_encode(['success' => true, 'movies' => $movies]);
    exit;
}
