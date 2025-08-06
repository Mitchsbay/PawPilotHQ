<?php
// Redirect if already authenticated
if (isset($_COOKIE['auth_token'])) {
    require_once 'config/database.php';
    $supabase = new SupabaseClient();
    if ($supabase->verifyToken($_COOKIE['auth_token'])) {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - PawPilot HQ</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-header">
            <img src="images/logo.svg" alt="PawPilot HQ" class="auth-logo">
            <h1>PawPilot HQ</h1>
        </div>
        
        <div class="auth-card">
            <div class="auth-content">
                <h2>Welcome Back!</h2>
                <p>Sign in to access your pet's digital companion</p>
                
                <form id="loginForm" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
                        <a href="/forgot-password.php" class="forgot-link">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">Sign In</button>
                    
                    <div id="error-message" class="error-message" style="display: none;"></div>
                </form>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="/signup.php">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const email = formData.get('email');
            const password = formData.get('password');
            
            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';
            
            try {
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        action: 'login',
                        email: email,
                        password: password
                    })
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    window.location.href = '/index.php';
                } else {
                    errorDiv.textContent = result.error || 'Login failed';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>