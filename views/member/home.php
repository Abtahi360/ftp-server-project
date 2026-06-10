<?php
    session_start();
    require_once('../../models/contentModel.php');
    require_once('../../models/categoryModel.php');

    $recent  = getRecentContents(6);
    $topDown = getMostDownloaded(6);

    include('../shared/header.php');

    function fileTypeBadge($filePath) {
        $ext = strtolower(pathinfo($filePath ?? '', PATHINFO_EXTENSION));
        $map = [
            'mp4'=>['video','ftype-video'], 'avi'=>['video','ftype-video'], 'mkv'=>['video','ftype-video'],
            'mp3'=>['audio','ftype-audio'], 'wav'=>['audio','ftype-audio'],
            'pdf'=>['doc','ftype-doc'], 'doc'=>['doc','ftype-doc'], 'docx'=>['doc','ftype-doc'],
            'zip'=>['zip','ftype-zip'],
            'jpg'=>['image','ftype-image'], 'jpeg'=>['image','ftype-image'], 'png'=>['image','ftype-image'],
        ];
        $info = $map[$ext] ?? ['file', 'ftype-other'];
        return "<span class='file-type-badge {$info[1]}'>" . strtoupper($ext ?: 'FILE') . "</span>";
    }
?>

<section class="hero">
    <div class="hero-badge">🌐 ISP Media Portal</div>
    <h1>Your Media.<br><span class="highlight">All in One Place.</span></h1>
    <p>Browse, stream, and download movies, music, software, eBooks, and more — free for all subscribers.</p>
    <div class="hero-actions">
        <a href="browse.php" class="btn btn-primary btn-lg">📂 Browse Library</a>
        <a href="search.php" class="btn btn-outline btn-lg">🔍 Search Content</a>
    </div>
</section>

<!-- Category tabs (AJAX loaded) -->
<div class="section">
    <div class="section-header">
        <h2 class="section-title">📁 Categories</h2>
        <a href="browse.php" class="section-link">View all →</a>
    </div>
    <div id="categoryTabs" class="category-tabs">
        <div class="loading-pulse"><span></span><span></span><span></span></div>
    </div>
</div>

<!-- Recently Added -->
<div class="section">
    <div class="section-header">
        <h2 class="section-title">🆕 Recently Added</h2>
        <a href="browse.php" class="section-link">See all →</a>
    </div>

    <?php if (empty($recent)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>Nothing uploaded yet</h3>
            <p>Check back soon — content is being added.</p>
        </div>
    <?php else: ?>
        <div class="content-grid">
            <?php foreach ($recent as $item): ?>
                <div class="content-card">
                    <?= fileTypeBadge($item['file_path'] ?? '') ?>
                    <div class="card-cat">📁 <?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></div>
                    <h4><?= htmlspecialchars($item['title']) ?></h4>
                    <p><?= htmlspecialchars(mb_substr($item['description'] ?? 'No description available.', 0, 90)) ?>…</p>
                    <div class="card-footer">
                        <div class="card-meta">
                            <span class="dl-count">⬇ <?= intval($item['download_count']) ?></span>
                        </div>
                        <a href="../../controllers/memberController.php?action=download&id=<?= intval($item['id']) ?>"
                           class="btn btn-primary btn-sm">⬇ Download</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Most Downloaded -->
<div class="section">
    <div class="section-header">
        <h2 class="section-title">🔥 Most Downloaded</h2>
    </div>
    <?php if (empty($topDown)): ?>
        <p class="empty-msg">No downloads recorded yet.</p>
    <?php else: ?>
        <div class="content-grid">
            <?php foreach ($topDown as $item): ?>
                <div class="content-card">
                    <?= fileTypeBadge($item['file_path'] ?? '') ?>
                    <div class="card-cat">📁 <?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></div>
                    <h4><?= htmlspecialchars($item['title']) ?></h4>
                    <p><?= htmlspecialchars(mb_substr($item['description'] ?? '', 0, 90)) ?><?= strlen($item['description'] ?? '') > 90 ? '…' : '' ?></p>
                    <div class="card-footer">
                        <div class="card-meta">
                            <span class="dl-count">🔥 <?= intval($item['download_count']) ?> downloads</span>
                        </div>
                        <a href="../../controllers/memberController.php?action=download&id=<?= intval($item['id']) ?>"
                           class="btn btn-primary btn-sm">⬇ Download</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Request banner -->
<div style="background:linear-gradient(135deg,#1e1b4b,#312e81);border-radius:16px;padding:36px;text-align:center;margin:32px 0;">
    <h3 style="color:#fff;font-size:20px;margin-bottom:10px;">Can't find what you're looking for?</h3>
    <p style="color:rgba(255,255,255,.6);margin-bottom:20px;">Submit a content request and our moderators will try to add it.</p>
    <a href="request_box.php" class="btn btn-primary">📬 Request Content</a>
</div>

<script>
function loadCategories() {
    var xhttp = new XMLHttpRequest();
    xhttp.open('get', '../../api/categories.php', true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            var box  = document.getElementById('categoryTabs');
            box.innerHTML = '';
            if (resp.success && resp.categories.length > 0) {
                var icons = {'Movies':'🎬','Music':'🎵','Software':'💻','eBooks':'📚','Games':'🎮','TV Series':'📺'};
                resp.categories.forEach(function(cat) {
                    var a = document.createElement('a');
                    a.href = 'browse.php?cat=' + cat.id;
                    a.className = 'cat-tab';
                    var icon = icons[cat.name] || '📁';
                    a.innerHTML = icon + ' ' + cat.name;
                    box.appendChild(a);
                });
            } else {
                box.innerHTML = '<span class="muted">No categories found.</span>';
            }
        }
    };
}
loadCategories();
</script>

<?php include('../shared/footer.php'); ?>
