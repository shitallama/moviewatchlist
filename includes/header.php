<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = isset($basePath) ? $basePath : '';
// Check if user is logged in (you can adjust this logic)
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? 'Guest';
?>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="<?php echo $basePath; ?>index.php">
                    <i class="fas fa-film"></i>
                    <span>CineList</span>
                </a>
            </div>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-menu" id="navMenu">
                <ul class="nav-links">
                    <li><a href="<?php echo $basePath; ?>index.php"><i class="fas fa-home"></i> Home</a></li>
                    <?php if($isLoggedIn): ?>
                    <li><a href="<?php echo $basePath; ?>movie_management/add_movies.php"><i class="fas fa-video"></i> Movies</a></li>
                    <li><a href="<?php echo $basePath; ?>watchlist.php"><i class="fas fa-eye"></i> Watchlist</a></li>
                    <li><a href="<?php echo $basePath; ?>genres.php"><i class="fas fa-tags"></i> Genres</a></li>
                    <li><a href="<?php echo $basePath; ?>reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                    <?php endif; ?>
                </ul>
                
                <div class="nav-user">
                    <?php if($isLoggedIn): ?>
                        <div class="user-dropdown">
                            <button class="user-btn">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo htmlspecialchars($userName); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="<?php echo $basePath; ?>profile.php"><i class="fas fa-id-card"></i> Profile</a>
                                <a href="<?php echo $basePath; ?>settings.php"><i class="fas fa-cog"></i> Settings</a>
                                <hr>
                                <a href="<?php echo $basePath; ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $basePath; ?>Login/login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="<?php echo $basePath; ?>Login/register.php" class="btn-register"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="main-content">