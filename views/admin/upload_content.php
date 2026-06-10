<?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php'); exit;
    }
    require_once('../../models/categoryModel.php');
    $categories = getAllCategories();
    include('../shared/header.php');
?>

<div style="max-width:580px">
    <div class="page-header">
        <h2>⬆️ Upload Content</h2>
        <a href="contents.php" class="btn btn-secondary btn-sm">← Back</a>
    </div>

    <div class="card">
        <h3>📄 New Media File</h3>
        <form method="post" action="../../controllers/adminController.php?action=upload_content"
              enctype="multipart/form-data" onsubmit="return validateUpload()">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

            <div class="form-group">
                <label>Title <span class="required">*</span></label>
                <input type="text" id="upTitle" name="title" placeholder="Content title" />
                <span class="field-error" id="upTitleErr"></span>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Brief description of this file…"></textarea>
            </div>
            <div class="form-group">
                <label>Category <span class="required">*</span></label>
                <select id="upCat" name="category_id">
                    <option value="">— Select a category —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= intval($cat['id']) ?>">
                            <?= $cat['parent_id'] ? '&nbsp;&nbsp;⤷ ' : '📁 ' ?><?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="field-error" id="upCatErr"></span>
            </div>
            <div class="form-group">
                <label>File <span class="required">*</span></label>
                <input type="file" id="upFile" name="content_file" />
                <small style="color:var(--text3);font-size:12px;display:block;margin-top:5px">
                    Allowed: MP4, AVI, MKV, MP3, WAV, ZIP, PDF, DOC, DOCX, JPG, PNG, GIF — max 100 MB
                </small>
                <span class="field-error" id="upFileErr"></span>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" name="submit" class="btn btn-primary">⬆️ Upload File</button>
                <a href="contents.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="../../public/assets/js/validation.js"></script>
<script>
function validateUpload() {
    var ok = true;
    var t  = document.getElementById('upTitle').value.trim();
    var c  = document.getElementById('upCat').value;
    var f  = document.getElementById('upFile');
    clearErrors(['upTitleErr','upCatErr','upFileErr']);
    if (!t) { showError('upTitleErr','Title is required.'); ok=false; }
    if (!c || c === '0') { showError('upCatErr','Select a category.'); ok=false; }
    if (f.files.length === 0) { showError('upFileErr','Select a file to upload.'); ok=false; }
    else {
        var allowed = ['mp4','avi','mkv','mp3','wav','zip','pdf','doc','docx','jpg','jpeg','png','gif'];
        var ext = f.files[0].name.split('.').pop().toLowerCase();
        if (allowed.indexOf(ext) === -1) { showError('upFileErr','File type not allowed.'); ok=false; }
        else if (f.files[0].size > 100*1024*1024) { showError('upFileErr','File must be under 100 MB.'); ok=false; }
    }
    return ok;
}
</script>
<?php include('../shared/footer.php'); ?>
