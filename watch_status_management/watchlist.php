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

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Watchlist – CineList</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
    /* ── Design tokens ── */
    :root{
        --indigo:#5b5bf0;--indigo-600:#4f46e5;--indigo-700:#4338ca;
        --indigo-soft:#eef0ff;
        --bg:#ffffff;--bg-alt:#f7f8fb;--surface:#ffffff;
        --border:#e7e9f2;
        --text:#1e2235;--text-soft:#6b7090;
        --green:#16a34a;--green-soft:#e7f7ee;
        --amber:#b45309;--amber-soft:#fdf2e0;
        --red:#ef4444;--red-hover:#dc2626;
        --shadow:0 8px 24px rgba(40,44,90,.10);
        --radius:14px;
    }
    body.dark{
        --bg:#0f1120;--bg-alt:#161a2e;--surface:#1a1f38;
        --border:#2a3052;--text:#eef0ff;--text-soft:#9aa0c4;
        --indigo-soft:#23264a;--green-soft:#13351f;--amber-soft:#3a2a10;
        --shadow:0 10px 30px rgba(0,0,0,.45);
    }

    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);transition:background .3s,color .3s}

    /* layout */
    main{max-width:1180px;margin:0 auto;padding:2.5rem 2rem 4rem}
    .head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem}
    h1{font-size:2.4rem;font-weight:800;letter-spacing:-.02em}

    /* buttons */
    .btn{
        display:inline-flex;align-items:center;gap:.5rem;justify-content:center;
        background:var(--indigo);color:#fff;border:none;font-family:inherit;
        padding:.8rem 1.4rem;border-radius:12px;font-weight:700;font-size:.95rem;
        cursor:pointer;transition:.18s;box-shadow:0 4px 14px rgba(91,91,240,.35);
    }
    .btn:hover{background:var(--indigo-600);transform:translateY(-1px)}
    .btn:active{transform:translateY(0)}
    .btn svg{width:18px;height:18px}
    .btn-ghost{background:var(--bg-alt);color:var(--text);box-shadow:none;border:1px solid var(--border)}
    .btn-ghost:hover{background:var(--indigo-soft);color:var(--indigo)}

    /* controls */
    .controls label{display:block;font-weight:700;font-size:.95rem;margin-bottom:.5rem}
    .input,.select{
        width:100%;padding:1rem 1.1rem;border:1px solid var(--border);border-radius:12px;
        background:var(--bg-alt);color:var(--text);font-family:inherit;font-size:1rem;
        transition:.2s;margin-bottom:1.1rem;
    }
    .input:focus,.select:focus{outline:none;border-color:var(--indigo);box-shadow:0 0 0 3px rgba(91,91,240,.15)}
    .control-btns{display:flex;gap:.8rem;margin-bottom:1.8rem}

    /* alert */
    .alert{padding:.9rem 1.2rem;border-radius:10px;margin-bottom:1.2rem;font-weight:600}
    .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}

    /* table */
    .table-wrap{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);background:var(--surface)}
    table{width:100%;border-collapse:collapse}
    thead{background:var(--indigo)}
    th{color:#fff;text-align:left;padding:1.1rem 1.4rem;font-size:.82rem;letter-spacing:.06em;text-transform:uppercase;font-weight:700}
    th.center,td.center{text-align:center}
    tbody tr{border-top:1px solid var(--border);transition:background .15s}
    tbody tr:nth-child(even){background:var(--bg-alt)}
    tbody tr:hover{background:var(--indigo-soft)}
    td{padding:1.25rem 1.4rem;font-size:1rem;vertical-align:middle}
    .title-cell{font-weight:700}
    .empty{padding:3rem;text-align:center;color:var(--text-soft);font-weight:600}

    /* status pills */
    .pill{display:inline-block;padding:.35rem .85rem;border-radius:999px;font-size:.82rem;font-weight:700}
    .pill.plan{background:var(--amber-soft);color:var(--amber)}
    .pill.completed{background:var(--green-soft);color:var(--green)}
    .pill.watching{background:var(--indigo-soft);color:var(--indigo)}

    /* progress bar */
    .progress{display:flex;align-items:center;gap:.6rem;min-width:120px}
    .bar{flex:1;height:8px;border-radius:99px;background:var(--border);overflow:hidden}
    .bar span{display:block;height:100%;background:var(--indigo);border-radius:99px;transition:width .35s ease}
    .progress small{font-weight:700;color:var(--text-soft);min-width:38px;text-align:right}

    /* toggle switch */
    .watch-toggle{display:inline-flex;flex-direction:column;align-items:center;gap:.35rem;cursor:pointer;border:none;background:none;padding:0}
    .switch{position:relative;width:52px;height:28px;border-radius:99px;background:var(--border);transition:background .25s;display:block}
    .switch::after{content:"";position:absolute;top:3px;left:3px;width:22px;height:22px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.25);transition:transform .25s}
    .watch-toggle.on .switch{background:var(--green)}
    .watch-toggle.on .switch::after{transform:translateX(24px)}
    .watch-label{font-size:.74rem;font-weight:700;color:var(--text-soft);letter-spacing:.03em}
    .watch-toggle.on .watch-label{color:var(--green)}

    /* action buttons */
    .actions{display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap}
    .act{display:inline-flex;align-items:center;gap:.35rem;background:var(--indigo);color:#fff;border:none;font-family:inherit;font-weight:700;font-size:.85rem;padding:.5rem .9rem;border-radius:9px;cursor:pointer;transition:.15s;text-decoration:none}
    .act:hover{background:var(--indigo-600)}
    .act.del{background:var(--red)}
    .act.del:hover{background:var(--red-hover)}
    .act svg{width:14px;height:14px}

    /* modal */
    .overlay{position:fixed;inset:0;background:rgba(20,22,45,.55);backdrop-filter:blur(3px);display:none;align-items:center;justify-content:center;z-index:50;padding:1rem}
    .overlay.show{display:flex}
    .modal{background:var(--surface);border-radius:18px;padding:2rem;width:100%;max-width:480px;box-shadow:var(--shadow);border:1px solid var(--border);animation:pop .25s ease}
    @keyframes pop{from{opacity:0;transform:scale(.94) translateY(10px)}to{opacity:1;transform:none}}
    .modal h2{font-size:1.5rem;margin-bottom:.3rem}
    .modal p{color:var(--text-soft);margin-bottom:1.4rem;font-size:.95rem}
    .modal label{font-weight:700;font-size:.9rem;display:block;margin-bottom:.4rem}
    .modal .input,.modal .select{margin-bottom:1rem}
    .modal-actions{display:flex;gap:.7rem;margin-top:.5rem}
    .modal-actions .btn{flex:1}

    @media(max-width:860px){
        main{padding:1.5rem 1rem}
        th,td{padding:.9rem .7rem;font-size:.9rem}
        h1{font-size:1.9rem}
    }
    </style>
</head>
<body>
<main>
    <div class="head">
        <h1>My Watchlist</h1>
        <!-- "Add to Wishlist" opens the modal to pick a movie and add it -->
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

    <!-- Search & filter controls -->
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
                        <!-- Toggle watched / unwatched via AJAX -->
                        <button type="button"
                                class="watch-toggle <?php echo $isWatched ? 'on' : ''; ?>"
                                onclick="toggleWatched(this, <?php echo $movieId; ?>, <?php echo $isWatched; ?>)">
                            <span class="switch"></span>
                            <span class="watch-label"><?php echo $isWatched ? 'Watched' : 'Unwatch'; ?></span>
                        </button>
                    </td>
                    <td class="center">
                        <div class="actions">
                            <!-- Edit opens the update-status modal -->
                            <button class="act"
                                    onclick="openEditModal(<?php echo $statusId; ?>, '<?php echo addslashes($movie['title']); ?>', '<?php echo $state; ?>', <?php echo $progress; ?>)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>Edit
                            </button>
                            <?php if ($statusId): ?>
                            <!-- Delete removes from watchlist -->
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
</main>

<!-- ── EDIT STATUS MODAL ── -->
<div class="overlay" id="editOverlay">
    <div class="modal">
        <h2 id="editModalTitle">Edit Watch Status</h2>
        <p>Update the watch state and your progress for this film.</p>
        <form method="POST" action="update_status.php" id="editForm">
            <input type="hidden" name="status_id" id="editStatusId">
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

<!-- ── ADD TO WISHLIST MODAL ── -->
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
                // Load all movies belonging to this user that aren't already in the watchlist
                try {
                    $stmt = $pdo->prepare("
                        SELECT m.movie_id, m.title
                        FROM Movies m
                        LEFT JOIN WatchStatus ws ON ws.movie_id = m.movie_id AND ws.user_id = ?
                        WHERE m.user_id = ? AND ws.status_id IS NULL
                        ORDER BY m.title ASC
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

<script>
/* ── Search / filter ── */
function applyFilters() {
    var q  = document.getElementById('search').value.toLowerCase().trim();
    var sf = document.getElementById('statusFilter').value.toLowerCase();
    document.querySelectorAll('#tbody tr').forEach(function(row) {
        var title  = row.querySelector('.title-cell').textContent.toLowerCase();
        var state  = (row.dataset.state || '').toLowerCase();
        var tMatch = !q  || title.includes(q);
        var sMatch = !sf || state === sf;
        row.style.display = tMatch && sMatch ? '' : 'none';
    });
}
function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('statusFilter').value = '';
    applyFilters();
}

/* ── Watched toggle (AJAX to watchlistcontroller.php) ── */
function toggleWatched(btn, movieId, currentStatus) {
    fetch('watchlistcontroller.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + movieId + '&current_status=' + currentStatus
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success) {
            var isNowOn = !currentStatus;
            btn.classList.toggle('on', isNowOn);
            btn.querySelector('.watch-label').textContent = isNowOn ? 'Watched' : 'Unwatch';
            // Update the onclick for next click
            btn.setAttribute('onclick',
                'toggleWatched(this,' + movieId + ',' + (isNowOn ? 1 : 0) + ')');
        }
    })
    .catch(function(){ alert('Could not update status. Please try again.'); });
}

/* ── Edit modal ── */
function openEditModal(statusId, title, state, progress) {
    document.getElementById('editStatusId').value   = statusId;
    document.getElementById('editMovieTitle').value = title;
    document.getElementById('editWatchState').value = state;
    document.getElementById('editProgress').value   = progress;
    document.getElementById('editProgressLabel').textContent = progress + '%';
    document.getElementById('editOverlay').classList.add('show');
}
function closeEditModal() {
    document.getElementById('editOverlay').classList.remove('show');
}
document.getElementById('editOverlay').addEventListener('click', function(e){
    if (e.target === this) closeEditModal();
});

/* ── Add modal ── */
function openAddModal() {
    document.getElementById('addOverlay').classList.add('show');
}
function closeAddModal() {
    document.getElementById('addOverlay').classList.remove('show');
}
document.getElementById('addOverlay').addEventListener('click', function(e){
    if (e.target === this) closeAddModal();
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
