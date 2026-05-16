<?php

    session_start();

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
        header('location: ../auth/login.php');
        exit;
    }

    require_once('../../models/contentModel.php');
    require_once('../../models/requestModel.php');

    $totalContents   = countContents();
    $pendingRequests = countPendingRequests();

    include('../shared/header.php');
?>

<h2>&#128100; Moderator Dashboard</h2>
<p>Welcome back, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>.</p>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $totalContents ?></div>
        <div class="stat-label">Total Contents</div>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-number"><?= $pendingRequests ?></div>
        <div class="stat-label">Pending Requests</div>
    </div>
</div>

<div class="section">
    <h3>Quick Actions</h3>
    <div class="action-links">
        <a href="contents.php"       class="btn btn-primary">&#128190; View All Contents</a>
        <a href="upload_content.php" class="btn btn-secondary">+ Upload Content</a>
        <a href="requests.php"       class="btn btn-warning">&#128231; View Requests (<?= $pendingRequests ?> pending)</a>
    </div>
</div>

<?php include('../shared/footer.php'); ?>
 