<?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php'); exit;
    }
    require_once('../../models/userModel.php');
    $moderators = getAllModerators();
    include('../shared/header.php');
?>

<div class="page-header">
    <h2>👥 Moderators</h2>
    <a href="add_moderator.php" class="btn btn-primary">➕ Add Moderator</a>
</div>

<?php if (empty($moderators)): ?>
    <div class="empty-state">
        <div class="empty-icon">👤</div>
        <h3>No moderators yet</h3>
        <p><a href="add_moderator.php">Add the first moderator</a></p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <div class="table-header">
            <h3>👥 All Moderators <span style="font-weight:400;color:var(--text3);font-size:13px">(<?= count($moderators) ?> total)</span></h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($moderators as $mod): ?>
                        <tr>
                            <td style="color:var(--text3)"><?= intval($mod['id']) ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#a78bfa);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0">
                                        <?= strtoupper(substr($mod['name'], 0, 1)) ?>
                                    </div>
                                    <strong><?= htmlspecialchars($mod['name']) ?></strong>
                                </div>
                            </td>
                            <td style="color:var(--text2)"><?= htmlspecialchars($mod['email']) ?></td>
                            <td><span class="badge badge-moderator">🛡️ Moderator</span></td>
                            <td style="color:var(--text2);font-size:13px"><?= date('M d, Y', strtotime($mod['created_at'])) ?></td>
                            <td>
                                <a href="../../controllers/adminController.php?action=delete_moderator&id=<?= intval($mod['id']) ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Remove moderator <?= htmlspecialchars($mod['name'], ENT_QUOTES) ?>?')">🗑 Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include('../shared/footer.php'); ?>
