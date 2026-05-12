<?php
// watch status management/update_status.php
$basePath = '../';
require_once '../includes/db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';
$selectedStatusId = 0;

$allowedStates = [
    'plan' => 'Plan to Watch',
    'watching' => 'Watching',
    'completed' => 'Completed',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['status_id'])) {
    $selectedStatusId = (int) $_POST['status_id'];
    $watch_state = isset($_POST['watch_state']) && array_key_exists($_POST['watch_state'], $allowedStates)
        ? $_POST['watch_state']
        : 'plan';
    $progress_percent = isset($_POST['progress_percent']) ? (int) $_POST['progress_percent'] : 0;
    $progress_percent = max(0, min(100, $progress_percent));

    $finished_at = $watch_state === 'completed' ? date('Y-m-d H:i:s') : null;
    if ($watch_state === 'completed') {
        $progress_percent = 100;
    }

    try {
        $stmt = $pdo->prepare(
            "UPDATE WatchStatus
             SET watch_state = ?, progress_percent = ?, finished_at = ?
             WHERE status_id = ? AND user_id = ?"
        );
        $stmt->execute([$watch_state, $progress_percent, $finished_at, $selectedStatusId, $user_id]);
        $message = 'Watch status updated successfully.';
    } catch (Exception $e) {
        $error = 'Unable to update watch status: ' . $e->getMessage();
    }
}

try {
    $stmt = $pdo->prepare(
        "SELECT ws.status_id, m.movie_id, m.title, ws.watch_state, ws.progress_percent, ws.finished_at
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
    $watchlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $watchlist = [];
    if (!$error) {
        $error = 'Unable to load watchlist items: ' . $e->getMessage();
    }
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Watch Status - MovieHub</title>
    <link rel="stylesheet" href="../assets/colors.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/watchstyle_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main>
        <section class="container">
            <h2>Update Watch Status</h2>
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (empty($watchlist)): ?>
                <p>You have no items in your watchlist yet. <a href="watchlist.php">Go to Watchlist</a> to add movies.</p>
            <?php else: ?>
                <form method="post" action="update_status.php">
                    <div class="form-group">
                        <label for="status_id">Select Watchlist Item</label>
                        <select id="status_id" name="status_id" required onchange="syncWatchStatus()">
                            <option value="">Choose a movie</option>
                            <?php foreach ($watchlist as $item): ?>
                                <option
                                    value="<?php echo $item['status_id']; ?>"
                                    data-state="<?php echo htmlspecialchars($item['watch_state']); ?>"
                                    data-progress="<?php echo intval($item['progress_percent']); ?>"
                                    <?php echo $selectedStatusId === (int) $item['status_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Current State</label>
                        <div id="current-state">Select a watchlist item</div>
                    </div>
                    <div class="form-group">
                        <label>Current Progress</label>
                        <div id="current-progress">0%</div>
                    </div>

                    <div class="form-group">
                        <label for="watch_state">New Status</label>
                        <select id="watch_state" name="watch_state" required>
                            <?php foreach ($allowedStates as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="progress_percent">Progress (%)</label>
                        <input type="range" id="progress_percent" name="progress_percent" min="0" max="100" step="5" value="0" oninput="document.getElementById('progress-output').textContent = this.value + '%';">
                        <div><span id="progress-output">0%</span></div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Status</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
    <script>
        function syncWatchStatus() {
            const select = document.getElementById('status_id');
            const stateDisplay = document.getElementById('current-state');
            const progressDisplay = document.getElementById('current-progress');
            const progressInput = document.getElementById('progress_percent');
            const progressOutput = document.getElementById('progress-output');
            const watchState = document.getElementById('watch_state');

            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                const state = option.getAttribute('data-state') || 'plan';
                const progress = option.getAttribute('data-progress') || '0';

                stateDisplay.textContent = state.charAt(0).toUpperCase() + state.slice(1);
                progressDisplay.textContent = progress + '%';
                progressInput.value = progress;
                progressOutput.textContent = progress + '%';
                watchState.value = state;
            } else {
                stateDisplay.textContent = 'Select a watchlist item';
                progressDisplay.textContent = '0%';
                progressInput.value = 0;
                progressOutput.textContent = '0%';
                watchState.value = 'plan';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            syncWatchStatus();
        });
    </script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
