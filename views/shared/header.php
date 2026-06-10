<?php
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['csrf_token'];
    $basePath  = '../../';
    $role      = $_SESSION['role'] ?? '';
    $userName  = $_SESSION['name'] ?? '';

    // Determine active nav link
    $currentFile = basename($_SERVER['PHP_SELF']);
    $currentDir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FTP Media — ISP Content Portal</title>
    <link rel="stylesheet" href="<?= $basePath ?>public/assets/css/style.css">
</head>
<body>

<nav class="navbar" id="mainNav">
    <div class="nav-brand">
        <a href="<?= $basePath ?>views/member/home.php">
            <span class="brand-icon">💾</span>
            FTP Media
        </a>
    </div>

    <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="navLinks">
        <li><a href="<?= $basePath ?>views/member/home.php"><?= $currentFile === 'home.php' ? '<strong>' : '' ?>🏠 Home<?= $currentFile === 'home.php' ? '</strong>' : '' ?></a></li>
        <li><a href="<?= $basePath ?>views/member/browse.php">📂 Browse</a></li>
        <li><a href="<?= $basePath ?>views/member/search.php">🔍 Search</a></li>
        <li><a href="<?= $basePath ?>views/member/request_box.php">📬 Request</a></li>

        <?php if ($role === 'admin'): ?>
            <li><a href="<?= $basePath ?>views/admin/dashboard.php" class="nav-btn">⚙️ Admin</a></li>
            <li><a href="<?= $basePath ?>views/auth/profile.php">👤 <?= htmlspecialchars($userName) ?></a></li>
            <li><a href="<?= $basePath ?>controllers/authController.php?action=logout" class="nav-danger">🚪 Logout</a></li>

        <?php elseif ($role === 'moderator'): ?>
            <li><a href="<?= $basePath ?>views/moderator/dashboard.php" class="nav-btn">🛡️ Mod Panel</a></li>
            <li><a href="<?= $basePath ?>views/auth/profile.php">👤 <?= htmlspecialchars($userName) ?></a></li>
            <li><a href="<?= $basePath ?>controllers/authController.php?action=logout" class="nav-danger">🚪 Logout</a></li>

        <?php else: ?>
            <li><a href="<?= $basePath ?>views/auth/login.php" class="nav-btn">🔐 Staff Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="container">

<?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">✅ ' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">⚠️ ' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
?>

<script>
var toggle = document.getElementById('navToggle');
var links  = document.getElementById('navLinks');
if (toggle) toggle.addEventListener('click', function() {
    links.classList.toggle('open');
});
</script>
