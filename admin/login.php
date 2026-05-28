<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard if already logged in
if (AdminAuth::isLoggedIn()) {
    header('Location: ' . $basePath . 'admin/dashboard.php');
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username === '' || $password === '') {
        $errors[] = 'Please enter your username and password.';
    } else {
        if (AdminAuth::authenticate($username, $password)) {
            AdminAuth::setSession($username);
            header('Location: ' . $basePath . 'admin/dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-login-banner {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            color: white;
            font-weight: 600;
        }
        
        .admin-lock-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <section class="login-shell">
        <div class="login-panel">
            <div class="login-visual">
                <h1>Welcome to CineList Admin</h1>
                <p>System administrator portal. Manage movies, users, and content.</p>
                <div class="login-badges">
                    <div class="badge-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/clapperboard.svg" alt="" aria-hidden="true"> Manage movies</div>
                    <div class="badge-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/eye.svg" alt="" aria-hidden="true"> Monitor users</div>
                    <div class="badge-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/star.svg" alt="" aria-hidden="true"> System control</div>
                </div>
            </div>
            <div class="login-form-wrap">
                <div>
                    <div class="admin-lock-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h2>Admin Access</h2>
                    <p class="login-meta">System administrator credentials required.</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="form-alert">
                        <?php echo htmlspecialchars(implode(' ', $errors)); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Enter admin username" 
                            value="<?php echo htmlspecialchars($username); ?>"
                            required
                            autocomplete="username"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter admin password" 
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Sign In as Admin</button>
                </form>

                <div class="admin-login-banner">
                    <i class="fas fa-shield-alt"></i>
                    <p style="margin: 10px 0 0 0;">This is a restricted area. Unauthorized access is prohibited.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
