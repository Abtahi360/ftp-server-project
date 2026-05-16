<?php
    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php');
        exit;
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

<h2>&#128187; Admin Dashboard</h2>
<p>Welcome back, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>.</p>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $totalContents ?></div>
        <div class="stat-label">Total Contents</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $totalCategories ?></div>
        <div class="stat-label">Categories</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $totalModerators ?></div>
        <div class="stat-label">Moderators</div>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-number"><?= $pendingRequests ?></div>
        <div class="stat-label">Pending Requests</div>
    </div>
</div>

<div class="section">
    <h3>Quick Actions</h3>
    <div class="action-links">
        <a href="moderators.php"      class="btn btn-primary">&#128100; Manage Moderators</a>
        <a href="add_moderator.php"   class="btn btn-secondary">+ Add Moderator</a>
        <a href="contents.php"        class="btn btn-primary">&#128190; Manage Contents</a>
        <a href="upload_content.php"  class="btn btn-secondary">+ Upload Content</a>
        <a href="requests.php"        class="btn btn-warning">&#128231; View Requests (<?= $pendingRequests ?> pending)</a>
    </div>
</div>


<div class="section">
    <h3>Moderator Quick List</h3>
    <button onclick="loadModerators()" class="btn btn-sm">Refresh</button>
    <div id="modListBox" style="margin-top:12px;">
        <span class="loading-text">Click Refresh to load.</span>
    </div>
</div>

<script>

function loadModerators() {
    var box   = document.getElementById('modListBox');
    box.innerHTML = '<span class="loading-text">Loading...</span>';

    var xhttp = new XMLHttpRequest();
    xhttp.open('get', '../../api/admin_moderators.php', true);
    xhttp.send();

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);

            if (!resp.success) {
                box.innerHTML = '<span class="alert alert-error">' + resp.error + '</span>';
                return;
            }

            if (resp.count === 0) {
                box.innerHTML = '<p class="empty-msg">No moderators yet.</p>';
                return;
            }

            var html = '<table class="data-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th></tr></thead><tbody>';
            resp.moderators.forEach(function(m) {
                html += '<tr>';
                html += '<td>' + parseInt(m.id) + '</td>';
                html += '<td>' + escapeHtml(m.name) + '</td>';
                html += '<td>' + escapeHtml(m.email) + '</td>';
                html += '<td>' + escapeHtml(m.created_at) + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            box.innerHTML = html;
        }
    };
}

function escapeHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}
</script>

<?php include('../shared/footer.php'); ?>
 