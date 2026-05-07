<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';

$status = $_GET['status'] ?? '';
$message = '';
if ($status === 'added') {
    $message = 'Category added successfully.';
} elseif ($status === 'updated') {
    $message = 'Category updated successfully.';
} elseif ($status === 'deleted') {
    $message = 'Category deleted successfully.';
}

$stmt = $pdo->query('SELECT * FROM categories ORDER BY created_at ASC');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Categories | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/categories.css">
</head>
<body class="categories-page">
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2 class="section-title">Categories List</h2>
        <div class="header-actions">
            <a href="add_category.php" class="btn-view">Add Category</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message-banner success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
        <div class="message-banner">
            No categories found yet. Use the button above to add your first category.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $rowNumber = 1; ?>
                <?php foreach ($categories as $row): ?>
                    <tr>
                        <td data-label="No."><?php echo $rowNumber++; ?></td>
                        <td data-label="Name"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td data-label="Description"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td data-label="Created Date"><?php echo htmlspecialchars(date('M j, Y', strtotime($row['created_at']))); ?></td>
                        <td data-label="Status">
                            <span class="status-pill <?php echo $row['is_active'] == 1 ? 'active' : 'inactive'; ?>">
                                <?php echo $row['is_active'] == 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="table-actions" data-label="Actions">
                            <a href="edit_category.php?id=<?php echo urlencode($row['category_id']); ?>">Edit</a>
                            <a href="delete_category.php?id=<?php echo urlencode($row['category_id']); ?>" class="btn-secondary" onclick="return confirm('Delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</body>
</html>