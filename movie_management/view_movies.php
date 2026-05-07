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

<h2>Movie List</h2>

<!-- SEARCH + FILTER FORM -->
<form method="GET">
    Search: <input type="text" name="search">
    Genre: <input type="text" name="genre">
    Watched:
    <select name="watched">
        <option value="">All</option>
        <option value="1">Watched</option>
        <option value="0">Not Watched</option>
    </select>
    <button>Filter</button>
</form>

<a href="add_movies.php">Add Movie</a>

<table border="1">
    <tr>
        <th>Title</th>
        <th>Genre</th>
        <th>Date</th>
        <th>Rating</th>
        <th>Watched</th>
        <th>Action</th>
    </tr>

    <?php foreach($result as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['genre']) ?></td>
        <td><?= htmlspecialchars($row['release_date']) ?></td>
        <td><?= htmlspecialchars($row['rating']) ?></td>
        <td><?= $row['watched'] ? 'Yes' : 'No' ?></td>
        <td>
            <a href="edit_movies.php?id=<?= $row['movie_id'] ?>">Edit</a>
            <a href="delete_movies.php?id=<?= $row['movie_id'] ?>">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>