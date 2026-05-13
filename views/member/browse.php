<?php

    session_start();
    require_once('../../models/contentModel.php');
    require_once('../../models/categoryModel.php');

    $catId    = intval($_GET['cat'] ?? 0);
    $subCatId = intval($_GET['sub'] ?? 0);

    $topCategories = getTopCategories();

    $activeId = $subCatId > 0 ? $subCatId : $catId;
    $contents = $activeId > 0 ? getContentsByCategory($activeId) : getAllContents();

    $subCategories = $catId > 0 ? getSubCategories($catId) : [];
    $activeCategory = $catId > 0 ? getCategoryById($catId) : null;

    include('../shared/header.php');
?>

<h2>Browse Content</h2>


<div class="category-tabs">
    <a href="browse.php" class="cat-tab <?= $catId === 0 ? 'active' : '' ?>">All</a>
    <?php foreach ($topCategories as $cat): ?>
        <a href="browse.php?cat=<?= intval($cat['id']) ?>"
           class="cat-tab <?= $catId === intval($cat['id']) ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (!empty($subCategories)): ?>
<div class="subcategory-tabs">
    <a href="browse.php?cat=<?= $catId ?>" class="sub-tab <?= $subCatId === 0 ? 'active' : '' ?>">All <?= htmlspecialchars($activeCategory['name'] ?? '') ?></a>
    <?php foreach ($subCategories as $sub): ?>
        <a href="browse.php?cat=<?= $catId ?>&sub=<?= intval($sub['id']) ?>"
           class="sub-tab <?= $subCatId === intval($sub['id']) ? 'active' : '' ?>">
            <?= htmlspecialchars($sub['name']) ?>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="results-header">
    <span><?= count($contents) ?> item(s) found</span>
</div>

<?php if (empty($contents)): ?>
    <p class="empty-msg">No content found in this category.</p>
<?php else: ?>
    <div class="content-grid">
        <?php foreach ($contents as $item): ?>
            <div class="content-card">
                <div class="card-cat"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></div>
                <h4><?= htmlspecialchars($item['title']) ?></h4>
                <p><?= htmlspecialchars(substr($item['description'] ?? '', 0, 100)) ?><?= strlen($item['description'] ?? '') > 100 ? '...' : '' ?></p>
                <div class="card-meta">
                    <span>&#11015; <?= intval($item['download_count']) ?> downloads</span>
                    <span><?= date('M d, Y', strtotime($item['uploaded_at'])) ?></span>
                </div>
                <a href="../../controllers/memberController.php?action=download&id=<?= intval($item['id']) ?>"
                   class="btn btn-sm">&#11015; Download</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include('../shared/footer.php'); ?>
