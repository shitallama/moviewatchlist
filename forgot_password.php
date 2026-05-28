<?php
$basePath = '';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'includes/UserManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$successMessage = '';
$resetLinkToShow = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        $userManager = new UserManager($pdo);
        $user = $userManager->getUserByEmail($email);

        if ($user) {
            $token = $userManager->createPasswordResetToken($user['user_id']);
            
            if ($token) {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $baseUrl = rtrim($scheme . '://' . $host . dirname($_SERVER['PHP_SELF']), '/\\');
                $resetLink = $baseUrl . '/reset_password.php?token=' . urlencode($token);

                $resetLinkToShow = $resetLink;
            }
        }

        if (empty($errors)) {
            $successMessage = 'If an account exists for that email, a reset link is ready below.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Forgot Password | CineList</title>
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
                <h1>Forgot your password?</h1>
                <p>Enter the email linked to your account to get a reset link.</p>
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
                <?php if ($resetLinkToShow !== ''): ?>
                    <div class="reset-link">
                        <a href="<?php echo htmlspecialchars($resetLinkToShow); ?>">Open reset link</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form" novalidate>
            <div class="form-field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" autocomplete="email" placeholder="name@example.com" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <button class="primary-btn" type="submit">Send reset link</button>
        </form>

        <div class="auth-footer">
            <a href="Login/login.php">Back to login</a>
        </div>
    </div>
</section>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>
