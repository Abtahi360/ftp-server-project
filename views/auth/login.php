<?php
    session_start();
    if (isset($_SESSION['role'])) {
        header('location: ' . ($_SESSION['role'] === 'admin' ? '../admin/dashboard.php' : '../moderator/dashboard.php'));
        exit;
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['csrf_token'];

    if (isset($_SESSION['error'])) {
        $flashError = $_SESSION['error']; unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        $flashSuccess = $_SESSION['success']; unset($_SESSION['success']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login — FTP Media</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>

<div class="login-page">
    <div class="login-card">

        <div class="login-logo">
            <div class="login-logo-icon">💾</div>
            <div class="login-logo-text">
                FTP Media
                <small>ISP Content Portal — Staff</small>
            </div>
        </div>

        <?php if (!empty($flashError)): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>

        <form method="post" action="../../controllers/authController.php?action=login"
              onsubmit="return validateLogin()">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="loginEmail" name="email" placeholder="admin@ftp.local" autocomplete="email" />
                <span class="field-error" id="emailErr"></span>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="loginPass" name="password" placeholder="Min. 8 characters" autocomplete="current-password" />
                <span class="field-error" id="passErr"></span>
            </div>

            <div class="form-group">
                <div class="check-row">
                    <input type="checkbox" name="remember_me" id="rememberMe" value="1" />
                    <label for="rememberMe">Remember me for 30 days</label>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
                🔐 Sign In
            </button>
        </form>

        <div class="form-divider">or</div>

        <p class="form-note">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<script src="../../public/assets/js/validation.js"></script>
<script>
function validateLogin() {
    var ok    = true;
    var email = document.getElementById('loginEmail').value.trim();
    var pass  = document.getElementById('loginPass').value;
    document.getElementById('emailErr').innerText = '';
    document.getElementById('passErr').innerText  = '';
    if (email === '') {
        document.getElementById('emailErr').innerText = 'Email is required.'; ok = false;
    } else if (!validateEmail(email)) {
        document.getElementById('emailErr').innerText = 'Enter a valid email address.'; ok = false;
    }
    if (pass === '') {
        document.getElementById('passErr').innerText = 'Password is required.'; ok = false;
    } else if (pass.length < 8) {
        document.getElementById('passErr').innerText = 'Password must be at least 8 characters.'; ok = false;
    }
    return ok;
}
</script>
</body>
</html>
