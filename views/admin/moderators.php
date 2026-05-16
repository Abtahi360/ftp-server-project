<?php

    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php');
        exit;
    }

    require_once('../../models/userModel.php');
    $moderators = getAllModerators();

    include('../shared/header.php');
?>

<div class="page-header">
    <h2>&#128100; Moderators</h2>
    <a href="add_moderator.php" class="btn btn-primary">+ Add Moderator</a>
</div>

<?php if (empty($moderators)): ?>
    <p class="empty-msg">No moderators registered yet.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($moderators as $mod): ?>
                <tr>
                    <td><?= intval($mod['id']) ?></td>
                    <td><?= htmlspecialchars($mod['name']) ?></td>
                    <td><?= htmlspecialchars($mod['email']) ?></td>
                    <td><?= htmlspecialchars($mod['created_at']) ?></td>
                    <td>
                        <a href="../../controllers/adminController.php?action=delete_moderator&id=<?= intval($mod['id']) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete moderator <?= htmlspecialchars($mod['name'], ENT_QUOTES) ?>?')">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include('../shared/footer.php'); ?>
 