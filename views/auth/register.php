<?php
    session_start();
    if (isset($_SESSION['role'])) {
        header('location: ' . ($_SESSION['role'] === 'admin' ? '../admin/dashboard.php' : '../moderator/dashboard.php')); exit;
    }
    if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
    $csrfToken = $_SESSION['csrf_token'];
    $form = $_SESSION['form'] ?? []; unset($_SESSION['form']);
    $flashError = $_SESSION['error'] ?? null; unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — FTP Media</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
<div class="login-page">
    <div class="login-card" style="max-width:480px">
        <div class="login-logo">
            <div class="login-logo-icon">💾</div>
            <div class="login-logo-text">FTP Media<small>Create Staff Account</small></div>
        </div>

        <?php if ($flashError): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <form method="post" action="../../controllers/authController.php?action=register"
              onsubmit="return validateReg()">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="rName" name="name" value="<?= htmlspecialchars($form['name'] ?? '') ?>" placeholder="Your full name" />
                <span class="field-error" id="rNameErr"></span>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="rEmail" name="email" value="<?= htmlspecialchars($form['email'] ?? '') ?>" placeholder="you@example.com" />
                <span class="field-error" id="rEmailErr"></span>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="moderator" <?= (($form['role'] ?? '') === 'moderator') ? 'selected' : '' ?>>🛡️ Moderator</option>
                    <option value="admin"     <?= (($form['role'] ?? '') === 'admin')     ? 'selected' : '' ?>>⚙️ Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="rPass" name="password" placeholder="Min. 8 characters" />
                <span class="field-error" id="rPassErr"></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="rConf" name="confirm_password" placeholder="Repeat password" />
                <span class="field-error" id="rConfErr"></span>
            </div>
            <button type="submit" name="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
                ✅ Create Account
            </button>
        </form>
        <p class="form-note">Already have an account? <a href="login.php">Sign in here</a></p>
    </div>
</div>
<script src="../../public/assets/js/validation.js"></script>
<script>
function validateReg() {
    var ok=true;
    clearErrors(['rNameErr','rEmailErr','rPassErr','rConfErr']);
    var n=document.getElementById('rName').value.trim();
    var e=document.getElementById('rEmail').value.trim();
    var p=document.getElementById('rPass').value;
    var c=document.getElementById('rConf').value;
    if(!n){showError('rNameErr','Name is required.');ok=false;}
    if(!validateEmail(e)){showError('rEmailErr','Valid email required.');ok=false;}
    if(p.length<8){showError('rPassErr','Minimum 8 characters.');ok=false;}
    if(p!==c){showError('rConfErr','Passwords do not match.');ok=false;}
    return ok;
}
</script>
</body>
</html>
