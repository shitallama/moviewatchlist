<?php
/**
 * WatchStatusRepository
 * Handles all database operations for watch status
 */
require_once __DIR__ . '/../models/WatchStatus.php';

class WatchStatusRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Find a watch status by ID
     */
    public function findById($status_id, $user_id) {
        $query = "SELECT * FROM WatchStatus 
                  WHERE status_id = ? AND user_id = ?";
        $preparedStatement = $this->pdo->prepare($query);
        $preparedStatement->execute([$status_id, $user_id]);
        $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new WatchStatus(
                $result['user_id'],
                $result['movie_id'],
                $result['watch_state'],
                $result['progress_percent'],
                $result['status_id'],
                $result['finished_at'],
                $result['added_to_list_at'] ?? null
            );
        }
        return null;
    }

    /**
     * Find watch status by movie and user
     */
    public function findByMovieAndUser($movie_id, $user_id) {
        $query = "SELECT * FROM WatchStatus 
                  WHERE movie_id = ? AND user_id = ?";
        $preparedStatement = $this->pdo->prepare($query);
        $preparedStatement->execute([$movie_id, $user_id]);
        $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new WatchStatus(
                $result['user_id'],
                $result['movie_id'],
                $result['watch_state'],
                $result['progress_percent'],
                $result['status_id'],
                $result['finished_at'],
                $result['added_to_list_at'] ?? null
            );
        }
        return null;
    }

    /**
     * Get all watch status for a user with movie details
     */
    public function getWatchlistForUser($user_id) {
        $query = "SELECT m.movie_id, m.title, m.watch_date, m.watched,
                         ws.status_id, ws.watch_state, ws.progress_percent
                  FROM WatchStatus ws
                  JOIN Movies m ON m.movie_id = ws.movie_id AND m.user_id = ?
                  WHERE ws.user_id = ?
                  ORDER BY CASE ws.watch_state
                      WHEN 'plan' THEN 1
                      WHEN 'watching' THEN 2
                      WHEN 'completed' THEN 3
                      ELSE 4
                  END, m.title ASC";
        $preparedStatement = $this->pdo->prepare($query);
        $preparedStatement->execute([$user_id, $user_id]);
        return $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save a new watch status
     */
    public function save(WatchStatus $watchStatus) {
        $query = "INSERT INTO WatchStatus (user_id, movie_id, watch_state, progress_percent, finished_at) VALUES (?, ?, ?, ?, ?)";
        $preparedStatement = $this->pdo->prepare($query);
        
        $result = $preparedStatement->execute([
            $watchStatus->getUserId(),
            $watchStatus->getMovieId(),
            $watchStatus->getWatchState(),
            $watchStatus->getProgressPercent(),
            $watchStatus->getFinishedAt(),
        ]);

        if ($result) {
            $watchStatus = $this->findByMovieAndUser(
                $watchStatus->getMovieId(),
                $watchStatus->getUserId()
            );
            return $watchStatus;
        }
        return false;
    }

    /**
     * Update an existing watch status
     */
    public function update(WatchStatus $watchStatus) {
        $query = "UPDATE WatchStatus 
                  SET watch_state = ?, progress_percent = ?, finished_at = ?
                  WHERE status_id = ? AND user_id = ?";
        $preparedStatement = $this->pdo->prepare($query);

        return $preparedStatement->execute([
            $watchStatus->getWatchState(),
            $watchStatus->getProgressPercent(),
            $watchStatus->getFinishedAt(),
            $watchStatus->getStatusId(),
            $watchStatus->getUserId(),
        ]);
    }

    /**
     * Delete a watch status
     */
    public function delete($status_id, $user_id) {
        $query = "DELETE FROM WatchStatus 
                  WHERE status_id = ? AND user_id = ?";
        $preparedStatement = $this->pdo->prepare($query);
        return $preparedStatement->execute([$status_id, $user_id]);
    }

    /**
     * Delete a movie from the user's watchlist
     */
    public function deleteMovie(int $movie_id, int $user_id): bool {
        $query = "DELETE FROM Movies WHERE movie_id = ? AND user_id = ?";
        $preparedStatement = $this->pdo->prepare($query);
        return $preparedStatement->execute([$movie_id, $user_id]);
    }

    /**
     * Toggle watch status (plan/completed)
     */
    public function toggleStatus($movie_id, $user_id, $currentStatus = 0) {
        try {
            // Get current status
            $watchStatus = $this->findByMovieAndUser($movie_id, $user_id);

            if (!$watchStatus) {
                // Determine initial state based on current status
                $newState = $currentStatus ? 'plan' : 'completed';
                $newStatus = new WatchStatus($user_id, $movie_id, $newState, $newState === 'completed' ? 100 : 0);
                $result = $this->save($newStatus);
            } else {
                // Toggle between plan and completed
                $currentState = $watchStatus->getWatchState();
                $newState = ($currentState === 'completed') ? 'plan' : 'completed';
                
                $watchStatus->setWatchState($newState);
                $watchStatus->setProgressPercent($newState === 'completed' ? 100 : 0);
                $result = $this->update($watchStatus);
            }

            // Update Movies table
            $isCompleted = isset($newStatus) ? ($newStatus->getWatchState() === 'completed') : ($watchStatus->getWatchState() === 'completed');
            $query = "UPDATE Movies 
                      SET watched = ?, watch_date = ?
                      WHERE movie_id = ? AND user_id = ?";
            $preparedStatement = $this->pdo->prepare($query);
            $updateResult = $preparedStatement->execute([
                $isCompleted ? 1 : 0,
                $isCompleted ? date('Y-m-d') : null,
                $movie_id,
                $user_id
            ]);

            if (!$updateResult || !$result) {
                throw new Exception("Failed to update watch status");
            }

            return true;
        } catch (Exception $e) {
            throw new Exception("Toggle status error: " . $e->getMessage());
        }
    }
}
?>
