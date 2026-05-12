<?php
    session_start();

    if (isset($_SESSION['role'])) {
        header('location: ' . ($_SESSION['role'] === 'admin' ? '../admin/dashboard.php' : '../moderator/dashboard.php'));
        exit;
    }

    include('../shared/header.php');
?>

<div class="form-wrapper">
    <h2>Staff Login</h2>

    <form method="post" action="../../controllers/authController.php?action=login" onsubmit="return validateLogin()">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

        <div class="form-group">
            <label>Email</label>
            <input type="email" id="loginEmail" name="email" value="" placeholder="admin@ftp.local" />
            <span class="field-error" id="emailErr"></span>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" id="loginPass" name="password" value="" placeholder="Min. 8 characters" />
            <span class="field-error" id="passErr"></span>
        </div>

        <div class="form-group" style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="remember_me" id="rememberMe" value="1" />
            <label for="rememberMe" style="font-weight:normal; margin-bottom:0;">Remember me for 30 days</label>
        </div>

        <input type="submit" name="submit" value="Login" class="btn btn-primary" />
    </form>

    <p class="form-note">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<script src="../../public/assets/js/validation.js"></script>

<script>
function validateLogin() {
    var ok= true;
    var email= document.getElementById('loginEmail').value.trim();
    var pass= document.getElementById('loginPass').value;

    document.getElementById('emailErr').innerText = '';
    document.getElementById('passErr').innerText  = '';

    if (email === '') {
        document.getElementById('emailErr').innerText = 'Email is required.'; ok = false;
    } else if (!validateEmail(email)) {
        document.getElementById('emailErr').innerText = 'Enter a valid email.'; ok = false;
    }

    if (pass === '') {
        document.getElementById('passErr').innerText = 'Password is required.'; ok = false;
    }
    
    else if (pass.length < 8) {
        document.getElementById('passErr').innerText = 'Password must be at least 8 characters.'; ok = false;
    }

    return ok;
}
</script>

<?php
include('../shared/footer.php'); 
?>
