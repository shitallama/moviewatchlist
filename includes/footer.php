    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-film"></i> MovieHub</h3>
                <p>Your complete movie management system. Track, rate, and discover amazing movies.</p>
                <div class="social-links">
                    <a href="https://github.com/shitallama/moviewatchlist" target="_blank"><i class="fab fa-github"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="movies.php">Browse Movies</a></li>
                    <li><a href="watchlist.php">My Watchlist</a></li>
                    <li><a href="genres.php">Genres</a></li>
                    <li><a href="reviews.php">Reviews</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Account</h4>
                <ul>
                    <?php if($isLoggedIn): ?>
                        <li><a href="profile.php">My Profile</a></li>
                        <li><a href="settings.php">Settings</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
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
    
    <script src="js/script.js"></script>
</body>
