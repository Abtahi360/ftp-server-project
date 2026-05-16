<?php

    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php');
        exit;
    }

    require_once('../../models/requestModel.php');
    $requests = getAllRequests();

    include('../shared/header.php');
?>


<h2>&#128231; Content Requests</h2>

<?php if (empty($requests)): ?>
    <p class="empty-msg">No requests yet.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title Requested</th>
                <th>Category</th>
                <th>Message</th>
                <th>IP</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?= intval($req['id']) ?></td>
                    <td><?= htmlspecialchars($req['content_title']) ?></td>
                    <td><?= htmlspecialchars($req['category_requested'] ?? '—') ?></td>
                    <td><?= htmlspecialchars(substr($req['message'] ?? '', 0, 60)) ?><?= strlen($req['message'] ?? '') > 60 ? '...' : '' ?></td>
                    <td><?= htmlspecialchars($req['requester_ip'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $req['status'] ?>"><?= $req['status'] ?></span></td>
                    <td><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                    <td>
                        <button onclick="updateStatus(<?= intval($req['id']) ?>, 'fulfilled')" class="btn btn-sm btn-success">Fulfill</button>
                        <button onclick="updateStatus(<?= intval($req['id']) ?>, 'rejected')"  class="btn btn-sm btn-danger">Reject</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
 




<script>

function updateStatus(id, status) {
    if (!confirm('Mark request #' + id + ' as ' + status + '?')) return;

    let data = JSON.stringify({ request_id: id, status: status });

    let xhttp = new XMLHttpRequest();
    xhttp.open('post', '../../api/requests_update.php', true);
    xhttp.setRequestHeader('Content-type', 'application/json');
    xhttp.send(data);

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let resp = JSON.parse(this.responseText);
            if (resp.success) {
                alert(resp.message);
                location.reload();
            } else {
                alert('Error: ' + resp.error);
            }
        }
    };
}
</script>

<?php include('../shared/footer.php'); ?>
 