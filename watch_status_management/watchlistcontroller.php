<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

session_start();

$basePath = '../';
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

class WatchlistController {
    private $service;

    public function __construct(WatchStatusService $service) {
        $this->service = $service;
    }

    public function getMovies($user_id) {
        return $this->service->getWatchlist($user_id);
    }

    public function toggleWatchStatus(int $movieId, int $currentStatus, $user_id): array {
        try {
            $this->service->toggleWatchStatus($movieId, $user_id, $currentStatus);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

try {
    // Initialize controller with service
    $repository = new WatchStatusRepository($pdo);
    $service = new WatchStatusService($repository);
    $controller = new WatchlistController($service);

    // AJAX endpoint for status toggling
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['current_status'])) {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }
        $user_id = $_SESSION['user_id'];
        $id = (int) $_POST['id'];
        $currentStatus = (int) $_POST['current_status'];
        $result = $controller->toggleWatchStatus($id, $currentStatus, $user_id);
        echo json_encode($result);
        exit;
    }

    // Optional JSON endpoint for consuming the watchlist via AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }
        $user_id = $_SESSION['user_id'];
        $movies = $controller->getMovies($user_id);
        echo json_encode(['success' => true, 'movies' => $movies]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
