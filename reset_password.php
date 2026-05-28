<?php
$basePath = '';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'includes/UserManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$successMessage = '';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$token = trim($token);

if ($token === '') {
    $errors[] = 'Invalid or missing reset token.';
}

$resetRow = null;
if ($token !== '') {
    $userManager = new UserManager($pdo);
    $resetRow = $userManager->getResetTokenDetails($token);

    if (!$resetRow || $resetRow['used_at'] !== null || strtotime($resetRow['expires_at']) < time()) {
        $errors[] = 'This reset link is invalid or has expired.';
        $resetRow = null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $resetRow) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword === '' || strlen($newPassword) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $userManager = new UserManager($pdo);
        if ($userManager->resetPasswordWithToken($token, $newPassword)) {
            $successMessage = 'Your password has been updated. You can now log in.';
        } else {
            $errors[] = 'Failed to reset password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Reset Password | CineList</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/login.css">
    <link rel="stylesheet" href="assets/reset.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<section class="auth-shell">
    <div class="auth-card">
        <div class="auth-title">
            <img class="icon" src="assets/icons/lock.svg" alt="" aria-hidden="true">
            <div>
                <h1>Reset password</h1>
                <p>Create a new password for your account.</p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="form-alert">
                <?php echo htmlspecialchars(implode(' ', $errors)); ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div class="form-success">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage === '' && $resetRow): ?>
            <form method="post" class="auth-form" novalidate>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-field">
                    <label for="new_password">New password</label>
                    <div class="password-wrap">
                        <input type="password" id="new_password" name="new_password" autocomplete="new-password" placeholder="Enter a new password" required>
                        <button class="toggle-password" type="button" data-target="new_password" aria-label="Show password">
                            <img class="icon" src="assets/icons/eye-closed.svg" alt="" aria-hidden="true" data-icon-open="assets/icons/eye-open.svg" data-icon-closed="assets/icons/eye-closed.svg">
                        </button>
                    </div>
                </div>
                <div class="form-field">
                    <label for="confirm_password">Confirm password</label>
                    <div class="password-wrap">
                        <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" placeholder="Re-enter your new password" required>
                        <button class="toggle-password" type="button" data-target="confirm_password" aria-label="Show password">
                            <img class="icon" src="assets/icons/eye-closed.svg" alt="" aria-hidden="true" data-icon-open="assets/icons/eye-open.svg" data-icon-closed="assets/icons/eye-closed.svg">
                        </button>
                    </div>
                </div>
                <button class="primary-btn" type="submit">Update password</button>
            </form>
        <?php endif; ?>

        <div class="auth-footer">
            <a href="Login/login.php">Back to login</a>
        </div>
    </div>
</section>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>
