<?php
    session_start();
    include('../shared/header.php');
?>

<div class="page-header">
    <h2>🔍 Search Content</h2>
</div>

<div class="search-wrapper">
    <div class="search-box">
        <input type="text" id="searchInput"
               placeholder="Search by title, description, or keyword…"
               oninput="doSearch()" autocomplete="off" />
        <button onclick="doSearch()" class="btn btn-primary">🔍 Search</button>
    </div>
    <p class="search-hint">Type at least 2 characters to see results. Results update as you type.</p>
    <div id="searchStatus" class="search-status"></div>
</div>

<div id="searchResults"></div>

<script>
var searchTimer = null;

function doSearch() {
    var keyword = document.getElementById('searchInput').value.trim();
    var status  = document.getElementById('searchStatus');
    var results = document.getElementById('searchResults');

    if (keyword.length < 2) {
        status.innerHTML = '';
        results.innerHTML = '';
        return;
    }

    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        status.innerHTML = '<div class="loading-pulse"><span></span><span></span><span></span></div>';
        results.innerHTML = '';

        var xhttp = new XMLHttpRequest();
        xhttp.open('get', '../../api/search.php?q=' + encodeURIComponent(keyword), true);
        xhttp.send();

        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var resp = JSON.parse(this.responseText);

                if (!resp.success) {
                    status.innerHTML = '<span style="color:var(--red)">' + escapeHtml(resp.error) + '</span>';
                    return;
                }

                status.innerHTML = '<span style="font-weight:600;color:var(--text)">' + resp.count + '</span> result' + (resp.count !== 1 ? 's' : '') + ' for <em>"' + escapeHtml(resp.keyword) + '"</em>';

                if (resp.count === 0) {
                    results.innerHTML = '<div class="empty-state"><div class="empty-icon">🔍</div><h3>No results found</h3><p>Try different keywords or <a href="request_box.php">request this content</a>.</p></div>';
                    return;
                }

                var html = '<div class="content-grid">';
                resp.results.forEach(function(item) {
                    var ext = (item.file_path || '').split('.').pop().toLowerCase();
                    var typeMap = {'mp4':'ftype-video','avi':'ftype-video','mkv':'ftype-video','mp3':'ftype-audio','wav':'ftype-audio','pdf':'ftype-doc','doc':'ftype-doc','docx':'ftype-doc','zip':'ftype-zip','jpg':'ftype-image','jpeg':'ftype-image','png':'ftype-image'};
                    var cls = typeMap[ext] || 'ftype-other';
                    var desc = item.description ? item.description.substring(0, 100) : 'No description.';
                    html += '<div class="content-card">';
                    html += '<span class="file-type-badge ' + cls + '">' + (ext ? ext.toUpperCase() : 'FILE') + '</span>';
                    html += '<div class="card-cat">📁 ' + escapeHtml(item.category_name || 'Uncategorized') + '</div>';
                    html += '<h4>' + escapeHtml(item.title) + '</h4>';
                    html += '<p>' + escapeHtml(desc) + (item.description && item.description.length > 100 ? '…' : '') + '</p>';
                    html += '<div class="card-footer"><div class="card-meta"><span class="dl-count">⬇ ' + parseInt(item.download_count) + '</span></div>';
                    html += '<a href="../../controllers/memberController.php?action=download&id=' + parseInt(item.id) + '" class="btn btn-primary btn-sm">⬇ Download</a></div>';
                    html += '</div>';
                });
                html += '</div>';
                results.innerHTML = html;
            }
        };
    }, 350);
}

function escapeHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}
</script>

<?php include('../shared/footer.php'); ?>
