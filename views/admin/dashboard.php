<?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php'); exit;
    }
    require_once('../../models/userModel.php');
    require_once('../../models/contentModel.php');
    require_once('../../models/categoryModel.php');
    require_once('../../models/requestModel.php');

    $totalContents   = countContents();
    $totalCategories = countCategories();
    $totalModerators = countModerators();
    $pendingRequests = countPendingRequests();

    include('../shared/header.php');
?>

<div class="page-header">
    <h2>⚙️ Admin Dashboard</h2>
    <span style="font-size:13px;color:var(--text2)">Welcome back, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
</div>

<!-- Stat Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📁</div>
        <div class="stat-body">
            <div class="stat-number"><?= $totalContents ?></div>
            <div class="stat-label">Total Contents</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">🗂️</div>
        <div class="stat-body">
            <div class="stat-number"><?= $totalCategories ?></div>
            <div class="stat-label">Categories</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">👥</div>
        <div class="stat-body">
            <div class="stat-number"><?= $totalModerators ?></div>
            <div class="stat-label">Moderators</div>
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

<!-- Quick Actions -->
<div class="section">
    <div class="section-header"><h2 class="section-title">⚡ Quick Actions</h2></div>
    <div class="quick-actions">
        <a href="moderators.php" class="quick-action-card">
            <div class="qa-icon">👥</div> Manage Moderators
        </a>
        <a href="add_moderator.php" class="quick-action-card">
            <div class="qa-icon">➕</div> Add Moderator
        </a>
        <a href="contents.php" class="quick-action-card">
            <div class="qa-icon">📁</div> Manage Contents
        </a>
        <a href="upload_content.php" class="quick-action-card">
            <div class="qa-icon">⬆️</div> Upload Content
        </a>
        <a href="requests.php" class="quick-action-card">
            <div class="qa-icon">📬</div> View Requests <?= $pendingRequests > 0 ? "<span style='background:var(--orange);color:#fff;border-radius:99px;padding:1px 7px;font-size:11px;margin-left:4px;'>{$pendingRequests}</span>" : '' ?>
        </a>
    </div>
</div>

<!-- Moderator Quick List via AJAX -->
<div class="section">
    <div class="section-header">
        <h2 class="section-title">👥 Moderator List</h2>
        <button onclick="loadModerators()" class="btn btn-secondary btn-sm">🔄 Refresh</button>
    </div>
    <div class="table-wrapper">
        <div id="modListBox" style="padding:24px;">
            <span class="muted">Click Refresh to load the moderator list.</span>
        </div>
    </div>
</div>

<script>
function loadModerators() {
    var box = document.getElementById('modListBox');
    box.innerHTML = '<div class="loading-pulse" style="padding:12px"><span></span><span></span><span></span></div>';
    var xhttp = new XMLHttpRequest();
    xhttp.open('get', '../../api/admin_moderators.php', true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            if (!resp.success) { box.innerHTML = '<div class="alert alert-error" style="margin:12px">' + resp.error + '</div>'; return; }
            if (resp.count === 0) { box.innerHTML = '<div style="padding:24px;text-align:center;color:var(--text3)">No moderators added yet.</div>'; return; }
            var html = '<div class="table-scroll"><table class="data-table"><thead><tr><th>#</th><th>Name</th><th>Email</th><th>Joined</th></tr></thead><tbody>';
            resp.moderators.forEach(function(m) {
                html += '<tr><td>' + parseInt(m.id) + '</td><td><strong>' + escapeHtml(m.name) + '</strong></td><td>' + escapeHtml(m.email) + '</td><td>' + escapeHtml(m.created_at) + '</td></tr>';
            });
            html += '</tbody></table></div>';
            box.innerHTML = html;
        }
    };
}
function escapeHtml(s) { var d=document.createElement('div'); d.appendChild(document.createTextNode(s||'')); return d.innerHTML; }
</script>

<?php include('../shared/footer.php'); ?>
