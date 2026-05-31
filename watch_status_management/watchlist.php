<?php
// watch_status_management/watchlist.php
$basePath = '../';
require_once '../includes/db.php';
require_once 'WatchStatusService.php';
require_once 'repositories/WatchStatusRepository.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

$repository = new WatchStatusRepository($pdo);
$service    = new WatchStatusService($repository);

try {
    $movies = $service->getWatchlist($user_id);
} catch (Exception $e) {
    $movies = [];
    $error  = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Watchlist – CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/watchlist.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="watchlist-page">
    <div class="head">
        <h1>My Watchlist</h1>
        <button class="btn" onclick="openAddModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
            Add to Wishlist
        </button>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">Error loading watchlist: <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (count($movies) === 0): ?>
        <p style="color:var(--text-soft);font-weight:600">
            You haven't added any movies yet.
            <a href="../movie_management/view_movies.php" style="color:var(--indigo)">Browse movies</a> to add them.
        </p>
    <?php else: ?>

    <div class="controls">
        <label for="search">Find:</label>
        <input id="search" class="input" type="text" placeholder="Search by title..." oninput="applyFilters()">
        <label for="statusFilter">Filter:</label>
        <select id="statusFilter" class="select" onchange="applyFilters()">
            <option value="">All statuses</option>
            <option value="plan">Plan</option>
            <option value="watching">Watching</option>
            <option value="completed">Completed</option>
        </select>
        <div class="control-btns">
            <button class="btn" onclick="applyFilters()">Find</button>
            <button class="btn btn-ghost" onclick="clearFilters()">Clear</button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Watch Date</th>
                    <th class="center">Watched</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody id="tbody">
            <?php foreach ($movies as $movie):
                $state      = $movie['watch_state'] ?? 'plan';
                $pillClass  = $state === 'completed' ? 'completed' : ($state === 'watching' ? 'watching' : 'plan');
                $isWatched  = !empty($movie['watched']) ? 1 : 0;
                $progress   = intval($movie['progress_percent']);
                $watchDate  = $movie['watch_date'] ? htmlspecialchars($movie['watch_date']) : 'N/A';
                $statusId   = intval($movie['status_id'] ?? 0);
                $movieId    = intval($movie['movie_id']);
            ?>
                <tr data-state="<?php echo $state; ?>">
                    <td class="title-cell"><?php echo htmlspecialchars($movie['title']); ?></td>
                    <td><span class="pill <?php echo $pillClass; ?>"><?php echo ucfirst($state); ?></span></td>
                    <td>
                        <div class="progress">
                            <div class="bar"><span style="width:<?php echo $progress; ?>%"></span></div>
                            <small><?php echo $progress; ?>%</small>
                        </div>
                    </td>
                    <td><?php echo $watchDate; ?></td>
                    <td class="center">
                        <button type="button"
                                class="watch-toggle <?php echo $isWatched ? 'on' : ''; ?>"
                                onclick="toggleWatched(this, <?php echo $movieId; ?>, <?php echo $isWatched; ?>)">
                            <span class="switch"></span>
                            <span class="watch-label"><?php echo $isWatched ? 'Watched' : 'Unwatch'; ?></span>
                        </button>
                    </td>
                    <td class="center">
                        <div class="actions">
                            <button class="act"
                                onclick="openEditModal(<?php echo $statusId; ?>, <?php echo $movieId; ?>, '<?php echo addslashes($movie['title']); ?>', '<?php echo $state; ?>', <?php echo $progress; ?>)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>Edit
                            </button>
                            <?php if ($statusId): ?>
                            <form method="POST" action="remove_watchlist.php" class="inline" onsubmit="return confirm('Remove this movie from your watchlist?');">
                                <input type="hidden" name="status_id" value="<?php echo $statusId; ?>">
                                <button type="submit" class="act del">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>Delete
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<div class="overlay" id="editOverlay">
    <div class="modal">
        <h2 id="editModalTitle">Edit Watch Status</h2>
        <p>Update the watch state and your progress for this film.</p>
        <form method="POST" action="update_status.php" id="editForm">
            <input type="hidden" name="status_id" id="editStatusId">
            <input type="hidden" name="movie_id" id="editMovieId">
            <input type="hidden" name="redirect" value="watchlist.php">

            <label>Movie</label>
            <input class="input" id="editMovieTitle" disabled style="opacity:.6">

            <label for="editWatchState">Status</label>
            <select id="editWatchState" name="watch_state" class="select">
                <option value="plan">Plan to Watch</option>
                <option value="watching">Watching</option>
                <option value="completed">Completed</option>
            </select>

            <label for="editProgress">Progress: <span id="editProgressLabel">0%</span></label>
            <input type="range" id="editProgress" name="progress_percent"
                   min="0" max="100" step="5" value="0"
                   oninput="document.getElementById('editProgressLabel').textContent = this.value + '%'"
                   style="width:100%;accent-color:var(--indigo);margin-bottom:1rem">

            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="overlay" id="addOverlay">
    <div class="modal">
        <h2>Add to Wishlist</h2>
        <p>Choose a movie from your collection to add to your watchlist.</p>
        <form method="POST" action="add_to_watchlist.php">
            <input type="hidden" name="redirect" value="watchlist.php">

            <label for="addMovieId">Movie</label>
            <select id="addMovieId" name="movie_id" class="select" required>
                <option value="">Select a movie…</option>
                <?php
                try {
                    $stmt = $pdo->prepare("\
                        SELECT m.movie_id, m.title\
                        FROM Movies m\
                        LEFT JOIN WatchStatus ws ON ws.movie_id = m.movie_id AND ws.user_id = ?\
                        WHERE m.user_id = ? AND ws.status_id IS NULL\
                        ORDER BY m.title ASC\
                    ");
                    $stmt->execute([$user_id, $user_id]);
                    $availableMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($availableMovies as $am):
                ?>
                    <option value="<?php echo intval($am['movie_id']); ?>"><?php echo htmlspecialchars($am['title']); ?></option>
                <?php endforeach;
                } catch (Exception $e) { /* show empty list */ } ?>
            </select>

            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn">Add</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?php echo $basePath; ?>assets/js/watchlist.js"></script>
</body>
</html>
