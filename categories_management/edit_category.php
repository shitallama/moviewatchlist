<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: view_category.php');
    exit;
}

$error = '';
$stmt = $pdo->prepare('SELECT * FROM categories WHERE category_id = :id');
$stmt->execute(['id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('Location: view_category.php');
    exit;
}

$name = $row['name'];
$description = $row['description'];
$is_active = $row['is_active'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = ($_POST['is_active'] ?? '1') === '1' ? 1 : 0;

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'UPDATE categories SET name = :name, description = :description, is_active = :is_active WHERE category_id = :id'
            );
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'is_active' => $is_active,
                'id' => $id,
            ]);

            header('Location: view_category.php?status=updated');
            exit;
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
    <title>Edit Category | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/categories.css">
</head>
<body class="categories-page">
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2 class="section-title">Edit Category</h2>
        <div class="header-actions">
            <a href="view_category.php" class="btn-secondary">Back to list</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="message-banner error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="category-form">
        <div class="form-field">
            <label for="name">Category name</label>
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
            <button type="submit" name="update" class="btn-view">Update Category</button>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</body>
</html>