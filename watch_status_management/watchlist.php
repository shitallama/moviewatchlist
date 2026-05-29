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
                <p>You haven't added any movies to your watchlist yet. <a href="../movie_management/view_movies.php">Browse movies</a> to add them to your list.</p>
            <?php else: ?>
                <div class="watchlist-header">
                    <a href="../movie_management/view_movies.php" class="btn btn-primary">Browse Movies</a>
                </div>
                <div class="watchlist-controls">
                    <div class="search-group">
                        <label for="watchlist-search">Find:</label>
                        <input type="text" id="watchlist-search" placeholder="Search by title..." />
                    </div>
                    <div class="filter-group">
                        <label for="watchlist-filter">Filter:</label>
                        <select id="watchlist-filter">
                            <option value="all">All statuses</option>
                            <option value="plan">Plan</option>
                            <option value="watching">Watching</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="button-group">
                        <button type="button" id="watchlist-find-btn" class="btn btn-primary">Find</button>
                        <button type="button" id="watchlist-clear-btn" class="btn btn-secondary">Clear</button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Watchlist Status</th>
                            <th>Movie Status</th>
                            <th>Progress</th>
                            <th>Watch Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td>
                                    <span class="status <?php echo htmlspecialchars($movie['watch_state']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($movie['watch_state'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status <?php echo $movie['watched'] ? 'completed' : 'plan'; ?>">
                                        <?php echo $movie['watched'] ? 'Watched' : 'Not Watched'; ?>
                                    </span>
                                </td>
                                <td><?php echo intval($movie['progress_percent']); ?>%</td>
                                <td><?php echo $movie['watch_date'] ? htmlspecialchars($movie['watch_date']) : 'N/A'; ?></td>
                                <td class="action-buttons">
                                    <a href="../movie_management/edit_movies.php?id=<?php echo $movie['movie_id']; ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-secondary" onclick="toggleStatus(<?php echo $movie['movie_id']; ?>, <?php echo intval($movie['watch_state'] === 'completed'); ?>)">
                                        <i class="fas fa-eye"></i> <?php echo $movie['watch_state'] === 'completed' ? 'Unwatch' : 'Watch'; ?>
                                    </button>
                                    <?php if (!empty($movie['status_id'])): ?>
                                        <form method="POST" action="remove_watchlist.php" class="inline-form" onsubmit="return confirm('Remove this movie from your watchlist?');">
                                            <input type="hidden" name="status_id" value="<?php echo htmlspecialchars($movie['status_id']); ?>">
                                            <button type="submit" class="btn btn-delete">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="add_to_watchlist.php" class="inline-form">
                                            <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie['movie_id']); ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add to Watchlist
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?> 
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var searchInput = document.getElementById('watchlist-search');
            var filterSelect = document.getElementById('watchlist-filter');
            var findButton = document.getElementById('watchlist-find-btn');
            var clearButton = document.getElementById('watchlist-clear-btn');
            var rows = document.querySelectorAll('tbody tr');

            function applyFilter() {
                var query = searchInput.value.trim().toLowerCase();
                var statusFilter = filterSelect.value.toLowerCase();

                rows.forEach(function (row) {
                    var titleCell = row.querySelector('td:first-child');
                    var statusCell = row.querySelector('td:nth-child(2)');
                    var title = titleCell ? titleCell.textContent.toLowerCase() : '';
                    var statusText = statusCell ? statusCell.textContent.toLowerCase() : '';

                    var titleMatches = !query || title.indexOf(query) !== -1;
                    var statusMatches = statusFilter === 'all' || statusText.indexOf(statusFilter) !== -1;

                    row.style.display = titleMatches && statusMatches ? '' : 'none';
                });
            }

            findButton.addEventListener('click', function () {
                applyFilter();
            });

            clearButton.addEventListener('click', function () {
                searchInput.value = '';
                filterSelect.value = 'all';
                applyFilter();
            });

            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    applyFilter();
                }
            });
        });
    </script>
    <script src="../assets/js/toggle_status.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html> 