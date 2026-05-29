<?php
// watch_status_management/watchlist.php
$basePath = '../';
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Initialize service
$repository = new WatchStatusRepository($pdo);
$service = new WatchStatusService($repository);

try {
    $movies = $service->getWatchlist($user_id);
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
                            <th>Progress</th>
                            <th>Watch Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo intval($movie['progress_percent']); ?>%</td>
                                <td><?php echo $movie['watch_date'] ? htmlspecialchars($movie['watch_date']) : 'N/A'; ?></td>
                                <td class="action-buttons">
                                    <a href="../movie_management/edit_movies.php?id=<?php echo $movie['movie_id']; ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="remove_watchlist.php" class="inline-form" onsubmit="return confirm('Remove this movie from your watchlist?');">
                                        <input type="hidden" name="id" value="<?php echo $movie['movie_id']; ?>">
                                        <button type="submit" class="btn btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
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