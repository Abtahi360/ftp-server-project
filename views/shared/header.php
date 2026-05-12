<?php
session_start();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['csrf_token'];

    $basePath = '../../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FTP Media System — ISP Content Portal</title>
    <link rel="stylesheet" href="<?= $basePath ?>public/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">
        <a href="<?= $basePath ?>views/member/home.php">&#128190; FTP Media</a>
    </div>


    <ul class="nav-links">
        <li><a href="<?= $basePath ?>views/member/home.php">Home</a></li>
        <li><a href="<?= $basePath ?>views/member/browse.php">Browse</a></li>
        <li><a href="<?= $basePath ?>views/member/search.php">Search</a></li>
        <li><a href="<?= $basePath ?>views/member/request_box.php">Request Content</a></li>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="<?= $basePath ?>views/admin/dashboard.php">Admin Panel</a></li>
            <li><a href="<?= $basePath ?>views/auth/profile.php">Profile</a></li>
            <li><a href="<?= $basePath ?>controllers/authController.php?action=logout">Logout</a></li>

        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'moderator'): ?>
            <li><a href="<?= $basePath ?>views/moderator/dashboard.php">Mod Panel</a></li>
            <li><a href="<?= $basePath ?>views/auth/profile.php">Profile</a></li>
            <li><a href="<?= $basePath ?>controllers/authController.php?action=logout">Logout</a></li>

        <?php else: ?>
            <li><a href="<?= $basePath ?>views/auth/login.php">Staff Login</a></li>
        <?php endif; ?>
    </ul>


</nav>

<div class="container">

<?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }

    
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
?>
