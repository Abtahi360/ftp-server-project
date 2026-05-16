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
 