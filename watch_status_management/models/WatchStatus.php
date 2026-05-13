<?php
/**
 * WatchStatus Model
 * Represents a watch status record
 */
class WatchStatus {
    private $status_id;
    private $user_id;
    private $movie_id;
    private $watch_state;
    private $progress_percent;
    private $finished_at;
    private $created_at;

    public function __construct(
        $user_id,
        $movie_id,
        $watch_state = 'plan',
        $progress_percent = 0,
        $status_id = null,
        $finished_at = null,
        $created_at = null
    ) {
        $this->status_id = $status_id;
        $this->user_id = $user_id;
        $this->movie_id = $movie_id;
        $this->watch_state = $watch_state;
        $this->progress_percent = max(0, min(100, $progress_percent));
        $this->finished_at = $finished_at;
        $this->created_at = $created_at;
    }

    // Getters
    public function getStatusId() {
        return $this->status_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getMovieId() {
        return $this->movie_id;
    }

    public function getWatchState() {
        return $this->watch_state;
    }

    public function getProgressPercent() {
        return $this->progress_percent;
    }

    public function getFinishedAt() {
        return $this->finished_at;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setWatchState($state) {
        $allowedStates = ['plan', 'watching', 'completed'];
        if (in_array($state, $allowedStates)) {
            $this->watch_state = $state;
            if ($state === 'completed' && $this->finished_at === null) {
                $this->finished_at = date('Y-m-d H:i:s');
                $this->progress_percent = 100;
            }
        }
    }

    public function setProgressPercent($percent) {
        $this->progress_percent = max(0, min(100, $percent));
    }

    public function setFinishedAt($datetime) {
        $this->finished_at = $datetime;
    }

    // Convert to array for database operations
    public function toArray() {
        return [
            'status_id' => $this->status_id,
            'user_id' => $this->user_id,
            'movie_id' => $this->movie_id,
            'watch_state' => $this->watch_state,
            'progress_percent' => $this->progress_percent,
            'finished_at' => $this->finished_at,
            'created_at' => $this->created_at,
        ];
    }
}
?>
