<?php


    session_start();

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
        header('location: ../auth/login.php');
        exit;
    }

    require_once('../../models/contentModel.php');
    $contents = getAllContents();

    include('../shared/header.php');
?>

<div class="page-header">
    <h2>&#128190; All Contents</h2>
    <a href="upload_content.php" class="btn btn-primary">+ Upload Content</a>
</div>

<?php if (empty($contents)): ?>
    <p class="empty-msg">No contents uploaded yet.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Uploader</th>
                <th>Downloads</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contents as $c): ?>
                <tr>
                    <td><?= intval($c['id']) ?></td>
                    <td><?= htmlspecialchars($c['title']) ?></td>
                    <td><?= htmlspecialchars($c['category_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['uploader_name'] ?? '—') ?></td>
                    <td><?= intval($c['download_count']) ?></td>
                    <td><?= date('M d, Y', strtotime($c['uploaded_at'])) ?></td>
                    <td>
                        <a href="../../controllers/memberController.php?action=download&id=<?= intval($c['id']) ?>"
                           class="btn btn-sm">Download</a>

                        <?php if ($_SESSION['role'] === 'admin' || $c['uploader_id'] == $_SESSION['user_id']): ?>
                            <a href="../../controllers/moderatorController.php?action=delete_content&id=<?= intval($c['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this content?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include('../shared/footer.php'); ?>
 