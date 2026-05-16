<?php


    session_start();

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
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
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $req): ?>
                <tr id="row-<?= intval($req['id']) ?>">
                    <td><?= intval($req['id']) ?></td>
                    <td><?= htmlspecialchars($req['content_title']) ?></td>
                    <td><?= htmlspecialchars($req['category_requested'] ?? '—') ?></td>
                    <td><?= htmlspecialchars(substr($req['message'] ?? '', 0, 60)) ?><?= strlen($req['message'] ?? '') > 60 ? '...' : '' ?></td>
                    <td id="status-<?= intval($req['id']) ?>">
                        <span class="badge badge-<?= $req['status'] ?>"><?= $req['status'] ?></span>
                    </td>
                    <td><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                    <td>
                        <?php if ($req['status'] === 'pending'): ?>
                            <button onclick="updateStatus(<?= intval($req['id']) ?>, 'fulfilled')"
                                    class="btn btn-sm btn-success">&#10003; Fulfill</button>
                            <button onclick="updateStatus(<?= intval($req['id']) ?>, 'rejected')"
                                    class="btn btn-sm btn-danger">&#10007; Reject</button>
                        <?php else: ?>
                            <span class="muted">Done</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div id="ajaxMsg" class="alert" style="display:none; margin-top:12px;"></div>

<script>

function updateStatus(id, status) {
    var data = JSON.stringify({ request_id: id, status: status });

    var xhttp = new XMLHttpRequest();
    xhttp.open('post', '../../api/requests_update.php', true);
    xhttp.setRequestHeader('Content-type', 'application/json');
    xhttp.send(data);

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            var msg  = document.getElementById('ajaxMsg');
            msg.style.display = 'block';

            if (resp.success) {
                
                var cell = document.getElementById('status-' + id);
                cell.innerHTML = '<span class="badge badge-' + status + '">' + status + '</span>';
                
                var row = document.getElementById('row-' + id);
                var btns = row.querySelectorAll('button');
                btns.forEach(function(b){ b.style.display = 'none'; });
                var td = row.cells[row.cells.length - 1];
                td.innerHTML = '<span class="muted">Done</span>';

                msg.className = 'alert alert-success';
                msg.innerText = resp.message;
            } else {
                msg.className = 'alert alert-error';
                msg.innerText = resp.error || 'Update failed.';
            }

          
            setTimeout(function(){ msg.style.display = 'none'; }, 3000);
        }
    };
}
</script>

<?php include('../shared/footer.php'); ?>
 