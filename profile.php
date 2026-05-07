<?php
$basePath = '';
require_once $basePath . 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $basePath . 'Login/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT username, email, is_admin, created_at FROM Users WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ' . $basePath . 'Login/login.php');
    exit;
}

$roleLabel = ((int)($user['is_admin'] ?? 0) === 1) ? 'Admin' : 'Member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Profile | CineList</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<section class="profile-shell">
    <div class="profile-container">
        <div class="profile-hero">
            <div class="profile-title">
                <img class="icon" src="assets/icons/user-circle.svg" alt="" aria-hidden="true">
                <div>
                    <h1>Your Profile</h1>
                    <p>Keep your CineList identity up to date.</p>
                </div>
            </div>
            <a class="profile-cta" href="settings.php">
                <img class="icon" src="assets/icons/settings.svg" alt="" aria-hidden="true">
                Manage settings
            </a>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <h2>Account details</h2>
                <span class="profile-role">
                    <img class="icon" src="assets/icons/shield.svg" alt="" aria-hidden="true">
                    <?php echo htmlspecialchars($roleLabel); ?>
                </span>
            </div>
            <div class="profile-grid">
                <div class="profile-item">
                    <span>Username</span>
                    <strong><?php echo htmlspecialchars($user['username'] ?? ''); ?></strong>
                </div>
                <div class="profile-item">
                    <span>Email</span>
                    <strong><?php echo htmlspecialchars($user['email'] ?? ''); ?></strong>
                </div>
                <div class="profile-item">
                    <span>Member since</span>
                    <strong><?php echo htmlspecialchars(date('M d, Y', strtotime($user['created_at'] ?? 'now'))); ?></strong>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>
