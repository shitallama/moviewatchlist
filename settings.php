<?php
$basePath = '';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'includes/UserManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $basePath . 'Login/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$errors = [];
$success = [];

$userManager = new UserManager($pdo);
$user = $userManager->getUserById($userId);

if (!$user) {
    header('Location: ' . $basePath . 'Login/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($username === '' || strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($errors)) {
            $exists = $userManager->checkUserExistsExclude($username, $email, $userId);

            if ($exists) {
                $errors[] = 'That username or email is already in use.';
            } else {
                if ($userManager->updateProfile($userId, $email, $username)) {
                    $_SESSION['user_name'] = $username;
                    $success[] = 'Profile details updated.';
                    $user['username'] = $username;
                    $user['email'] = $email;
                } else {
                    $errors[] = 'Failed to update profile. Please try again.';
                }
            }
        }
    }

    if ($action === 'update_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$userManager->verifyPassword($currentPassword, $user['password_hash'] ?? '')) {
            $errors[] = 'Current password is incorrect.';
        }

        if ($newPassword === '' || strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New password and confirmation do not match.';
        }

        if (empty($errors)) {
            if ($userManager->updatePassword($userId, $newPassword)) {
                $success[] = 'Password updated.';
            } else {
                $errors[] = 'Failed to update password. Please try again.';
            }
        }
    }

    if ($action === 'delete_account') {
        $confirmPassword = $_POST['delete_password'] ?? '';
        $confirmDelete = isset($_POST['confirm_delete']);

        if (!$confirmDelete) {
            $errors[] = 'Please confirm you want to delete your account.';
        }

        if (!$userManager->verifyPassword($confirmPassword, $user['password_hash'] ?? '')) {
            $errors[] = 'Password confirmation failed.';
        }

        if (empty($errors)) {
            if ($userManager->deleteUser($userId)) {
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                }
                session_destroy();

                header('Location: ' . $basePath . 'Login/login.php');
                exit;
            } else {
                $errors[] = 'Failed to delete account. Please try again.';
            }
        }
    }

    if ($action === 'deactivate_account') {
        $deactivatePassword = $_POST['deactivate_password'] ?? '';

        if (!$userManager->verifyPassword($deactivatePassword, $user['password_hash'] ?? '')) {
            $errors[] = 'Password confirmation failed.';
        }

        if (empty($errors)) {
            if ($userManager->deactivateUserAccount($userId)) {
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                }
                session_destroy();

                header('Location: ' . $basePath . 'Login/login.php?msg=account_deactivated');
                exit;
            } else {
                $errors[] = 'Failed to deactivate account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Settings | CineList</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/settings.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<section class="settings-shell">
    <div class="settings-container">
        <div class="settings-hero">
            <div>
                <h1>Settings</h1>
                <p>Update your account details and security preferences.</p>
            </div>
            <a class="settings-link" href="profile.php">
                <img class="icon" src="assets/icons/id-card.svg" alt="" aria-hidden="true">
                View profile
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="form-alert">
                <?php echo htmlspecialchars(implode(' ', $errors)); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="form-success">
                <?php echo htmlspecialchars(implode(' ', $success)); ?>
            </div>
        <?php endif; ?>

        <div class="settings-grid">
            <form method="post" class="settings-card">
                <div class="settings-card-header">
                    <h2>Profile details</h2>
                    <span>Edit your username and email.</span>
                </div>
                <input type="hidden" name="action" value="update_profile">
                <div class="form-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" placeholder="Choose a username" required>
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" placeholder="Enter your email" required>
                </div>
                <button class="primary-btn" type="submit">Save profile</button>
            </form>

            <form method="post" class="settings-card">
                <div class="settings-card-header">
                    <h2>Change password</h2>
                    <span>Keep your account secure.</span>
                </div>
                <input type="hidden" name="action" value="update_password">
                <div class="form-field">
                    <label for="current_password">Current password</label>
                    <div class="password-wrap">
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password" placeholder="Enter your current password" required>
                        <button class="toggle-password" type="button" data-target="current_password" aria-label="Show password">
                            <img class="icon" src="assets/icons/eye-closed.svg" alt="" aria-hidden="true" data-icon-open="assets/icons/eye-open.svg" data-icon-closed="assets/icons/eye-closed.svg">
                        </button>
                    </div>
                </div>
                <div class="form-field">
                    <label for="new_password">New password</label>
                    <div class="password-wrap">
                        <input type="password" id="new_password" name="new_password" autocomplete="new-password" required placeholder="Enter a new password">
                        <button class="toggle-password" type="button" data-target="new_password" aria-label="Show password">
                            <img class="icon" src="assets/icons/eye-closed.svg" alt="" aria-hidden="true" data-icon-open="assets/icons/eye-open.svg" data-icon-closed="assets/icons/eye-closed.svg">
                        </button>
                    </div>
                </div>
                <div class="form-field">
                    <label for="confirm_password">Confirm new password</label>
                    <div class="password-wrap">
                        <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required placeholder="Confirm your new password">
                        <button class="toggle-password" type="button" data-target="confirm_password" aria-label="Show password">
                            <img class="icon" src="assets/icons/eye-closed.svg" alt="" aria-hidden="true" data-icon-open="assets/icons/eye-open.svg" data-icon-closed="assets/icons/eye-closed.svg">
                        </button>
                    </div>
                </div>
                <button class="primary-btn" type="submit">Update password</button>
            </form>

            <form method="post" class="settings-card settings-card-warning">
                <div class="settings-card-header">
                    <h2>Deactivate account</h2>
                    <span>Temporarily disable your account. You can reactivate it later.</span>
                </div>
                <input type="hidden" name="action" value="deactivate_account">
                <div class="form-field">
                    <label for="deactivate_password">Confirm password</label>
                    <input type="password" id="deactivate_password" name="deactivate_password" autocomplete="current-password" required placeholder="Enter your password">
                </div>
                <p style="font-size: 14px; color: #666; margin: 10px 0;">
                    <i class="fas fa-info-circle"></i> Your account will be deactivated and you won't be able to log in. You can reactivate it anytime.
                </p>
                <button class="warning-btn" type="button" data-deactivate-trigger>Deactivate account</button>
            </form>

            <form method="post" class="settings-card settings-card-danger">
                <div class="settings-card-header">
                    <h2>Delete account</h2>
                    <span>This action permanently removes your account and data.</span>
                </div>
                <input type="hidden" name="action" value="delete_account">
                <div class="form-field">
                    <label for="delete_password">Confirm password</label>
                    <input type="password" id="delete_password" name="delete_password" autocomplete="current-password" required placeholder="Enter your password">
                </div>
                <label class="confirm-row">
                    <input type="checkbox" name="confirm_delete"  required>
                    I understand this action cannot be undone.
                </label>
                <button class="danger-btn" type="button" data-delete-trigger>Delete account</button>
            </form>
        </div>
    </div>
</section>

<div class="modal" data-deactivate-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="deactivate-modal-title">
    <div class="modal-backdrop" data-deactivate-close></div>
    <div class="modal-card" role="document">
        <h2 id="deactivate-modal-title">Deactivate your account</h2>
        <p>Your account will be temporarily deactivated. You can reactivate it anytime by logging back in.</p>
        <div class="modal-actions">
            <button type="button" class="secondary-btn" data-deactivate-close>Cancel</button>
            <button type="button" class="warning-btn" data-deactivate-confirm>Yes, deactivate</button>
        </div>
    </div>
</div>

<div class="modal" data-delete-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
    <div class="modal-backdrop" data-delete-close></div>
    <div class="modal-card" role="document">
        <h2 id="delete-modal-title">Confirm account deletion</h2>
        <p>This will permanently remove your account and all related data. This action cannot be undone.</p>
        <div class="modal-actions">
            <button type="button" class="secondary-btn" data-delete-close>Cancel</button>
            <button type="button" class="danger-btn" data-delete-confirm>Yes, delete</button>
        </div>
    </div>
</div>

<?php require_once $basePath . 'includes/footer.php'; ?>
<script src="<?php echo $basePath; ?>assets/js/settings.js"></script>
</html>
