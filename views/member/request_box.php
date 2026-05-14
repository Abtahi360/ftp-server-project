<?php
    session_start();
    require_once('../../models/categoryModel.php');
    $categories = getAllCategories();

    include('../shared/header.php');
?>

<h2>&#128231; Request Content</h2>
<p>Can't find what you're looking for? Submit a request and our team will try to add it.</p>

<div class="form-wrapper">
    <form id="requestForm" onsubmit="return submitRequest(event)">

        <div class="form-group">
            <label>Content Title <span class="required">*</span></label>
            <input type="text" id="reqTitle" name="content_title" placeholder="e.g. Inception (2010)" />
            <span class="field-error" id="reqTitleErr"></span>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select id="reqCategory" name="category_requested">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['name']) ?>">
                        <?= $cat['parent_id'] ? '&nbsp;&nbsp;&mdash; ' : '' ?><?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Message (optional)</label>
            <textarea id="reqMessage" name="message" rows="4" placeholder="Any extra details about the content you want..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>

    <div id="reqFeedback" class="feedback-msg" style="display:none;"></div>
</div>

<script>
function submitRequest(e) {
    e.preventDefault();

    var title    = document.getElementById('reqTitle').value.trim();
    var category = document.getElementById('reqCategory').value;
    var message  = document.getElementById('reqMessage').value.trim();
    var feedback = document.getElementById('reqFeedback');

    document.getElementById('reqTitleErr').innerText = '';
    if (title === '') {
        document.getElementById('reqTitleErr').innerText = 'Content title is required.';
        return false;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.open('post', '../../api/request_add.php', true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(
        'content_title='    + encodeURIComponent(title) +
        '&category_requested=' + encodeURIComponent(category) +
        '&message='         + encodeURIComponent(message)
    );

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            feedback.style.display = 'block';

            if (resp.success) {
                feedback.className = 'feedback-msg alert alert-success';
                feedback.innerText = resp.message;
                document.getElementById('requestForm').reset();
            } else {
                feedback.className = 'feedback-msg alert alert-error';
                feedback.innerText = resp.error || 'Submission failed.';
            }
        }
    };

    return false;
}
</script>

<?php include('../shared/footer.php'); ?>
