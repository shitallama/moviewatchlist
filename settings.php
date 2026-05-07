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
$errors = [];
$success = [];

$stmt = $pdo->prepare('SELECT username, email, password_hash FROM Users WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

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
            $check = $pdo->prepare('SELECT user_id FROM Users WHERE (username = :username OR email = :email) AND user_id != :user_id LIMIT 1');
            $check->execute([
                'username' => $username,
                'email' => $email,
                'user_id' => $userId,
            ]);
            $exists = $check->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                $errors[] = 'That username or email is already in use.';
            } else {
                $update = $pdo->prepare('UPDATE Users SET username = :username, email = :email WHERE user_id = :user_id');
                $update->execute([
                    'username' => $username,
                    'email' => $email,
                    'user_id' => $userId,
                ]);

                $_SESSION['user_name'] = $username;
                $success[] = 'Profile details updated.';
                $user['username'] = $username;
                $user['email'] = $email;
            }
        }
    }

    if ($action === 'update_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password_hash'] ?? '')) {
            $errors[] = 'Current password is incorrect.';
        }

        if ($newPassword === '' || strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New password and confirmation do not match.';
        }

        if (empty($errors)) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE Users SET password_hash = :password_hash WHERE user_id = :user_id');
            $update->execute([
                'password_hash' => $hash,
                'user_id' => $userId,
            ]);
            $success[] = 'Password updated.';
        }
    }

    if ($action === 'delete_account') {
        $confirmPassword = $_POST['delete_password'] ?? '';
        $confirmDelete = isset($_POST['confirm_delete']);

        if (!$confirmDelete) {
            $errors[] = 'Please confirm you want to delete your account.';
        }

        if (!password_verify($confirmPassword, $user['password_hash'] ?? '')) {
            $errors[] = 'Password confirmation failed.';
        }

        if (empty($errors)) {
            $delete = $pdo->prepare('DELETE FROM Users WHERE user_id = :user_id');
            $delete->execute(['user_id' => $userId]);

            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();

            header('Location: ' . $basePath . 'Login/login.php');
            exit;
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
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
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

<script>
(() => {
    const trigger = document.querySelector('[data-delete-trigger]');
    const modal = document.querySelector('[data-delete-modal]');
    const closeButtons = document.querySelectorAll('[data-delete-close]');
    const confirmButton = document.querySelector('[data-delete-confirm]');
    const form = trigger ? trigger.closest('form') : null;

    const openModal = () => {
        if (!modal) {
            return;
        }
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = () => {
        if (!modal) {
            return;
        }
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    if (trigger) {
        trigger.addEventListener('click', openModal);
    }

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    if (confirmButton && form) {
        confirmButton.addEventListener('click', () => {
            form.submit();
        });
    }
})();
</script>
</html>
