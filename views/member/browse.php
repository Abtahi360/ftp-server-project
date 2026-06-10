<?php
    session_start();
    require_once('../../models/contentModel.php');
    require_once('../../models/categoryModel.php');

    $catId    = intval($_GET['cat'] ?? 0);
    $subCatId = intval($_GET['sub'] ?? 0);
    $topCategories  = getTopCategories();
    $activeId       = $subCatId > 0 ? $subCatId : $catId;
    $contents       = $activeId > 0 ? getContentsByCategory($activeId) : getAllContents();
    $subCategories  = $catId > 0 ? getSubCategories($catId) : [];
    $activeCategory = $catId > 0 ? getCategoryById($catId) : null;

    include('../shared/header.php');
?>

<div class="page-header">
    <h2>📂 Browse Content <?= $activeCategory ? '— <span style="color:var(--accent)">' . htmlspecialchars($activeCategory['name']) . '</span>' : '' ?></h2>
</div>

<!-- Top-level tabs -->
<div class="category-tabs">
    <a href="browse.php" class="cat-tab <?= $catId === 0 ? 'active' : '' ?>">🌐 All</a>
    <?php
    $catIcons = ['Movies'=>'🎬','Music'=>'🎵','Software'=>'💻','eBooks'=>'📚','Games'=>'🎮','TV Series'=>'📺'];
    foreach ($topCategories as $cat):
        $icon = $catIcons[$cat['name']] ?? '📁';
    ?>
        <a href="browse.php?cat=<?= intval($cat['id']) ?>"
           class="cat-tab <?= $catId === intval($cat['id']) ? 'active' : '' ?>">
            <?= $icon ?> <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Sub-category filter -->
<?php if (!empty($subCategories)): ?>
<div class="subcategory-tabs">
    <span style="font-size:12px;font-weight:600;color:var(--text3);margin-right:4px;align-self:center;">Filter:</span>
    <a href="browse.php?cat=<?= $catId ?>" class="sub-tab <?= $subCatId === 0 ? 'active' : '' ?>">
        All <?= htmlspecialchars($activeCategory['name'] ?? '') ?>
    </a>
    <?php foreach ($subCategories as $sub): ?>
        <a href="browse.php?cat=<?= $catId ?>&sub=<?= intval($sub['id']) ?>"
           class="sub-tab <?= $subCatId === intval($sub['id']) ? 'active' : '' ?>">
            <?= htmlspecialchars($sub['name']) ?>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="results-header">
    <span style="background:var(--accent);color:#fff;border-radius:99px;padding:2px 10px;font-size:12.5px;font-weight:700;">
        <?= count($contents) ?>
    </span>
    item<?= count($contents) !== 1 ? 's' : '' ?> found
</div>

<?php if (empty($contents)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No content in this category yet</h3>
        <p>Try a different category or <a href="request_box.php">request it</a>.</p>
    </div>
<?php else: ?>
    <div class="content-grid">
        <?php foreach ($contents as $item): ?>
            <?php
                $ext = strtolower(pathinfo($item['file_path'] ?? '', PATHINFO_EXTENSION));
                $typeMap = ['mp4'=>'ftype-video','avi'=>'ftype-video','mkv'=>'ftype-video',
                            'mp3'=>'ftype-audio','wav'=>'ftype-audio',
                            'pdf'=>'ftype-doc','doc'=>'ftype-doc','docx'=>'ftype-doc',
                            'zip'=>'ftype-zip','jpg'=>'ftype-image','jpeg'=>'ftype-image','png'=>'ftype-image'];
                $typeCls = $typeMap[$ext] ?? 'ftype-other';
            ?>
            <div class="content-card">
                <span class="file-type-badge <?= $typeCls ?>"><?= strtoupper($ext ?: 'FILE') ?></span>
                <div class="card-cat">📁 <?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></div>
                <h4><?= htmlspecialchars($item['title']) ?></h4>
                <p><?= htmlspecialchars(mb_substr($item['description'] ?? '', 0, 100)) ?><?= mb_strlen($item['description'] ?? '') > 100 ? '…' : '' ?></p>
                <div class="card-footer">
                    <div class="card-meta">
                        <span class="dl-count">⬇ <?= intval($item['download_count']) ?></span>
                        <span><?= date('M Y', strtotime($item['uploaded_at'])) ?></span>
                    </div>
                    <a href="../../controllers/memberController.php?action=download&id=<?= intval($item['id']) ?>"
                       class="btn btn-primary btn-sm">⬇ Download</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include('../shared/footer.php'); ?>
