<?php
// watch_status_management/watchlist.php
$basePath = '../';
require_once '../includes/db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT movie_id, title, watched, watch_date FROM Movies WHERE user_id = ? ORDER BY watched ASC, title ASC");
    $stmt->execute([$user_id]);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $movies = [];
    $error = $e->getMessage();
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Watchlist - CineList</title>
    <link rel="stylesheet" href="../assets/colors.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/watchstyle_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main>
        <section class="container">
            <h2>My Watchlist</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">Error loading watchlist: <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (count($movies) === 0): ?>
                <p>You haven't added any movies to your watchlist yet. <a href="../movie_management/add_movies.php">Add some movies</a> to get started!</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Watch Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td>
                                    <span class="status <?php echo $movie['watched'] ? 'watched' : 'to-watch'; ?>">
                                        <?php echo $movie['watched'] ? 'Watched' : 'To Watch'; ?>
                                    </span>
                                </td>
                                <td><?php echo $movie['watch_date'] ? htmlspecialchars($movie['watch_date']) : 'N/A'; ?></td>
                                <td>
                                    <button onclick="toggleStatus(<?php echo $movie['movie_id']; ?>, <?php echo $movie['watched'] ? 1 : 0; ?>)" class="btn-toggle">
                                        <i class="fas <?php echo $movie['watched'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                        Mark as <?php echo $movie['watched'] ? 'Unwatched' : 'Watched'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    <script src="../assets/js/toggle_status.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>