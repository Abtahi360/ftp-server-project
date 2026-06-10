<?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php'); exit;
    }
    require_once('../../models/requestModel.php');
    $requests = getAllRequests();
    include('../shared/header.php');
?>

<div class="page-header">
    <h2>📬 Content Requests</h2>
    <span style="font-size:13px;color:var(--text2)"><?= count($requests) ?> total</span>
</div>

<?php if (empty($requests)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No requests yet</h3>
        <p>When members submit content requests, they will appear here.</p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Content Requested</th><th>Category</th><th>Message</th><th>IP</th><th>Status</th><th>Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr id="row-<?= intval($req['id']) ?>">
                            <td style="color:var(--text3)"><?= intval($req['id']) ?></td>
                            <td><strong><?= htmlspecialchars($req['content_title']) ?></strong></td>
                            <td><?= htmlspecialchars($req['category_requested'] ?? '—') ?></td>
                            <td style="max-width:220px;color:var(--text2);font-size:13px">
                                <?= htmlspecialchars(mb_substr($req['message'] ?? '', 0, 70)) ?><?= mb_strlen($req['message'] ?? '') > 70 ? '…' : '' ?>
                            </td>
                            <td style="font-size:12.5px;color:var(--text3)"><?= htmlspecialchars($req['requester_ip'] ?? '—') ?></td>
                            <td id="status-<?= intval($req['id']) ?>">
                                <span class="badge badge-<?= $req['status'] ?>"><?= $req['status'] ?></span>
                            </td>
                            <td style="font-size:13px;color:var(--text2)"><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                            <td>
                                <?php if ($req['status'] === 'pending'): ?>
                                    <div class="action-cell">
                                        <button onclick="updateStatus(<?= intval($req['id']) ?>,'fulfilled')" class="btn btn-success btn-sm">✅</button>
                                        <button onclick="updateStatus(<?= intval($req['id']) ?>,'rejected')"  class="btn btn-danger btn-sm">❌</button>
                                    </div>
                                <?php else: ?>
                                    <span class="muted">Done</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<div id="ajaxMsg" style="display:none;margin-top:14px;"></div>

<script>
function updateStatus(id, status) {
    var xhttp = new XMLHttpRequest();
    xhttp.open('post', '../../api/requests_update.php', true);
    xhttp.setRequestHeader('Content-type', 'application/json');
    xhttp.send(JSON.stringify({ request_id: id, status: status }));
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            var msg = document.getElementById('ajaxMsg');
            msg.style.display = 'block';
            if (resp.success) {
                document.getElementById('status-' + id).innerHTML = '<span class="badge badge-' + status + '">' + status + '</span>';
                var row = document.getElementById('row-' + id);
                row.cells[row.cells.length - 1].innerHTML = '<span class="muted">Done</span>';
                msg.className = 'alert alert-success';
                msg.innerHTML = '✅ ' + resp.message;
            } else {
                msg.className = 'alert alert-error';
                msg.innerHTML = '⚠️ ' + (resp.error || 'Update failed.');
            }
            setTimeout(function(){ msg.style.display='none'; }, 3000);
        }
    };
}
</script>
<?php include('../shared/footer.php'); ?>
