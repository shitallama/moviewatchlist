<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'genres_management/Genre.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $repository = new GenreRepository($pdo);
    $repository->delete($id);
}

header('Location: view_genre.php?status=deleted');
exit;
?>