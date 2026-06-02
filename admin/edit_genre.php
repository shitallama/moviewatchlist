<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'genres_management/Genre.php';

AdminAuth::requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: manage_genres.php');
    exit;
}

$repository = new GenreRepository($pdo);
$genre = $repository->getById($id);

if (!$genre) {
    header('Location: manage_genres.php');
    exit;
}

$error = '';
$name = $genre->name;
$description = $genre->description;
$isActive = $genre->is_active;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $isActive = ($_POST['is_active'] ?? '1') === '1' ? 1 : 0;

    if ($name === '') {
        $error = 'Genre name is required.';
    } else {
        try {
            $genre->name = $name;
            $genre->description = $description;
            $genre->is_active = $isActive;

            if ($repository->update($genre)) {
                header('Location: manage_genres.php');
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Genre | Admin Dashboard</title>
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
                <h1>Edit Genre</h1>
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
            <h2><i class="fas fa-tags"></i> Update Genre</h2>
            <form method="POST" class="admin-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Genre Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Genre name" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Describe the genre" style="width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; box-sizing: border-box;"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active">
                            <option value="1" <?php echo $isActive == 1 ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo $isActive == 0 ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="<?php echo $basePath; ?>admin/manage_genres.php" class="btn-primary" style="background-color: #6c757d;">
                        <i class="fas fa-arrow-left"></i> Back to Genres
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
        .form-group textarea,
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
