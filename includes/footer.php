    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><img class="icon" src="<?php echo $basePath; ?>assets/icons/film.svg" alt="" aria-hidden="true" width="20" height="20"> MovieHub</h3>
                <p>Your complete movie management system. Track, rate, and discover amazing movies.</p>
                <div class="social-links">
                    <a href="https://github.com/shitallama/moviewatchlist" target="_blank" aria-label="GitHub">
                        <img class="icon" src="<?php echo $basePath; ?>assets/icons/github.svg" alt="" aria-hidden="true">
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo $basePath; ?>movies.php">Browse Movies</a></li>
                    <li><a href="<?php echo $basePath; ?>watchlist.php">My Watchlist</a></li>
                    <li><a href="<?php echo $basePath; ?>genres.php">Genres</a></li>
                    <li><a href="<?php echo $basePath; ?>reviews.php">Reviews</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Account</h4>
                <ul>
                    <?php if($isLoggedIn): ?>
                        <li><a href="<?php echo $basePath; ?>profile.php">My Profile</a></li>
                        <li><a href="<?php echo $basePath; ?>settings.php">Settings</a></li>
                        <li><a href="<?php echo $basePath; ?>Login/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $basePath; ?>Login/login.php">Login</a></li>
                        <li><a href="<?php echo $basePath; ?>Login/register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Team Members</h4>
                <ul>
                    <li><a href="https://www.shitallamatamang.com.np/" target="_blank">Shital - User Management</a></li>
                    <li><a href="https://www.facebook.com/babita.pakuwal#" target="_blank">Babita - Movie Management</a></li>
                    <li><a href="https://www.facebook.com/profile.php?id=100073511192402#" target="_blank">Mallika - Watch Status</a></li>
                    <li><a href="https://www.facebook.com/alia.magar.587#" target="_blank">Alina - Categories/Genres</a></li>
                    <li><a href="https://www.facebook.com/rasmi.kumal.180#" target="_blank">Rasmi - Review & Rating</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 MovieHub. All rights reserved. | <a class="footer-link" href="http://localhost/moviewatchlist/" target="_blank">CineList</a></p>
        </div>
    </footer>
    
    <script src="<?php echo $basePath; ?>assets/js/script.js"></script>
</body>
