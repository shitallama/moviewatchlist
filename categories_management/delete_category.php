<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM categories WHERE category_id = :id');
    $stmt->execute(['id' => $id]);
}

header('Location: view_category.php?status=deleted');
exit;
?>