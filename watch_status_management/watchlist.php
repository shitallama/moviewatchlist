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
            <div class="watchlist-header">
                <div class="watchlist-heading">
                    <h2>My Watchlist</h2>
                    <p class="watchlist-subtitle">Track progress, update watched status, and keep your favorites in one place.</p>
                </div>
                <a href="../movie_management/view_movies.php" class="btn btn-primary btn-add-watchlist">+ Add to Wishlist</a>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">Error loading watchlist: <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (count($movies) === 0): ?>
                <p>You haven't added any movies to your watchlist yet. <a href="../movie_management/view_movies.php">Browse movies</a> to add them to your list.</p>
            <?php else: ?>
                <div class="watchlist-controls">
                    <div class="filter-group">
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
                            <th>STATUS</th>
                            <th>WATCHED</th>
                            <th>Progress</th>
                            <th>Watch Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie): 
                            $watchState = $movie['watch_state'] ?? 'plan';
                            $statusClass = 'status-chip--plan';

                            if ($watchState === 'completed') {
                                $statusClass = 'status-chip--completed';
                            } elseif ($watchState === 'watching') {
                                $statusClass = 'status-chip--watching';
                            } elseif ($watchState === 'dropped') {
                                $statusClass = 'status-chip--dropped';
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td>
                                    <span class="status-chip <?php echo $statusClass; ?>">
                                        <?php echo ucfirst(htmlspecialchars($watchState)); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="watch-toggle <?php echo $movie['watched'] ? 'watched' : 'unwatched'; ?>"
                                            onclick="toggleStatus(<?php echo $movie['movie_id']; ?>, <?php echo intval($movie['watch_state'] === 'completed'); ?>)">
                                        <span class="switch"></span>
                                    </button>
                                    <div class="watch-toggle-label">
                                        <?php echo $movie['watched'] ? 'Unwatch' : 'Watched'; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress-wrapper">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo intval($movie['progress_percent']); ?>%;"></div>
                                        </div>
                                        <span class="progress-text"><?php echo intval($movie['progress_percent']); ?>%</span>
                                    </div>
                                </td>
                                <td><?php echo $movie['watch_date'] ? htmlspecialchars($movie['watch_date']) : 'N/A'; ?></td>
                                <td class="action-buttons">
                                    <div class="action-group">
                                        <a href="../movie_management/edit_movies.php?id=<?php echo $movie['movie_id']; ?>" class="btn btn-edit">Edit</a>
                                        <?php if (!empty($movie['status_id'])): ?>
                                            <form method="POST" action="remove_watchlist.php" class="inline-form" onsubmit="return confirm('Remove this movie from your watchlist?');">
                                                <input type="hidden" name="status_id" value="<?php echo htmlspecialchars($movie['status_id']); ?>">
                                                <button type="submit" class="btn btn-delete">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="watchlist-label">Not in watchlist</span>
                                        <?php endif; ?>
                                    </div>
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