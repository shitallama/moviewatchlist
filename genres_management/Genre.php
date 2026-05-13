<?php

class Genre
{
    public ?int $genre_id;
    public string $name;
    public ?string $description;
    public ?string $created_at;
    public int $is_active;

    public function __construct(
        ?int $genre_id,
        string $name,
        ?string $description,
        ?string $created_at,
        int $is_active = 1
    ) {
        $this->genre_id = $genre_id;
        $this->name = $name;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->is_active = $is_active;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['genre_id']) ? (int)$data['genre_id'] : null,
            $data['name'] ?? '',
            $data['description'] ?? null,
            $data['created_at'] ?? null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        );
    }
}

class GenreRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add(Genre $genre): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO genres (name, description, created_at, is_active) VALUES (:name, :description, CURDATE(), :is_active)'
        );

        return $stmt->execute([
            'name' => $genre->name,
            'description' => $genre->description,
            'is_active' => $genre->is_active,
        ]);
    }

    public function update(Genre $genre): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE genres SET name = :name, description = :description, is_active = :is_active WHERE genre_id = :id'
        );

        return $stmt->execute([
            'name' => $genre->name,
            'description' => $genre->description,
            'is_active' => $genre->is_active,
            'id' => $genre->genre_id,
        ]);
    }

    public function delete(int $genreId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM genres WHERE genre_id = :id');
        return $stmt->execute(['id' => $genreId]);
    }

    public function getById(int $genreId): ?Genre
    {
        $stmt = $this->pdo->prepare('SELECT * FROM genres WHERE genre_id = :id');
        $stmt->execute(['id' => $genreId]);
        $genreData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $genreData ? Genre::fromArray($genreData) : null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM genres ORDER BY created_at ASC');
        $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => Genre::fromArray($row), $genres);
    }
}
