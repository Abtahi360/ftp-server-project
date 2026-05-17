<?php

    session_start();
    require_once('../../models/contentModel.php');
    require_once('../../models/categoryModel.php');

    $recent = getRecentContents(6);
    $topDown = getMostDownloaded(6);
    $categories = getTopCategories();

    include('../shared/header.php');
?>

<section class="hero">
    <h1>&#128190; ISP Media Content Portal</h1>
    <p>Browse, download, and request media content - movies, music, software, eBooks, and more.</p>
    <a href="search.php" class="btn btn-primary">&#128269; Search Content</a>
    <a href="browse.php" class="btn btn-secondary">Browse All</a>
</section>

<section class="section">
    <h2>Categories</h2>
    <div id="categoryTabs" class="category-tabs">
        <span class="loading-text">Loading categories...</span>
    </div>
</section>

<section class="section">
    <h2>Recently Added</h2>
    <?php if (empty($recent)): ?>
        <p class="empty-msg">No content uploaded yet.</p>
    <?php else: ?>
        <div class="content-grid">
            <?php foreach ($recent as $item): ?>
                <div class="content-card">
                    <div class="card-cat"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></div>
                    <h4><?= htmlspecialchars($item['title']) ?></h4>
                    <p><?= htmlspecialchars(substr($item['description'] ?? '', 0, 80)) ?>...</p>
                    <div class="card-meta">
                        <span>&#11015; <?= intval($item['download_count']) ?> downloads</span>
                        <a href="../../controllers/memberController.php?action=download&id=<?= intval($item['id']) ?>"
                           class="btn btn-sm">Download</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>


<section class="section">
    <h2>&#128293; Most Downloaded</h2>
    <?php if (empty($topDown)): ?>
        <p class="empty-msg">No content yet.</p>
    <?php else: ?>
        <div class="content-grid">
            <?php foreach ($topDown as $item): ?>
                <div class="content-card">
                    <div class="card-cat"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></div>
                    <h4><?= htmlspecialchars($item['title']) ?></h4>
                    <div class="card-meta">
                        <span>&#11015; <?= intval($item['download_count']) ?> downloads</span>
                        <a href="../../controllers/memberController.php?action=download&id=<?= intval($item['id']) ?>"
                           class="btn btn-sm">Download</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

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
                resp.categories.forEach(function(cat) {
                    var a = document.createElement('a');
                    a.href = 'browse.php?cat=' + cat.id;
                    a.className = 'cat-tab';
                    a.innerText = cat.name;
                    box.appendChild(a);
                });
            } else {
                box.innerHTML = '<span>No categories found.</span>';
            }
        }
    };
}

loadCategories();
</script>

<?php include('../shared/footer.php'); ?>
