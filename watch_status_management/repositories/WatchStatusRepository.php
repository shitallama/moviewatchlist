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
                $result['created_at']
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
                $result['created_at']
            );
        }
        return null;
    }

    /**
     * Get all watch status for a user with movie details
     */
    public function getWatchlistForUser($user_id) {
        $query = "SELECT m.movie_id, m.title, m.watch_date, m.watched,
                         ws.status_id, 
                         COALESCE(ws.watch_state, CASE WHEN m.watched = 1 THEN 'completed' ELSE 'plan' END) AS watch_state,
                         COALESCE(ws.progress_percent, CASE WHEN m.watched = 1 THEN 100 ELSE 0 END) AS progress_percent
                  FROM Movies m
                  LEFT JOIN WatchStatus ws ON ws.movie_id = m.movie_id AND ws.user_id = ?
                  WHERE m.user_id = ?
                  ORDER BY CASE COALESCE(ws.watch_state, CASE WHEN m.watched = 1 THEN 'completed' ELSE 'plan' END)
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
        $query = "INSERT INTO WatchStatus (user_id, movie_id, watch_state, progress_percent, finished_at, created_at)
                  VALUES (?, ?, ?, ?, ?, NOW())";
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
     * Toggle watch status (plan/completed)
     */
    public function toggleStatus($movie_id, $user_id) {
        $this->pdo->beginTransaction();
        
        try {
            // Get current status
            $watchStatus = $this->findByMovieAndUser($movie_id, $user_id);

            if (!$watchStatus) {
                // Create new with 'plan' state
                $newStatus = new WatchStatus($user_id, $movie_id, 'plan', 0);
                $result = $this->save($newStatus);
            } else {
                // Toggle between plan and completed
                $currentState = $watchStatus->getWatchState();
                $newState = ($currentState === 'completed') ? 'plan' : 'completed';
                
                $watchStatus->setWatchState($newState);
                $result = $this->update($watchStatus);
            }

            // Update Movies table
            $isCompleted = isset($newStatus) ? ($newStatus->getWatchState() === 'completed') : ($watchStatus->getWatchState() === 'completed');
            $query = "UPDATE Movies 
                      SET watched = ?, watch_date = ?
                      WHERE movie_id = ? AND user_id = ?";
            $preparedStatement = $this->pdo->prepare($query);
            $preparedStatement->execute([
                $isCompleted ? 1 : 0,
                $isCompleted ? date('Y-m-d') : null,
                $movie_id,
                $user_id
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>
