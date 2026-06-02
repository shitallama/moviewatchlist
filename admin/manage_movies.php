<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';
require_once $basePath . 'admin/includes/AdminManager.php';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'movie_management/MovieManager.php';

AdminAuth::requireLogin();

$adminManager = new AdminManager($pdo);
$movieRepository = new MovieRepository($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : null;

    if ($action === 'delete' && $movieId) {
        if ($movieRepository->delete($movieId, null)) {
            $message = 'Movie has been successfully deleted.';
            $messageType = 'success';
        } else {
            $message = 'Error deleting movie.';
            $messageType = 'error';
        }
    }
}

$allMovies = $adminManager->getAllMovies();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies | Admin Dashboard</title>
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
                <h1>Manage Movies</h1>
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
            <h2><i class="fas fa-film"></i> Movie Catalog</h2>

            <div class="action-links" style="margin-bottom: 16px;">
                <a href="<?php echo $basePath; ?>admin/add_movie.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Movie
                </a>
            </div>

            <?php if (!empty($allMovies)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Release Date</th>
                            <th>Rating</th>
                            <th>Reviews</th>
                            <th>Owner</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allMovies as $movie): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($movie['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($movie['genre'] ?? 'N/A'); ?></td>
                                <td><?php echo $movie['release_date'] ? date('M d, Y', strtotime($movie['release_date'])) : 'N/A'; ?></td>
                                <td>
                                    <?php if ($movie['avg_rating']): ?>
                                        <?php echo number_format($movie['avg_rating'], 1); ?>
                                    <?php else: ?>
                                        <span style="color: #95a5a6; font-style: italic;">Not Rated</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo (int)($movie['review_count'] ?? 0); ?></td>
                                <td>#<?php echo (int)($movie['user_id'] ?? 0); ?></td>
                                <td>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <a href="<?php echo $basePath; ?>admin/edit_movie.php?id=<?php echo (int)$movie['movie_id']; ?>" class="btn-action btn-primary">
                                            <i class="fas fa-pen"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline; margin: 0;">
                                            <input type="hidden" name="movie_id" value="<?php echo (int)$movie['movie_id']; ?>">
                                            <button type="submit" name="action" value="delete" class="btn-action btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this movie? This will remove its reviews and watch status too.');">
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
                    <p>No movies found</p>
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
