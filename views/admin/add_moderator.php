<?php

    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('location: ../auth/login.php');
        exit;
    }
    include('../shared/header.php');
?>
<div class="form-wrapper">
    <h2>+ Add Moderator</h2>
    <form method="post" action="../../controllers/adminController.php?action=add_moderator"
          onsubmit="return validateModerator()">
  
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="modName" name="name" value="" placeholder="Moderator full name" />
            <span class="field-error" id="modNameErr"></span>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" id="modEmail" name="email" value="" placeholder="mod@ftp.local" />
            <span class="field-error" id="modEmailErr"></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" id="modPass" name="password" placeholder="Min. 8 characters" />
            <span class="field-error" id="modPassErr"></span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" id="modConfirm" name="confirm_password" />
            <span class="field-error" id="modConfirmErr"></span>
        </div>
        <input type="submit" name="submit" value="Add Moderator" class="btn btn-primary" />
        <a href="moderators.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script src="../../public/assets/js/validation.js"></script>
<script>
function validateModerator() {
    let ok= true;
    let name= document.getElementById('modName').value.trim();
    let email= document.getElementById('modEmail').value.trim();
    let pass= document.getElementById('modPass').value;
    let confirm= document.getElementById('modConfirm').value;
    clearErrors(['modNameErr','modEmailErr','modPassErr','modConfirmErr']);
    if (name === '') {
        showError('modNameErr', 'Name is required.'); ok = false;
    }
    if (!validateEmail(email)) {
        showError('modEmailErr', 'Valid email required.'); ok = false;
    }
    if (pass.length < 8) {
        showError('modPassErr', 'Min 8 characters.'); ok = false;
    }
    if (pass !== confirm) {
        showError('modConfirmErr', 'Passwords do not match.'); ok = false;
    }
    return ok;
}
</script>
<?php include('../shared/footer.php'); ?>
 
 