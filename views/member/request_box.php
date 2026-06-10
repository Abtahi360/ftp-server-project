<?php
    session_start();
    require_once('../../models/categoryModel.php');
    $categories = getAllCategories();
    include('../shared/header.php');
?>

<div class="page-header">
    <h2>📬 Request Content</h2>
</div>

<div style="max-width:620px">
    <div class="card" style="margin-bottom:20px;border-left:4px solid var(--accent);">
        <p style="color:var(--text2);font-size:14px">Can't find what you're looking for in the library? Fill out the form below and our team will try to add it. Requests are usually reviewed within 24–48 hours.</p>
    </div>

    <div class="card request-form-wrap">
        <h3>📝 Submit a Request</h3>
        <form id="requestForm" onsubmit="return submitRequest(event)">

            <div class="form-group">
                <label>Content Title <span class="required">*</span></label>
                <input type="text" id="reqTitle" name="content_title"
                       placeholder="e.g. Inception (2010), Photoshop 2024, Harry Potter eBook" />
                <span class="field-error" id="reqTitleErr"></span>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select id="reqCat" name="category_requested">
                    <option value="">— Select a category (optional) —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['name']) ?>">
                            <?= $cat['parent_id'] ? '&nbsp;&nbsp;⤷ ' : '📁 ' ?><?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Additional Message</label>
                <textarea id="reqMsg" name="message" placeholder="Any extra details — year, language, format, etc."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">📤 Submit Request</button>
        </form>
        <div id="reqFeedback" class="feedback-msg" style="display:none;"></div>
    </div>
</div>

<script>
function submitRequest(e) {
    e.preventDefault();
    var title    = document.getElementById('reqTitle').value.trim();
    var category = document.getElementById('reqCat').value;
    var message  = document.getElementById('reqMsg').value.trim();
    var feedback = document.getElementById('reqFeedback');
    document.getElementById('reqTitleErr').innerText = '';

    if (title === '') {
        document.getElementById('reqTitleErr').innerText = 'Content title is required.';
        return false;
    }

    var btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerText = '⏳ Submitting…';

    var xhttp = new XMLHttpRequest();
    xhttp.open('post', '../../api/request_add.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('content_title=' + encodeURIComponent(title) +
               '&category_requested=' + encodeURIComponent(category) +
               '&message=' + encodeURIComponent(message));

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            feedback.style.display = 'block';
            btn.disabled = false;
            btn.innerText = '📤 Submit Request';
            if (resp.success) {
                feedback.className = 'feedback-msg alert alert-success';
                feedback.innerHTML = '✅ ' + resp.message;
                document.getElementById('requestForm').reset();
            } else {
                feedback.className = 'feedback-msg alert alert-error';
                feedback.innerHTML = '⚠️ ' + (resp.error || 'Submission failed.');
            }
        }
    };
    return false;
}
</script>
<?php include('../shared/footer.php'); ?>
