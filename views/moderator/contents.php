<?php
    session_start();
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin','moderator'])) {
        header('location: ../auth/login.php'); exit;
    }
    require_once('../../models/contentModel.php');
    $contents = getAllContents();
    include('../shared/header.php');
?>

<div class="page-header">
    <h2>📁 All Contents</h2>
    <a href="upload_content.php" class="btn btn-primary">⬆️ Upload Content</a>
</div>

<?php if (empty($contents)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No files uploaded yet</h3>
        <p><a href="upload_content.php">Upload the first file</a></p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <div class="table-header">
            <h3>📋 Library <span style="font-weight:400;color:var(--text3);font-size:13px">(<?= count($contents) ?> files)</span></h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Title</th><th>Category</th><th>Uploader</th><th>Downloads</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($contents as $c): ?>
                        <?php
                            $ext = strtolower(pathinfo($c['file_path'] ?? '', PATHINFO_EXTENSION));
                            $typeMap = ['mp4'=>'ftype-video','avi'=>'ftype-video','mkv'=>'ftype-video','mp3'=>'ftype-audio','wav'=>'ftype-audio','pdf'=>'ftype-doc','doc'=>'ftype-doc','docx'=>'ftype-doc','zip'=>'ftype-zip','jpg'=>'ftype-image','jpeg'=>'ftype-image','png'=>'ftype-image'];
                            $cls = $typeMap[$ext] ?? 'ftype-other';
                            $isOwn = ($c['uploader_id'] == $_SESSION['user_id']);
                            $canDelete = ($_SESSION['role'] === 'admin' || $isOwn);
                        ?>
                        <tr>
                            <td style="color:var(--text3)"><?= intval($c['id']) ?></td>
                            <td>
                                <span class="file-type-badge <?= $cls ?>" style="margin-right:6px"><?= strtoupper($ext ?: '?') ?></span>
                                <strong><?= htmlspecialchars($c['title']) ?></strong>
                                <?= $isOwn ? '<span style="font-size:11px;color:var(--accent);margin-left:5px">Mine</span>' : '' ?>
                            </td>
                            <td><?= htmlspecialchars($c['category_name'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($c['uploader_name'] ?? '—') ?></td>
                            <td><span class="dl-count" style="font-size:13px;font-weight:700;color:var(--green)">⬇ <?= intval($c['download_count']) ?></span></td>
                            <td style="font-size:13px;color:var(--text2)"><?= date('M d, Y', strtotime($c['uploaded_at'])) ?></td>
                            <td>
                                <div class="action-cell">
                                    <a href="../../controllers/memberController.php?action=download&id=<?= intval($c['id']) ?>" class="btn btn-secondary btn-sm">⬇</a>
                                    <?php if ($canDelete): ?>
                                        <a href="../../controllers/moderatorController.php?action=delete_content&id=<?= intval($c['id']) ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Delete this content?')">🗑</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php include('../shared/footer.php'); ?>
