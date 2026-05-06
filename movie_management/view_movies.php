<?php
include('../includes/db.php');

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

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

<a href="add_movies.php">Add Movie</a> |
<a href="../Login/logout.php">Logout</a>

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
<td><?= $row['title'] ?></td>
<td><?= $row['genre'] ?></td>
<td><?= $row['release_date'] ?></td>
<td><?= $row['rating'] ?></td>
<td><?= $row['watched'] ? 'Yes' : 'No' ?></td>
<td>
<a href="edit_movies.php?id=<?= $row['movie_id'] ?>">Edit</a>
<a href="delete_movies.php?id=<?= $row['movie_id'] ?>">Delete</a>
</td>
</tr>
<?php endforeach; ?>

</table>