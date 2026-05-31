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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Watchlist - CineList</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/watchlist-modern.css">
</head>
<body>

<nav>
    <div class="logo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M2 8h20M7 3v18M17 3v18M2 14h20"/></svg>
        CineList
    </div>
    <div class="nav-links">
        <a href="../index.php"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg><span>Home</span></a>
        <a href="../movie_management/view_movies.php"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="14" height="12" rx="2"/><path d="m16 10 6-3v10l-6-3"/></svg><span>Movies</span></a>
        <a href="#" class="active"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg><span>Watchlist</span></a>
        <a href="../genres_management/view_genre.php"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 13.8 13.8 20a2 2 0 0 1-2.8 0l-7-7V4h9z"/><circle cx="7.5" cy="7.5" r="1"/></svg><span>Genres</span></a>
        <a href="../review_system/all_reviews.php"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 2 3 6.5 7 .5-5.5 4.5 2 7L12 17l-6.5 3.5 2-7L2 9l7-.5z"/></svg><span>Reviews</span></a>
        <button class="nav-btn" onclick="toggleTheme()">
            <svg id="themeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
            <span id="themeLabel">Dark</span>
        </button>
        <span class="user"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
    </div>
</nav>

<main>
    <div class="head">
        <h1>My Watchlist</h1>
        <button class="btn" onclick="openModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
            Add to Wishlist
        </button>
    </div>

    <div class="controls">
        <label for="search">Find:</label>
        <input id="search" class="input" type="text" placeholder="Search by title..." oninput="applyFilters()">
        <label for="statusFilter">Filter:</label>
        <select id="statusFilter" class="select" onchange="applyFilters()">
            <option value="">All statuses</option>
            <option value="plan">Plan</option>
            <option value="completed">Completed</option>
        </select>
        <div class="control-btns">
            <button class="btn" onclick="applyFilters()">Find</button>
            <button class="btn btn-ghost" onclick="clearFilters()">Clear</button>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">Error loading watchlist: <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

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
                <?php if (count($movies) === 0): ?>
                    <tr><td colspan="6" class="empty">You haven't added any movies to your watchlist yet. <a href="../movie_management/view_movies.php">Browse movies</a> to add them to your list.</td></tr>
                <?php else: ?>
                    <?php foreach ($movies as $movie): 
                        $watchState = $movie['watch_state'] ?? 'plan';
                        $watched = !empty($movie['watched']);
                        $progress = intval($movie['progress_percent'] ?? 0);
                        $statusLabel = ucfirst($watchState);
                    ?>
                        <tr>
                            <td class="title-cell"><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><span class="pill <?php echo $watched ? 'completed' : 'plan'; ?>"><?php echo $statusLabel; ?></span></td>
                            <td>
                                <div class="progress">
                                    <div class="bar"><span style="width:<?php echo $progress; ?>%"></span></div>
                                    <small><?php echo $progress; ?>%</small>
                                </div>
                            </td>
                            <td><?php echo $movie['watch_date'] ? htmlspecialchars($movie['watch_date']) : '—'; ?></td>
                            <td class="center">
                                <div class="watch-toggle <?php echo $watched ? 'on' : ''; ?>" onclick="toggleWatched(<?php echo (int) $movie['movie_id']; ?>)" title="Toggle watched">
                                    <div class="switch"></div>
                                    <span class="watch-label"><?php echo $watched ? 'Watched' : 'Unwatch'; ?></span>
                                </div>
                            </td>
                            <td class="center">
                                <div class="actions">
                                    <a href="../movie_management/edit_movies.php?id=<?php echo (int) $movie['movie_id']; ?>" class="act">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>Edit
                                    </a>
                                    <form method="POST" action="remove_watchlist.php" class="inline-form" onsubmit="return confirm('Remove this movie from your watchlist?');">
                                        <input type="hidden" name="status_id" value="<?php echo htmlspecialchars($movie['status_id'] ?? ''); ?>">
                                        <button type="submit" class="act del">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- ADD / EDIT MODAL -->
<div class="overlay" id="overlay">
    <div class="modal">
        <h2>Add to Wishlist</h2>
        <p>Add a film you'd like to watch. Use the status and watch date to track your progress.</p>
        <form id="add-watchlist-form" method="POST" action="add_to_watchlist.php">
            <label for="mTitle">Title</label>
            <input id="mTitle" name="title" class="input" placeholder="Enter movie title..." required />

            <label for="mStatus">Status</label>
            <select id="mStatus" name="status" class="select">
                <option value="plan">Plan</option>
                <option value="completed">Completed</option>
            </select>

            <label for="mDate">Watch Date</label>
            <input id="mDate" name="watch_date" class="input" type="date" />

            <input type="hidden" name="redirect" value="watchlist.php">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleWatched(id) {
        const elem = document.querySelector(`.watch-toggle[onclick*="${id}"]`);
        const newStatus = elem?.classList.contains('on') ? 0 : 1;

        fetch('watchlistcontroller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'id=' + encodeURIComponent(id) + '&current_status=' + encodeURIComponent(newStatus)
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error: ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Update failed: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Unable to update watch status: ' + error.message);
        });
    }

    function openModal() {
        document.getElementById('overlay').classList.add('show');
        document.getElementById('mTitle').value = '';
        document.getElementById('mStatus').value = 'plan';
        document.getElementById('mDate').value = '';
    }

    function closeModal() {
        document.getElementById('overlay').classList.remove('show');
    }

    function applyFilters() {
        const q = document.getElementById('search').value.toLowerCase().trim();
        const s = document.getElementById('statusFilter').value.toLowerCase();
        
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (row.classList.contains('empty')) return;
            
            const title = row.querySelector('.title-cell')?.textContent.toLowerCase() || '';
            const status = row.querySelector('.pill')?.textContent.toLowerCase() || '';
            
            const titleMatch = title.includes(q);
            const statusMatch = !s || status.includes(s);
            
            row.style.display = (titleMatch && statusMatch) ? '' : 'none';
        });
    }

    function clearFilters() {
        document.getElementById('search').value = '';
        document.getElementById('statusFilter').value = '';
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = '';
        });
    }

    function toggleTheme() {
        const dark = document.body.classList.toggle('dark');
        document.getElementById('themeLabel').textContent = dark ? 'Light' : 'Dark';
        document.getElementById('themeIcon').innerHTML = dark
            ? '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>'
            : '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>';
    }

    function escapeHtml(s) {
        return s.replace(/[&<>"]/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;'
        }[c]));
    }
</script>
</body>
</html>
