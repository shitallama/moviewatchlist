<?php
// watch status management/update_status.php
$basePath = '../';
require_once '../includes/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = (int) $_POST['id'];
    $currentStatus = isset($_POST['current_status']) ? (int) $_POST['current_status'] : 0;
    $newStatus = $currentStatus ? 0 : 1;
    $watchDate = $newStatus ? date('Y-m-d') : null;

    try {
        $stmt = $pdo->prepare("UPDATE Movies SET watched = ?, watch_date = ? WHERE movie_id = ?");
        $stmt->execute([$newStatus, $watchDate, $id]);
        $message = 'Watch status updated successfully.';
    } catch (Exception $e) {
        $error = 'Unable to update status: ' . $e->getMessage();
    }
}

try {
    $stmt = $pdo->query("SELECT movie_id, title, watched FROM Movies ORDER BY title ASC");
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $movies = [];
    if (!$error) {
        $error = 'Unable to load movies: ' . $e->getMessage();
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
    <section class="container">
        <h2>Update Watch Status</h2>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="update_status.php">
            <div class="form-group">
                <label for="movie">Select Movie</label>
                <select id="movie" name="id" required onchange="updateStatusDisplay()">
                    <option value="">Choose a movie</option>
                    <?php foreach ($movies as $movie): ?>
                        <option value="<?php echo $movie['movie_id']; ?>" data-status="<?php echo $movie['watched']; ?>">
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Current Status:</label>
                <span id="current-status">Select a movie</span>
            </div>
            <input type="hidden" name="current_status" id="current-status-input" value="0">
            <button type="submit" class="btn btn-primary">Toggle Status</button>
        </form>
    </section>
    <script>
        function updateStatusDisplay() {
            const select = document.getElementById('movie');
            const statusSpan = document.getElementById('current-status');
            const statusInput = document.getElementById('current-status-input');
            const option = select.options[select.selectedIndex];

            if (option && option.value) {
                const status = option.getAttribute('data-status') === '1' ? 'Watched' : 'To Watch';
                statusSpan.textContent = status;
                statusInput.value = option.getAttribute('data-status');
            } else {
                statusSpan.textContent = 'Select a movie';
                statusInput.value = '0';
            }
        }
    </script>
</body>
</html>
<?php include '../includes/footer.php'; ?>
