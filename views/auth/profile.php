<?php

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('location: login.php');
        exit;
    }

    require_once('../../models/userModel.php');
    $user = getUserById($_SESSION['user_id']);

    include('../shared/header.php');
?>

<h2>My Profile</h2>

<div class="profile-grid">
    <div class="card">
        <h3>Account Information</h3>

        <?php if (!empty($user['profile_picture'])): ?>
            <div style="text-align:center; margin-bottom:12px;">
                <img src="../../public/uploads/profiles/<?= htmlspecialchars($user['profile_picture']) ?>"
                     alt="Profile Picture" class="profile-pic" />
            </div>
        <?php
        endif;
        ?>

        <form method="post" action="../../controllers/authController.php?action=update_profile"
              enctype="multipart/form-data" onsubmit="return validateProfile()">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="profName" name="name" value="<?= htmlspecialchars($user['name']) ?>" />
                <span class="field-error" id="profNameErr"></span>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="profEmail" name="email" value="<?= htmlspecialchars($user['email']) ?>" />
                <span class="field-error" id="profEmailErr"></span>
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?= htmlspecialchars($user['role']) ?>" readonly class="readonly-field" />
            </div>

            <div class="form-group">
                <label>Profile Picture (JPG/PNG/GIF, max 2 MB)</label>
                <input type="file" name="profile_picture" accept="image/*" />
            </div>

            <input type="submit" name="submit" value="Update Profile" class="btn btn-primary" />
        </form>
    </div>

    <div class="card">
        <h3>Change Password</h3>

        <form method="post" action="../../controllers/authController.php?action=change_password"
              onsubmit="return validatePasswordChange()">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

            <div class="form-group">
                <label>Current Password</label>
                <input type="password" id="curPass" name="current_password" />
                <span class="field-error" id="curPassErr"></span>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" id="newPass" name="new_password" placeholder="Min. 8 characters" />
                <span class="field-error" id="newPassErr"></span>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" id="confPass" name="confirm_new" />
                <span class="field-error" id="confPassErr"></span>
            </div>

            <input type="submit" name="submit" value="Change Password" class="btn btn-warning" />
        </form>
    </div>
</div>

<script src="../../public/assets/js/validation.js"></script>

<script>
function validateProfile() {
    let ok= true;
    let name= document.getElementById('profName').value.trim();
    let email= document.getElementById('profEmail').value.trim();

    clearErrors(['profNameErr','profEmailErr']);

    if (name==='') {
        showError('profNameErr', 'Name is required.'); ok = false;
    }
    
    if (!validateEmail(email)) {
        showError('profEmailErr', 'Valid email is required.'); ok = false;
    }
    return ok;
}

function validatePasswordChange() {
    let ok= true;
    let cur= document.getElementById('curPass').value;
    let nw= document.getElementById('newPass').value;
    let confirm= document.getElementById('confPass').value;

    clearErrors(['curPassErr','newPassErr','confPassErr']);

    if (cur === '') {
        showError('curPassErr', 'Current password required..!'); ok = false;
    }

    if (nw.length < 8) {
        showError('newPassErr', 'Min 8 characters..!'); ok = false;
    }

    if (nw !== confirm) {
        showError('confPassErr', 'Passwords do not match..!'); ok = false;
    }
    return ok;
}
</script>

<?php
include('../shared/footer.php'); 
?>
