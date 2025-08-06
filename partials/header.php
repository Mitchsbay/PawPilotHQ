<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Check if user is authenticated
$user = null;
$isAuthenticated = false;

if (isset($_COOKIE['auth_token'])) {
    $supabase = new SupabaseClient();
    $user = $supabase->verifyToken($_COOKIE['auth_token']);
    $isAuthenticated = $user !== false;
}

// Get current page for navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawPilot HQ - Your Pet's Digital Companion</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>
<body>
    <?php if ($isAuthenticated): ?>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <img src="images/logo.svg" alt="PawPilot HQ" class="logo">
                <span class="brand-text">PawPilot HQ</span>
            </div>
            
            <div class="nav-links">
                <a href="/index.php" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">
                    <i class="icon-dashboard"></i>
                    Dashboard
                </a>
                <a href="/profile.php" class="nav-link <?php echo $currentPage === 'profile' ? 'active' : ''; ?>">
                    <i class="icon-pets"></i>
                    My Pets
                </a>
                <a href="/health.php" class="nav-link <?php echo $currentPage === 'health' ? 'active' : ''; ?>">
                    <i class="icon-health"></i>
                    Health
                </a>
                <a href="/social.php" class="nav-link <?php echo $currentPage === 'social' ? 'active' : ''; ?>">
                    <i class="icon-social"></i>
                    Social
                </a>
                <a href="/groups.php" class="nav-link <?php echo $currentPage === 'groups' ? 'active' : ''; ?>">
                    <i class="icon-groups"></i>
                    Groups
                </a>
                <a href="/events.php" class="nav-link <?php echo $currentPage === 'events' ? 'active' : ''; ?>">
                    <i class="icon-events"></i>
                    Events
                </a>
                <a href="/map.php" class="nav-link <?php echo $currentPage === 'map' ? 'active' : ''; ?>">
                    <i class="icon-map"></i>
                    Lost Pets
                </a>
            </div>
            
            <div class="nav-actions">
                <button class="btn btn-primary" onclick="showAddPetModal()">
                    <i class="icon-plus"></i>
                    Add Pet
                </button>
                <div class="nav-notifications">
                    <button class="notification-btn">
                        <i class="icon-bell"></i>
                        <span class="notification-badge">1</span>
                    </button>
                </div>
                <div class="user-menu">
                    <img src="<?php echo $user['user_metadata']['avatar_url'] ?? 'images/default-avatar.png'; ?>" 
                         alt="User Avatar" class="user-avatar">
                    <span class="user-name"><?php echo $user['user_metadata']['full_name'] ?? 'User'; ?></span>
                    <div class="user-dropdown">
                        <a href="/profile.php">Profile</a>
                        <a href="/settings.php">Settings</a>
                        <a href="#" onclick="logout()">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <main class="main-content">