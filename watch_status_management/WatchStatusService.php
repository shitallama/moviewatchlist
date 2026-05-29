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
            throw new Exception("Watch status not found or does not belong to this user.");
        }

        $watchStatus->setWatchState($watch_state);
        $watchStatus->setProgressPercent((int) $progress_percent);

        if ($watch_state === 'completed') {
            $watchStatus->setProgressPercent(100);
            $watchStatus->setFinishedAt(date('Y-m-d H:i:s'));
        }

        return $this->repository->update($watchStatus);
    }

    /**
     * Toggle watched/unwatched on a movie (by movie_id)
     */
    public function toggleWatchStatus($movie_id, $user_id, $currentStatus = 0) {
        return $this->repository->toggleStatus($movie_id, $user_id, $currentStatus);
    }

    /**
     * Add movie to watchlist (skips if already present)
     */
    public function addToWatchlist($user_id, $movie_id, $watch_state = 'plan') {
        $existing = $this->repository->findByMovieAndUser($movie_id, $user_id);

        if ($existing) {
            return $existing; // already on the list — do nothing
        }

        $newStatus = new WatchStatus($user_id, $movie_id, $watch_state, 0);
        return $this->repository->save($newStatus);
    }

    /**
     * Remove a watchlist entry by its status_id
     */
    public function removeFromWatchlist($status_id, $user_id) {
        return $this->repository->delete($status_id, $user_id);
    }

    /**
     * Remove a watchlist entry by movie_id (fallback)
     */
    public function removeMovieFromWatchlist($movie_id, $user_id) {
        return $this->repository->deleteMovie($movie_id, $user_id);
    }
}
?>
