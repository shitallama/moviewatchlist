<?php
/**
 * WatchStatusService
 * Business logic layer for watch status operations
 */
require_once __DIR__ . '/repositories/WatchStatusRepository.php';

class WatchStatusService {
    private $repository;

    public function __construct(WatchStatusRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Get user's complete watchlist with movie details
     */
    public function getWatchlist($user_id) {
        return $this->repository->getWatchlistForUser($user_id);
    }

    /**
     * Update watch status with validation
     */
    public function updateWatchStatus($status_id, $user_id, $watch_state, $progress_percent) {
        $allowedStates = ['plan', 'watching', 'completed'];
        
        if (!in_array($watch_state, $allowedStates)) {
            throw new InvalidArgumentException("Invalid watch state: $watch_state");
        }

        $watchStatus = $this->repository->findById($status_id, $user_id);
        
        if (!$watchStatus) {
            throw new Exception("Watch status not found");
        }

        $watchStatus->setWatchState($watch_state);
        $watchStatus->setProgressPercent($progress_percent);

        if ($watch_state === 'completed') {
            $watchStatus->setProgressPercent(100);
            $watchStatus->setFinishedAt(date('Y-m-d H:i:s'));
        }

        return $this->repository->update($watchStatus);
    }

    /**
     * Toggle watch status
     */
    public function toggleWatchStatus($movie_id, $user_id, $currentStatus = 0) {
        return $this->repository->toggleStatus($movie_id, $user_id, $currentStatus);
    }

    /**
     * Add movie to watchlist
     */
    public function addToWatchlist($user_id, $movie_id, $watch_state = 'plan') {
        $existing = $this->repository->findByMovieAndUser($movie_id, $user_id);
        
        if ($existing) {
            return $existing;
        }

        $newStatus = new WatchStatus($user_id, $movie_id, $watch_state, 0);
        return $this->repository->save($newStatus);
    }

    /**
     * Remove from watchlist
     */
    public function removeFromWatchlist($status_id, $user_id) {
        return $this->repository->delete($status_id, $user_id);
    }

    /**
     * Delete a movie from the user's watchlist
     */
    public function removeMovieFromWatchlist($movie_id, $user_id) {
        return $this->repository->deleteMovie($movie_id, $user_id);
    }
}
?>

