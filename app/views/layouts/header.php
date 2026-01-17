<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn2Earn | Where Talent Meets Opportunity</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css?v=<?= time() ?>">
    <style>
        /* Add a specific badge color for Admin */
        .badge-admin { background: #8e44ad; color: white; } 
    </style>
</head>
<body>

<header class="main-header">
    <div class="header-content">
        
        <?php 
            // --- 1. SMART LINK LOGIC ---
            // Default link for Learner/Instructor/Client
            $dashboardLink = BASE_URL . 'dashboard/index'; 
            
            // Override if user is Admin
            if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
                $dashboardLink = BASE_URL . 'admin/dashboard';
            }
        ?>

        <div class="brand-section">
            <a href="<?= $dashboardLink ?>" class="logo">
                Learn<span class="highlight">2</span>Earn
            </a>
            <span class="divider">|</span>
            <span class="motto">Where Talent Meets Opportunity</span>
        </div>

        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-section">
                <div class="user-details">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <?php 
                        $role = ucfirst($_SESSION['user_role']);
                        $badgeClass = '';
                        // Determine Badge Color
                        if($role == 'Learner') $badgeClass = 'badge-learner';
                        elseif($role == 'Instructor') $badgeClass = 'badge-instructor';
                        elseif($role == 'Client') $badgeClass = 'badge-client';
                        elseif($role == 'Admin') $badgeClass = 'badge-admin';
                    ?>
                    <span class="role-badge <?= $badgeClass ?>"><?= $role ?></span>
                </div>
                
                <nav class="header-nav">
                    <a href="<?= $dashboardLink ?>">Dashboard</a>
                    
                    <?php if($_SESSION['user_role'] != 'admin'): ?>
                        <a href="<?= BASE_URL ?>auth/profile">My Profile</a>
                    <?php endif; ?>
                    
                    <a href="<?= BASE_URL ?>auth/logout" class="btn-logout">Logout</a>
                </nav>
            </div>
        <?php else: ?>
            <div class="user-section">
                <a href="<?= BASE_URL ?>auth/login" class="btn-login">Login</a>
            </div>
        <?php endif; ?>
    </div>
</header>
<div class="main-container">