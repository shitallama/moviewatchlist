<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'genres_management/Genre.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: view_genre.php');
    exit;
}

$repository = new GenreRepository($pdo);
$genre = $repository->getById($id);

if (!$genre) {
    header('Location: view_genre.php');
    exit;
}

$error = '';
$name = $genre->name;
$description = $genre->description;
$is_active = $genre->is_active;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = ($_POST['is_active'] ?? '1') === '1' ? 1 : 0;

    if ($name === '') {
        $error = 'Genre name is required.';
    } else {
        try {
            $genre->name = $name;
            $genre->description = $description;
            $genre->is_active = $is_active;

            if ($repository->update($genre)) {
                header('Location: view_genre.php?status=updated');
                exit;
            }

            $error = 'Unable to save changes. Please try again.';
        } catch (PDOException $e) {
            $error = 'Unable to save changes. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Edit Genre | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/categories.css">
</head>
<body class="categories-page">
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2 class="section-title">Edit Genre</h2>
        <div class="header-actions">
            <a href="view_genre.php" class="btn-secondary">Back to list</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="message-banner error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="category-form">
        <div class="form-field">
            <label for="name">Genre name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>

        <div class="form-field">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div class="form-field">
            <label for="is_active">Status</label>
            <select id="is_active" name="is_active">
                <option value="1" <?php echo $is_active == 1 ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo $is_active == 0 ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" name="update" class="btn-view">Update Genre</button>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</body>
</html>
