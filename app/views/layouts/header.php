<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn2Earn | Where Talent Meets Opportunity</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body>

<header class="main-header">
    <div class="header-content">
        <div class="brand-section">
            <a href="<?= BASE_URL ?>dashboard/index" class="logo">
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
                        if($role == 'Learner') $badgeClass = 'badge-learner';
                        elseif($role == 'Instructor') $badgeClass = 'badge-instructor';
                        else $badgeClass = 'badge-client';
                    ?>
                    <span class="role-badge <?= $badgeClass ?>"><?= $role ?></span>
                </div>
                
                <nav class="header-nav">
                    <a href="<?= BASE_URL ?>dashboard/index">Dashboard</a>
                    <a href="<?= BASE_URL ?>auth/profile">My Profile</a>
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