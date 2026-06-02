<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';
require_once $basePath . 'admin/includes/AdminManager.php';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'movie_management/MovieManager.php';
require_once $basePath . 'genres_management/Genre.php';

AdminAuth::requireLogin();

$adminManager = new AdminManager($pdo);
$movieRepository = new MovieRepository($pdo);
$genreRepository = new GenreRepository($pdo);

$genres = $genreRepository->getAll();
$users = $adminManager->getAllUsers();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $releaseDate = $_POST['release_date'] ?: null;
    $ownerUserId = (int)($_POST['owner_user_id'] ?? 0);

    if ($title === '' || $genre === '') {
        $error = 'Title and Genre are required.';
    } elseif ($ownerUserId <= 0) {
        $error = 'Please select a valid owner.';
    } else {
        try {
            $movie = new Movie(
                null,
                $title,
                $genre,
                $releaseDate,
                0,
                null,
                null,
                $ownerUserId
            );
            $movieRepository->add($movie);
            header('Location: manage_movies.php');
            exit;
        } catch (Exception $e) {
            $error = 'Unable to add movie: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie | Admin Dashboard</title>
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
                <h1>Add Movie</h1>
            </div>
            <div class="admin-user-info">
                <span>Logged in as: <strong><?php echo htmlspecialchars(AdminAuth::getUsername()); ?></strong></span>
                <a href="<?php echo $basePath; ?>admin/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="table-card">
            <h2><i class="fas fa-film"></i> Add Movie</h2>
            <form method="POST" class="admin-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="owner_user_id">Owner</label>
                        <select id="owner_user_id" name="owner_user_id" required>
                            <option value="">Select a user</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo (int)$user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="title">Movie Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter movie title" required>
                    </div>

                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <select id="genre" name="genre" required>
                            <option value="">Select a genre</option>
                            <?php foreach ($genres as $genreItem): ?>
                                <?php if ((int)$genreItem->is_active === 1): ?>
                                    <option value="<?php echo htmlspecialchars($genreItem->name); ?>">
                                        <?php echo htmlspecialchars($genreItem->name); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="release_date">Release Date</label>
                        <input type="date" id="release_date" name="release_date" placeholder="YYYY-MM-DD">
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i> Add Movie
                    </button>
                    <a href="<?php echo $basePath; ?>admin/manage_movies.php" class="btn-primary" style="background-color: #6c757d;">
                        <i class="fas fa-arrow-left"></i> Back to Movies
                    </a>
                </div>
            </form>
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

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .admin-form {
            margin-top: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
