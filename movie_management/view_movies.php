<?php
include('../includes/db.php');

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';
$user_id = $_SESSION['user_id'];

// Base query
$sql = "SELECT * FROM movies WHERE user_id = ?";
$params = [$user_id];

// SEARCH (Find)
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " AND title LIKE ?";
    $params[] = "%$search%";
}

// FILTER (Genre)
if (isset($_GET['genre']) && $_GET['genre'] != "") {
    $genre = $_GET['genre'];
    $sql .= " AND genre = ?";
    $params[] = $genre;
}

// FILTER (Watched)
if (isset($_GET['watched']) && $_GET['watched'] != "") {
    $watched = $_GET['watched'];
    $sql .= " AND watched = ?";
    $params[] = $watched;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Movie List | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/manage.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="manage-container">
    <div class="manage-header">
        <h2>My Movie Collection</h2>
        <p>Manage and track your personal movie library</p>
    </div>

    <!-- SEARCH + FILTER FORM -->
    <div class="filter-section">
        <form class="filter-form" method="GET">
            <div class="form-group">
                <label for="search">Search Movies</label>
                <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Enter movie title...">
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($_GET['genre'] ?? '') ?>" placeholder="Filter by genre...">
            </div>
            <div class="form-group">
                <label for="watched">Status</label>
                <select id="watched" name="watched">
                    <option value="">All Movies</option>
                    <option value="1" <?= ($_GET['watched'] ?? '') === '1' ? 'selected' : '' ?>>Watched</option>
                    <option value="0" <?= ($_GET['watched'] ?? '') === '0' ? 'selected' : '' ?>>Not Watched</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
        </form>
    </div>

    <div class="action-links">
        <a href="add_movies.php" class="btn-primary">Add New Movie</a>
    </div>

    <table class="manage-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Genre</th>
                <th>Release Date</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($result as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['genre']) ?></td>
                <td><?= htmlspecialchars($row['release_date']) ?></td>
                <td>
                    <?php if($row['rating']): ?>
                        <span class="rating-stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $row['rating'] ? 'filled' : '' ?>">&#9733;</span>
                            <?php endfor; ?>
                        </span>
                    <?php else: ?>
                        Not rated
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status-badge <?= $row['watched'] ? 'watched' : 'not-watched' ?>">
                        <?= $row['watched'] ? 'Watched' : 'Not Watched' ?>
                    </span>
                </td>
                <td>
                    <a href="edit_movies.php?id=<?= $row['movie_id'] ?>" class="action-link edit">Edit</a>
                    <a href="delete_movies.php?id=<?= $row['movie_id'] ?>" class="action-link delete" onclick="return confirm('Are you sure you want to delete this movie?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if(empty($result)): ?>
    <div class="empty-state">
        <p>No movies found. <a href="add_movies.php">Add your first movie</a> to get started!</p>
    </div>
    <?php endif; ?>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>