<?php
    session_start();
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin','moderator'])) {
        header('location: ../auth/login.php'); exit;
    }
    require_once('../../models/contentModel.php');
    require_once('../../models/requestModel.php');
    $totalContents   = countContents();
    $pendingRequests = countPendingRequests();
    include('../shared/header.php');
?>

<div class="page-header">
    <h2>🛡️ Moderator Dashboard</h2>
    <span style="font-size:13px;color:var(--text2)">Welcome back, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📁</div>
        <div class="stat-body">
            <div class="stat-number"><?= $totalContents ?></div>
            <div class="stat-label">Total Contents</div>
        </div>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-icon stat-icon-orange">📬</div>
        <div class="stat-body">
            <div class="stat-number"><?= $pendingRequests ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header"><h2 class="section-title">⚡ Quick Actions</h2></div>
    <div class="quick-actions">
        <a href="contents.php" class="quick-action-card">
            <div class="qa-icon">📁</div> All Contents
        </a>
        <a href="upload_content.php" class="quick-action-card">
            <div class="qa-icon">⬆️</div> Upload File
        </a>
        <a href="requests.php" class="quick-action-card">
            <div class="qa-icon">📬</div>
            Requests <?= $pendingRequests > 0 ? "<span style='background:var(--orange);color:#fff;border-radius:99px;padding:1px 7px;font-size:11px;margin-left:4px;'>{$pendingRequests}</span>" : '' ?>
        </a>
    </div>
</div>
<?php include('../shared/footer.php'); ?>
