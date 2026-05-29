<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';
require_once $basePath . 'admin/includes/AdminManager.php';
require_once $basePath . 'includes/db.php';

// Check if admin is logged in
AdminAuth::requireLogin();

$adminManager = new AdminManager($pdo);
$message = '';
$messageType = '';

// Handle user deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

    if ($action === 'deactivate' && $userId) {
        if ($adminManager->deactivateUser($userId)) {
            $message = 'User has been successfully deactivated.';
            $messageType = 'success';
        } else {
            $message = 'Error deactivating user.';
            $messageType = 'error';
        }
    } elseif ($action === 'reactivate' && $userId) {
        if ($adminManager->reactivateUser($userId)) {
            $message = 'User has been successfully reactivated.';
            $messageType = 'success';
        } else {
            $message = 'Error reactivating user.';
            $messageType = 'error';
        }
    }
}

// Fetch all users with their status
$allUsers = $adminManager->getAllUsersWithStatus();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin Dashboard</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="admin-title">
                <i class="fas fa-crown"></i>
                <h1>Manage Users</h1>
            </div>
            <div class="admin-user-info">
                <span>Logged in as: <strong><?php echo htmlspecialchars(AdminAuth::getUsername()); ?></strong></span>
                <a href="<?php echo $basePath; ?>admin/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Users Management Table -->
        <div class="table-card">
            <h2><i class="fas fa-users"></i> User Accounts Management</h2>

            <?php if (!empty($allUsers)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $user): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                        <i class="fas fa-<?php echo $user['is_active'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                        <?php echo $user['is_active'] ? 'Active' : 'Deactivated'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <?php if ($user['is_active']): ?>
                                            <button type="submit" name="action" value="deactivate" class="btn-action btn-danger" 
                                                    onclick="return confirm('Are you sure you want to deactivate this user? They will not be able to log in.');">
                                                <i class="fas fa-ban"></i> Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="reactivate" class="btn-action btn-success">
                                                <i class="fas fa-redo"></i> Reactivate
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No users found</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Back Button -->
        <div style="margin-top: 20px;">
            <a href="<?php echo $basePath; ?>admin/dashboard.php" class="btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <style>
        .alert {
            padding: 15px 20px;
            margin: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0277bd;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #01579b;
        }
    </style>

</body>
</html>
