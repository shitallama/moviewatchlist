<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';

$error = '';
$name = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO categories (name, description, created_at, is_active) VALUES (:name, :description, CURDATE(), 1)'
            );
            $stmt->execute([
                'name' => $name,
                'description' => $description,
            ]);

            header('Location: view_category.php?status=added');
            exit;
        } catch (PDOException $e) {
            $error = 'Unable to save category. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Add Category | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/categories.css">
</head>
<body class="categories-page">
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2 class="section-title">Add Category</h2>
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
            <input type="text" id="name" name="name" placeholder="Category name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>

        <div class="form-field">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Description"><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" name="submit" class="btn-view">Add Category</button>
        </div>
    </form>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</body>
</html>