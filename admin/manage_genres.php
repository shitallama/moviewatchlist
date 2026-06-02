<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'genres_management/Genre.php';

AdminAuth::requireLogin();

$repository = new GenreRepository($pdo);
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $genreId = isset($_POST['genre_id']) ? (int)$_POST['genre_id'] : null;

    if ($action === 'delete' && $genreId) {
        if ($repository->delete($genreId)) {
            $message = 'Genre has been successfully deleted.';
            $messageType = 'success';
        } else {
            $message = 'Error deleting genre.';
            $messageType = 'error';
        }
    }
}

$allGenres = $repository->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Genres | Admin Dashboard</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <div class="admin-header">
            <div class="admin-title">
                <i class="fas fa-crown"></i>
                <h1>Manage Genres</h1>
            </div>
            <div class="admin-user-info">
                <span>Logged in as: <strong><?php echo htmlspecialchars(AdminAuth::getUsername()); ?></strong></span>
                <a href="<?php echo $basePath; ?>admin/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="table-card">
            <h2><i class="fas fa-tags"></i> Genre List</h2>

            <div class="action-links" style="margin-bottom: 16px;">
                <a href="<?php echo $basePath; ?>admin/add_genre.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Genre
                </a>
            </div>

            <?php if (!empty($allGenres)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allGenres as $genre): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($genre->name); ?></strong></td>
                                <td><?php echo htmlspecialchars($genre->description ?? ''); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $genre->is_active ? 'active' : 'inactive'; ?>">
                                        <?php echo $genre->is_active ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo $genre->created_at ? date('M d, Y', strtotime($genre->created_at)) : 'N/A'; ?></td>
                                <td>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <a href="<?php echo $basePath; ?>admin/edit_genre.php?id=<?php echo (int)$genre->genre_id; ?>" class="btn-action btn-primary">
                                            <i class="fas fa-pen"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline; margin: 0;">
                                            <input type="hidden" name="genre_id" value="<?php echo (int)$genre->genre_id; ?>">
                                            <button type="submit" name="action" value="delete" class="btn-action btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this genre?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No genres found</p>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px;">
            <a href="<?php echo $basePath; ?>admin/dashboard.php" class="btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <style>
        .alert {
            padding: 15px 20px;
            margin: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background-color: #0277bd;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #01579b;
        }
    </style>
</body>
</html>
