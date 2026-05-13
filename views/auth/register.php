<?php
    session_start();

    $form = $_SESSION['form'] ?? [];
    unset($_SESSION['form']);

    include('../shared/header.php');
?>

<div class="form-wrapper">
    <h2>Create Account</h2>

    <form method="post" action="../../controllers/authController.php?action=register" onsubmit="return validateRegister()">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />
        
        
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="regName" name="name" value="<?= htmlspecialchars($form['name'] ?? '') ?>" placeholder="Your full name" />
            <span class="field-error" id="nameErr"></span>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" id="regEmail" name="email" value="<?= htmlspecialchars($form['email'] ?? '') ?>" placeholder="you@example.com" />
            <span class="field-error" id="emailErr"></span>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" id="regRole">
                <option value="moderator" <?= (($form['role'] ?? '') === 'moderator') ? 'selected' : '' ?>>Moderator</option>
                <option value="admin"     <?= (($form['role'] ?? '') === 'admin')     ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" id="regPass" name="password" placeholder="Min. 8 characters" />
            <span class="field-error" id="passErr"></span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" id="regConfirm" name="confirm_password" placeholder="Repeat password" />
            <span class="field-error" id="confirmErr"></span>
        </div>

        <input type="submit" name="submit" value="Register" class="btn btn-primary" />
    </form>
    <p class="form-note">Already have an account? <a href="login.php">Login here</a></p>
</div>
<script src="../../public/assets/js/validation.js"></script>


<script>
function validateRegister() {
    let ok      = true;
    let name    = document.getElementById('regName').value.trim();
    let email   = document.getElementById('regEmail').value.trim();
    let pass    = document.getElementById('regPass').value;
    let confirm = document.getElementById('regConfirm').value;

    clearErrors(['nameErr','emailErr','passErr','confirmErr']);

    if (name === '') {
        showError('nameErr', 'Name is required.'); ok = false;
    }
    
    
    if (email === '') {
        showError('emailErr', 'Email is required.');
        ok = false;
    } else if (!validateEmail(email)) {
        showError('emailErr', 'Invalid email address.'); 
        ok = false;
    }
    
    
    if (pass.length < 8) {
        showError('passErr', 'Minimum 8 characters required.');
        ok = false;
    }
    
    if (pass !== confirm) {
        showError('confirmErr', 'Passwords do not match.');
        ok = false;
    }

    return ok;
}
</script>

<?php 
include('../shared/footer.php'); 
?>
