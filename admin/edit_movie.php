<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';
require_once $basePath . 'admin/includes/AdminManager.php';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'movie_management/MovieManager.php';
require_once $basePath . 'genres_management/Genre.php';

AdminAuth::requireLogin();

$movieId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$movieId) {
    header('Location: manage_movies.php');
    exit;
}

$movieRepository = new MovieRepository($pdo);
$genreRepository = new GenreRepository($pdo);
$genres = $genreRepository->getAll();

$movie = $movieRepository->getById($movieId, null);
if (!$movie) {
    header('Location: manage_movies.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie->title = trim($_POST['title'] ?? '');
    $movie->genre = trim($_POST['genre'] ?? '');
    $movie->release_date = $_POST['release_date'] ?: null;

    if ($movie->title === '' || $movie->genre === '') {
        $error = 'Title and Genre are required.';
    } else {
        $movieRepository->update($movie, null);
        header('Location: manage_movies.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie | Admin Dashboard</title>
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
                <h1>Edit Movie</h1>
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
            <h2><i class="fas fa-film"></i> Update Movie</h2>
            <form method="POST" class="admin-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Movie Title</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($movie->title); ?>" placeholder="Enter movie title" required>
                    </div>

                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <select id="genre" name="genre" required>
                            <option value="">Select a genre</option>
                            <?php foreach ($genres as $genreItem): ?>
                                <?php if ((int)$genreItem->is_active === 1): ?>
                                    <option value="<?php echo htmlspecialchars($genreItem->name); ?>" <?php echo $movie->genre === $genreItem->name ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($genreItem->name); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="release_date">Release Date</label>
                        <input type="date" id="release_date" name="release_date" value="<?php echo htmlspecialchars($movie->release_date); ?>" placeholder="YYYY-MM-DD">
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Changes
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
