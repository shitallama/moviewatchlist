<?php
// watch status management/get_watchlist.php
$basePath = '../';
require_once '../includes/db.php';

try {
    $stmt = $pdo->query("SELECT movie_id, title, watched, watch_date FROM Movies ORDER BY watched ASC, title ASC");
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $movies = [];
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Watchlist - MovieHub</title>
    <link rel="stylesheet" href="../assets/colors.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/watchstyle_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
    <section class="container">
        <h2>Movie Watchlist</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">Error loading watchlist: <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Watch Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($movies) === 0): ?>
                    <tr>
                        <td colspan="3">No movies found in the watchlist.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo $movie['watched'] ? 'Watched' : 'To Watch'; ?></td>
                            <td><?php echo $movie['watch_date'] ? htmlspecialchars($movie['watch_date']) : 'N/A'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
<?php include '../includes/footer.php'; ?>
</body>
</html>