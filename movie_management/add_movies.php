<?php
include('../includes/db.php');

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$basePath = '../';

// Add movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $rating = $_POST['rating'];
    $watched = isset($_POST['watched']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    // Validation
    if (empty($title) || empty($genre)) {
        echo "All fields required!";
    } elseif ($rating < 1 || $rating > 5) {
        echo "Rating must be 1-5";
    } else {
        $stmt = $pdo->prepare("INSERT INTO movies (title, genre, release_date, rating, watched, user_id)
                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $genre, $release_date, $rating, $watched, $user_id]);
        header("Location: view_movies.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Add Movie | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/manage.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<h2>Add Movie</h2>
<form method="POST">
    Title: <input type="text" name="title"><br>
    Genre: <input type="text" name="genre"><br>
    Date: <input type="date" name="release_date"><br>
    Rating: <input type="number" name="rating"><br>
    Watched: <input type="checkbox" name="watched"><br>
    <button>Add</button>
</form>

<button><a href="view_movies.php">Back</a></button>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>