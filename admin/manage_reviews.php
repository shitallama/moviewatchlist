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

// Handle review deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $reviewId = isset($_POST['review_id']) ? (int)$_POST['review_id'] : null;

    if ($action === 'delete' && $reviewId) {
        if ($adminManager->deleteReview($reviewId)) {
            $message = 'Review has been successfully deleted.';
            $messageType = 'success';
        } else {
            $message = 'Error deleting review.';
            $messageType = 'error';
        }
    }
}

// Fetch all reviews for moderation
$allReviews = $adminManager->getAllReviewsForModeration();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews | Admin Dashboard</title>
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
                <h1>Manage Reviews</h1>
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

        <!-- Reviews Management Table -->
        <div class="table-card">
            <h2><i class="fas fa-comments"></i> Review Moderation</h2>

            <?php if (!empty($allReviews)): ?>
                <div class="reviews-container">
                    <?php foreach ($allReviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-meta">
                                    <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                    <span class="review-movie">
                                        <i class="fas fa-film"></i> <?php echo htmlspecialchars($review['title']); ?>
                                    </span>
                                    <span class="review-date">
                                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="review-rating">
                                    <span class="rating-stars">
                                        <?php for ($i = 0; $i < (int)$review['rating']; $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                    </span>
                                    <?php echo (int)$review['rating']; ?> / 5
                                </div>
                            </div>
                            <div class="review-body">
                                <p><?php echo nl2br(htmlspecialchars($review['review_text'] ?? 'No review text provided.')); ?></p>
                            </div>
                            <?php if ($review['is_recommended']): ?>
                                <div class="review-recommendation">
                                    <i class="fas fa-thumbs-up"></i> Recommended
                                </div>
                            <?php endif; ?>
                            <div class="review-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                    <button type="submit" name="action" value="delete" class="btn-action btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this review? This action cannot be undone.');">
                                        <i class="fas fa-trash"></i> Delete Review
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No reviews found</p>
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

        .reviews-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .review-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .review-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .review-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .review-meta strong {
            font-size: 14px;
        }

        .review-movie, .review-date {
            font-size: 12px;
            color: #666;
        }

        .review-movie i, .review-date i {
            margin-right: 4px;
        }

        .review-rating {
            text-align: right;
            font-weight: 600;
        }

        .rating-stars {
            color: #ffc107;
            margin-right: 8px;
        }

        .rating-stars i {
            font-size: 14px;
        }

        .review-body {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #333;
            font-size: 14px;
        }

        .review-body p {
            margin: 0;
        }

        .review-recommendation {
            background-color: #d4edda;
            color: #155724;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .review-actions {
            display: flex;
            gap: 10px;
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
            flex: 1;
            justify-content: center;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
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
