<?php

class Movie
{
    public ?int $movie_id;
    public string $title;
    public string $genre;
    public ?string $release_date;
    public int $watched;
    public ?string $watch_date;
    public ?string $user_notes;
    public int $user_id;

    public function __construct(
        ?int $movie_id,
        string $title,
        string $genre,
        ?string $release_date,
        int $watched,
        ?string $watch_date,
        ?string $user_notes,
        int $user_id
    ) {
        $this->movie_id = $movie_id;
        $this->title = $title;
        $this->genre = $genre;
        $this->release_date = $release_date;
        $this->watched = $watched;
        $this->watch_date = $watch_date;
        $this->user_notes = $user_notes;
        $this->user_id = $user_id;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['movie_id']) ? (int)$data['movie_id'] : null,
            $data['title'] ?? '',
            $data['genre'] ?? '',
            $data['release_date'] !== '' ? $data['release_date'] : null,
            isset($data['watched']) ? (int)$data['watched'] : 0,
            $data['watch_date'] !== '' ? $data['watch_date'] : null,
            $data['user_notes'] !== '' ? $data['user_notes'] : null,
            (int)($data['user_id'] ?? 0)
        );
    }
}

class MovieRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add(Movie $movie): bool
    {
        $this->validateUser($movie->user_id);

        $stmt = $this->pdo->prepare(
            "INSERT INTO Movies (title, genre, release_date, watched, watch_date, user_notes, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $movie->title,
            $movie->genre,
            $movie->release_date,
            $movie->watched,
            $movie->watch_date,
            $movie->user_notes,
            $movie->user_id,
        ]);
    }

    private function validateUser(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID. Please log in again.');
        }

        $stmt = $this->pdo->prepare('SELECT 1 FROM Users WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);

        if (!$stmt->fetchColumn()) {
            throw new InvalidArgumentException('User not found. Please log in again.');
        }
    }

    public function update(Movie $movie, ?int $userId = null): bool
    {
        if ($userId === null) {
            $stmt = $this->pdo->prepare(
                "UPDATE Movies SET
                    title = ?,
                    genre = ?,
                    release_date = ?,
                    watched = ?,
                    watch_date = ?,
                    user_notes = ?
                WHERE movie_id = ?"
            );

            return $stmt->execute([
                $movie->title,
                $movie->genre,
                $movie->release_date,
                $movie->watched,
                $movie->watch_date,
                $movie->user_notes,
                $movie->movie_id,
            ]);
        }

        $stmt = $this->pdo->prepare(
            "UPDATE Movies SET
                title = ?,
                genre = ?,
                release_date = ?,
                watched = ?,
                watch_date = ?,
                user_notes = ?
            WHERE movie_id = ? AND user_id = ?"
        );

        return $stmt->execute([
            $movie->title,
            $movie->genre,
            $movie->release_date,
            $movie->watched,
            $movie->watch_date,
            $movie->user_notes,
            $movie->movie_id,
            $userId,
        ]);
    }

    public function delete(int $movieId, ?int $userId = null): bool
    {
        if ($userId === null) {
            $stmt = $this->pdo->prepare("DELETE FROM Movies WHERE movie_id = ?");
            return $stmt->execute([$movieId]);
        }

        $stmt = $this->pdo->prepare("DELETE FROM Movies WHERE movie_id = ? AND user_id = ?");
        return $stmt->execute([$movieId, $userId]);
    }

    public function getById(int $movieId, ?int $userId = null): ?Movie
    {
        if ($userId === null) {
            $stmt = $this->pdo->prepare("SELECT * FROM Movies WHERE movie_id = ?");
            $stmt->execute([$movieId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM Movies WHERE movie_id = ? AND user_id = ?");
            $stmt->execute([$movieId, $userId]);
        }
        $movieData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $movieData ? Movie::fromArray($movieData) : null;
    }

    public function find(int $userId, bool $showAll = false, string $search = '', string $genre = '', ?int $watched = null): array
    {
        $conditions = [];
        $params = [];

        if (!$showAll) {
            $conditions[] = "user_id = ?";
            $params[] = $userId;
        }

        if ($search !== '') {
            $conditions[] = "title LIKE ?";
            $params[] = "%$search%";
        }

        if ($genre !== '') {
            $conditions[] = "genre = ?";
            $params[] = $genre;
        }

        if ($watched !== null) {
            $conditions[] = "watched = ?";
            $params[] = $watched;
        }

        $sql = "SELECT * FROM Movies";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY title ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $moviesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($movieData) => Movie::fromArray($movieData), $moviesData);
    }

    public function getReviewsByMovieIds(array $movieIds): array
    {
        if (empty($movieIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($movieIds), '?'));
        $reviewSql = "SELECT * FROM Review WHERE movie_id IN ($placeholders) ORDER BY created_at DESC";
        $stmtReviews = $this->pdo->prepare($reviewSql);
        $stmtReviews->execute($movieIds);
        $allReviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

        $reviewsByMovie = [];
        foreach ($allReviews as $review) {
            $reviewsByMovie[$review['movie_id']][] = $review;
        }

        return $reviewsByMovie;
    }
}
