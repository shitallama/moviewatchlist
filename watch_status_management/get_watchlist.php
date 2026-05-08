<?php
// watch_status_management/get_watchlist.php
$basePath = '../';
require_once '../includes/db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

try {
    $stmt = $pdo->prepare(
        "SELECT m.movie_id, m.title, m.watch_date, ws.watch_state, ws.progress_percent
         FROM WatchStatus ws
         JOIN Movies m ON ws.movie_id = m.movie_id
         WHERE ws.user_id = ?
         ORDER BY CASE ws.watch_state
             WHEN 'plan' THEN 1
             WHEN 'watching' THEN 2
             WHEN 'completed' THEN 3
             ELSE 4
         END, m.title ASC"
    );
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
    <title>Get Watchlist - MovieHub</title>
    <link rel="stylesheet" href="../assets/colors.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/watchstyle_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
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
                    <th>Progress</th>
                    <th>Watch Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($movies) === 0): ?>
                    <tr>
                        <td colspan="4">No movies found in the watchlist.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($movie['watch_state'])); ?></td>
                            <td><?php echo intval($movie['progress_percent']); ?>%</td>
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
