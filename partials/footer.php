</main>
    
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <div class="footer-brand">
                    <img src="images/logo.svg" alt="PawPilot HQ" class="footer-logo">
                    <span class="footer-brand-text">PawPilot HQ</span>
                </div>
                <p class="footer-description">
                    Your pet's digital companion for health tracking and community connection.
                </p>
            </div>
            
            <div class="footer-section">
                <h4>Features</h4>
                <ul class="footer-links">
                    <li><a href="profile.php">Pet Profiles</a></li>
                    <li><a href="health.php">Health Tracking</a></li>
                    <li><a href="social.php">Pet Community</a></li>
                    <li><a href="map.php">Lost Pet Alerts</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul class="footer-links">
                    <li><a href="help.php">Help Center</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="community.php">Community</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Connect</h4>
                <ul class="footer-links">
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                    <li><a href="cookies.php">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 PawPilot HQ. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Global logout function
        function logout() {
            fetch('/api/auth.php', {
                method: 'DELETE',
                credentials: 'include'
            }).then(() => {
                window.location.href = 'login.php';
            });
        }

        // Global add pet modal function
        function showAddPetModal() {
            // Implementation for add pet modal
            window.location.href = 'profile.php?action=add';
        }
    </script>
</body>
</html>