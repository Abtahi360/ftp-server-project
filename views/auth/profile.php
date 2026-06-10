<?php
    session_start();
    if (!isset($_SESSION['user_id'])) { header('location: login.php'); exit; }
    require_once('../../models/userModel.php');
    $user = getUserById($_SESSION['user_id']);
    include('../shared/header.php');
?>

<div class="page-header">
    <h2>👤 My Profile</h2>
    <span class="badge <?= $_SESSION['role'] === 'admin' ? 'badge-admin' : 'badge-moderator' ?>">
        <?= $_SESSION['role'] === 'admin' ? '⚙️ Admin' : '🛡️ Moderator' ?>
    </span>
</div>

<div class="profile-grid">

    <!-- Account Details -->
    <div class="card">
        <h3>✏️ Account Information</h3>

        <div class="profile-avatar-wrap">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="../../public/uploads/profiles/<?= htmlspecialchars($user['profile_picture']) ?>"
                     alt="Profile Picture" class="profile-pic" />
            <?php else: ?>
                <div class="profile-pic-placeholder">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="profile-info-text">
                <h3><?= htmlspecialchars($user['name']) ?></h3>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>
        </div>

        <form method="post" action="../../controllers/authController.php?action=update_profile"
              enctype="multipart/form-data" onsubmit="return validateProfile()">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="profName" name="name" value="<?= htmlspecialchars($user['name']) ?>" />
                <span class="field-error" id="profNameErr"></span>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="profEmail" name="email" value="<?= htmlspecialchars($user['email']) ?>" />
                <span class="field-error" id="profEmailErr"></span>
            </div>
            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?= ucfirst(htmlspecialchars($user['role'])) ?>" readonly class="readonly-field" />
            </div>
            <div class="form-group">
                <label>Profile Picture <small style="color:var(--text3)">(JPG/PNG/GIF, max 2 MB)</small></label>
                <input type="file" name="profile_picture" accept="image/*" />
            </div>
            <button type="submit" name="submit" class="btn btn-primary">💾 Save Changes</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="card">
        <h3>🔒 Change Password</h3>
        <form method="post" action="../../controllers/authController.php?action=change_password"
              onsubmit="return validatePassChange()">
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
            <button type="submit" name="submit" class="btn btn-warning">🔑 Update Password</button>
        </form>
    </div>
</div>

<script src="../../public/assets/js/validation.js"></script>
<script>
function validateProfile() {
    var ok=true; clearErrors(['profNameErr','profEmailErr']);
    var n=document.getElementById('profName').value.trim();
    var e=document.getElementById('profEmail').value.trim();
    if(!n){showError('profNameErr','Name required.');ok=false;}
    if(!validateEmail(e)){showError('profEmailErr','Valid email required.');ok=false;}
    return ok;
}
function validatePassChange() {
    var ok=true; clearErrors(['curPassErr','newPassErr','confPassErr']);
    var c=document.getElementById('curPass').value;
    var n=document.getElementById('newPass').value;
    var cf=document.getElementById('confPass').value;
    if(!c){showError('curPassErr','Enter current password.');ok=false;}
    if(n.length<8){showError('newPassErr','Min. 8 characters.');ok=false;}
    if(n!==cf){showError('confPassErr','Passwords do not match.');ok=false;}
    return ok;
}
</script>
<?php include('../shared/footer.php'); ?>
