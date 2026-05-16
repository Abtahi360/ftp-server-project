<?php

    session_start();

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
        header('location: ../auth/login.php');
        exit;
    }

    require_once('../../models/categoryModel.php');
    $categories = getAllCategories();

    include('../shared/header.php');
?>

<div class="form-wrapper">
    <h2>+ Upload Content</h2>

    <form method="post" action="../../controllers/moderatorController.php?action=upload_content"
          enctype="multipart/form-data" onsubmit="return validateModUpload()">

        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

        <div class="form-group">
            <label>Title <span class="required">*</span></label>
            <input type="text" id="upTitle" name="title" placeholder="Content title" />
            <span class="field-error" id="upTitleErr"></span>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Brief description..."></textarea>
        </div>

        <div class="form-group">
            <label>Category <span class="required">*</span></label>
            <select id="upCat" name="category_id">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= intval($cat['id']) ?>">
                        <?= $cat['parent_id'] ? '&nbsp;&nbsp;&mdash; ' : '' ?><?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="upCatErr"></span>
        </div>

        <div class="form-group">
            <label>File <span class="required">*</span>

                <small>Allowed: MP4, AVI, MKV, MP3, WAV, ZIP, PDF, DOC, DOCX, JPG, PNG, GIF &mdash; max 100 MB</small>
            </label>
            <input type="file" id="upFile" name="content_file" />
            <span class="field-error" id="upFileErr"></span>
        </div>

        <input type="submit" name="submit" value="Upload" class="btn btn-primary" />
        <a href="contents.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="../../public/assets/js/validation.js"></script>

<script>
function validateModUpload() {
    var ok    = true;
    var title = document.getElementById('upTitle').value.trim();
    var catSelect = document.getElementById('upCat');
    var catId = catSelect.value;
    var catName = catSelect.options[catSelect.selectedIndex].text.trim().toLowerCase();
    var file  = document.getElementById('upFile');

    clearErrors(['upTitleErr','upCatErr','upFileErr']);

    if (title === '') {
        showError('upTitleErr', 'Title is required.'); ok = false;
    }
    if (catId === '' || catId === '0') {
        showError('upCatErr', 'Please select a category.'); ok = false;
    }
    if (file.files.length === 0) {
        showError('upFileErr', 'Please select a file.'); ok = false;
    } else {
        var fileName = file.files[0].name;
        var ext = fileName.split('.').pop().toLowerCase();
        
    
        var allowed = [];
        var categoryGroup = '';
        var allowedText = '';

        if (catName.includes('movie') || catName.includes('action') || catName.includes('drama')) {
            categoryGroup = 'Movies';
            allowed = ['mp4', 'avi', 'mkv'];
            allowedText = 'MP4, AVI, or MKV';
        } else if (catName.includes('music') || catName.includes('audio') || catName.includes('pop') || catName.includes('rock')) {
            categoryGroup = 'Audio';
            allowed = ['mp3', 'wav'];
            allowedText = 'MP3 or WAV';
        } else if (catName.includes('ebook') || catName.includes('document') || catName.includes('pdf')) {
            categoryGroup = 'Documents / eBooks';
            allowed = ['pdf', 'doc', 'docx'];
            allowedText = 'PDF, DOC, or DOCX';
        } else if (catName.includes('image') || catName.includes('photo') || catName.includes('jpg') || catName.includes('png')) {
            categoryGroup = 'Images';
            allowed = ['jpg', 'jpeg', 'png', 'gif'];
            allowedText = 'JPG, JPEG, PNG, or GIF';
        }

        if (categoryGroup !== '') {
            if (allowed.indexOf(ext) === -1) {
                alert('Invalid file type for ' + categoryGroup + ' category. Please upload only ' + allowedText + ' files.');
                showError('upFileErr', 'Invalid file type for ' + categoryGroup + '.');
                ok = false;
            }
        } else {
           
            var defaultAllowed = ['mp4','avi','mkv','mp3','wav','zip','pdf','doc','docx','jpg','jpeg','png','gif'];
            if (defaultAllowed.indexOf(ext) === -1) {
                showError('upFileErr', 'File type not allowed.');
                ok = false;
            }
        }

        if (ok && file.files[0].size > 100 * 1024 * 1024) {
            showError('upFileErr', 'File must be under 100 MB.');
            ok = false;
        }
    }
    return ok;
}
</script>

<?php include('../shared/footer.php'); ?>
 