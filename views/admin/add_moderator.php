<?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php'); exit;
    }
    include('../shared/header.php');
?>

<div style="max-width:520px">
    <div class="page-header">
        <h2>➕ Add Moderator</h2>
        <a href="moderators.php" class="btn btn-secondary btn-sm">← Back</a>
    </div>

    <div class="card">
        <h3>👤 New Moderator Account</h3>
        <form method="post" action="../../controllers/adminController.php?action=add_moderator"
              onsubmit="return validateMod()">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="modName" name="name" placeholder="Moderator's full name" />
                <span class="field-error" id="modNameErr"></span>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="modEmail" name="email" placeholder="mod@ftp.local" />
                <span class="field-error" id="modEmailErr"></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="modPass" name="password" placeholder="Min. 8 characters" />
                <span class="field-error" id="modPassErr"></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="modConf" name="confirm_password" placeholder="Repeat password" />
                <span class="field-error" id="modConfErr"></span>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" name="submit" class="btn btn-primary">➕ Create Moderator</button>
                <a href="moderators.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="../../public/assets/js/validation.js"></script>
<script>
function validateMod() {
    var ok = true;
    var n  = document.getElementById('modName').value.trim();
    var e  = document.getElementById('modEmail').value.trim();
    var p  = document.getElementById('modPass').value;
    var c  = document.getElementById('modConf').value;
    clearErrors(['modNameErr','modEmailErr','modPassErr','modConfErr']);
    if (!n) { showError('modNameErr','Name is required.'); ok=false; }
    if (!validateEmail(e)) { showError('modEmailErr','Valid email required.'); ok=false; }
    if (p.length < 8) { showError('modPassErr','Min. 8 characters.'); ok=false; }
    if (p !== c) { showError('modConfErr','Passwords do not match.'); ok=false; }
    return ok;
}
</script>
<?php include('../shared/footer.php'); ?>
