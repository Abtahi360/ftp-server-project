<?php

    session_start();
    include('../shared/header.php');
?>

<h2>&#128269; Search Content</h2>

<div class="search-box">
    <input type="text" id="searchInput" placeholder="Type movie, song, software name..." onkeyup="doSearch()" />
    <button onclick="doSearch()" class="btn btn-primary">Search</button>
</div>

<div id="searchStatus" class="search-status"></div>
<div id="searchResults" class="content-grid"></div>

<script>
var searchTimer = null;

function doSearch() {
    var keyword = document.getElementById('searchInput').value.trim();
    var status  = document.getElementById('searchStatus');
    var results = document.getElementById('searchResults');

    if (keyword.length < 2) {
        status.innerText  = '';
        results.innerHTML = '';
        return;
    }

    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        status.innerText  = 'Searching...';
        results.innerHTML = '';

        var xhttp = new XMLHttpRequest();
        xhttp.open('get', '../../api/search.php?q=' + encodeURIComponent(keyword), true);
        xhttp.send();

        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var resp = JSON.parse(this.responseText);

                if (!resp.success) {
                    status.innerText = resp.error;
                    return;
                }

                status.innerText = resp.count + ' result(s) for "' + resp.keyword + '"';

                if (resp.count === 0) {
                    results.innerHTML = '<p class="empty-msg">No content found. Try a different keyword or <a href="request_box.php">request it</a>.</p>';
                    return;
                }

                var html = '';
                resp.results.forEach(function(item) {
                    html += '<div class="content-card">';
                    html += '  <div class="card-cat">' + escapeHtml(item.category_name || 'Uncategorized') + '</div>';
                    html += '  <h4>' + escapeHtml(item.title) + '</h4>';
                    var desc = item.description ? item.description.substring(0, 100) : '';
                    html += '  <p>' + escapeHtml(desc) + (item.description && item.description.length > 100 ? '...' : '') + '</p>';
                    html += '  <div class="card-meta">';
                    html += '    <span>&#11015; ' + parseInt(item.download_count) + ' downloads</span>';
                    html += '  </div>';
                    html += '  <a href="../../controllers/memberController.php?action=download&id=' + parseInt(item.id) + '" class="btn btn-sm">&#11015; Download</a>';
                    html += '</div>';
                });
                results.innerHTML = html;
            }
        };
    }, 400);
}

function escapeHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}
</script>

<?php include('../shared/footer.php'); ?>
