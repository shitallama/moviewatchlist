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
                    <img class="icon" src="<?php echo $basePath; ?>assets/icons/film.svg" alt="" aria-hidden="true" width="28" height="28" style="fill: var(--primary-color);">
                    <span>CineList</span>
                </a>
            </div>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
                <img class="icon" src="<?php echo $basePath; ?>assets/icons/menu.svg" alt="" aria-hidden="true">
            </button>
            
            <div class="nav-menu" id="navMenu">
                <ul class="nav-links">
                    <li><a href="<?php echo $basePath; ?>index.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/home.svg" alt="" aria-hidden="true"> Home</a></li>
                    <?php if($isLoggedIn): ?>
                    <li><a href="<?php echo $basePath; ?>movie_management/view_movies.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/video.svg" alt="" aria-hidden="true"> Movies</a></li>
                    <li><a href="<?php echo $basePath; ?>watch_status_management/watchlist.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/eye.svg" alt="" aria-hidden="true"> Watchlist</a></li>
                    <li><a href="<?php echo $basePath; ?>categories_management/view_category.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/tag.svg" alt="" aria-hidden="true"> Genres</a></li>
                    <li><a href="<?php echo $basePath; ?>review_system/review_page.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/star.svg" alt="" aria-hidden="true"> Reviews</a></li>
                    <?php endif; ?>
                </ul>
                
                <div class="nav-user">
                    <button class="theme-toggle" type="button" data-theme-toggle aria-pressed="false">
                        <img class="icon" data-theme-icon src="<?php echo $basePath; ?>assets/icons/moon.svg" alt="" aria-hidden="true">
                        <span>Dark</span>
                    </button>
                    <?php if($isLoggedIn): ?>
                        <div class="user-dropdown">
                            <button class="user-btn">
                                <img class="icon" src="<?php echo $basePath; ?>assets/icons/user-circle.svg" alt="" aria-hidden="true">
                                <span><?php echo htmlspecialchars($userName); ?></span>
                                <img class="icon" src="<?php echo $basePath; ?>assets/icons/chevron-down.svg" alt="" aria-hidden="true">
                            </button>
                            <div class="dropdown-menu">
                                <a href="<?php echo $basePath; ?>profile.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/id-card.svg" alt="" aria-hidden="true"> Profile</a>
                                <a href="<?php echo $basePath; ?>settings.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/settings.svg" alt="" aria-hidden="true"> Settings</a>
                                <hr>
                                <a href="<?php echo $basePath; ?>Login/logout.php"><img class="icon" src="<?php echo $basePath; ?>assets/icons/logout.svg" alt="" aria-hidden="true"> Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $basePath; ?>Login/login.php" class="btn-login"><img class="icon" src="<?php echo $basePath; ?>assets/icons/login.svg" alt="" aria-hidden="true"> Login</a>
                        <a href="<?php echo $basePath; ?>Login/register.php" class="btn-register"><img class="icon" src="<?php echo $basePath; ?>assets/icons/user-plus.svg" alt="" aria-hidden="true"> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="main-content">