<?php
session_start();
require_once(__DIR__ . '/../models/userModel.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';


    function generateCsrfToken() {

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];

    }

    function verifyCsrfToken() {
        $submitted = $_POST['csrf_token'] ?? '';

        if (empty($_SESSION['csrf_token']) || $submitted !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Invalid form submission. Please try again.";
            return false;

        }


        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return true;
    }
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $cookieToken = $_COOKIE['remember_token'];

        if (strlen($cookieToken) === 64) {
            $user = getUserByRememberToken($cookieToken);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['pic']     = $user['profile_picture'];

                setcookie('remember_token', $cookieToken, time() + (30 * 24 * 3600), '/', '', false, true);
            } else {
                setcookie('remember_token', '', time() - 3600, '/');
                unset($_COOKIE['remember_token']);
            }
        }
    }



    if ($action === 'login') {
        if (!isset($_POST['submit'])) {
            header('location: ../views/auth/login.php');
            exit;
        }

        if (!verifyCsrfToken()) {
            header('location: ../views/auth/login.php');
            exit;
        }

        $email= trim($_POST['email'] ?? '');
        $password= $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = "Email and password are required.";
            header('location: ../views/auth/login.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            header('location: ../views/auth/login.php');
            exit;
        }

        $user = getUserByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['pic']     = $user['profile_picture'];

            if (isset($_POST['remember_me'])) {
                $tokenPlain = bin2hex(random_bytes(32)); 
                saveRememberToken($user['id'], $tokenPlain);
                setcookie('remember_token', $tokenPlain, time() + (30 * 24 * 3600), '/', '', false, true);
            }

            if ($user['role'] === 'admin') {
                header('location: ../views/admin/dashboard.php');
            } else {
                header('location: ../views/moderator/dashboard.php');
            }
            exit;
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header('location: ../views/auth/login.php');
            exit;
        }
    }

    // REGISTER
    if ($action === 'register') {
        if (!isset($_POST['submit'])) {
            header('location: ../views/auth/register.php');
            exit;
        }

        if (!verifyCsrfToken()) {
            header('location: ../views/auth/register.php');
            exit;
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $role     = $_POST['role'] ?? 'moderator';


        $errors = [];
        if ($name === ''){
            $errors[] = "Name is required.";
        }

        if ($email === '')
            $errors[] = "Email is required.";

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = "Invalid email format.";

        if (strlen($password) < 8)
            $errors[] = "Password must be at least 8 characters.";
        if ($password !== $confirm) 
            $errors[] = "Passwords do not match.";

        if (!in_array($role, ['admin', 'moderator']))
            $errors[] = "Invalid role.";

        if (emailExists($email))
            $errors[] = "Email is already registered.";

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            $_SESSION['form']  = ['name' => $name, 'email' => $email, 'role' => $role];
            header('location: ../views/auth/register.php');
            exit;
        }

        $ok = createUser($name, $email, $password, $role);

        if ($ok) {
            $_SESSION['success'] = "Account created successfully. Please login.";
            header('location: ../views/auth/login.php');
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
            header('location: ../views/auth/register.php');
        }
        exit;
    }

    // LOGOUT
    if ($action === 'logout') {
        if (isset($_SESSION['user_id'])) {
            clearRememberToken($_SESSION['user_id']);
        }
        setcookie('remember_token', '', time() - 3600, '/');
        session_unset();
        session_destroy();
        header('location: ../views/auth/login.php');
        exit;
    }

    // UPDATE PROFILE
    if ($action === 'update_profile') {
        if (!isset($_SESSION['user_id'])) {
            header('location: ../views/auth/login.php');
            exit;
        }

        if (!verifyCsrfToken()) {
            header('location: ../views/auth/profile.php');
            exit;
        }

        $id    = $_SESSION['user_id'];
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];
        if ($name === '')  $errors[] = "Name is required.";
        if ($email === '') $errors[] = "Email is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";

        $existing = getUserByEmail($email);
        if ($existing && $existing['id'] != $id) {
            $errors[] = "Email is already in use by another account.";
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('location: ../views/auth/profile.php');
            exit;
        }

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $file    = $_FILES['profile_picture'];
            $maxSize = 2 * 1024 * 1024;

            $finfo        = finfo_open(FILEINFO_MIME_TYPE);
            $detectedMime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];
            $allowedExt  = ['jpg', 'jpeg', 'png', 'gif'];
            $ext         = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($detectedMime, $allowedMime) || !in_array($ext, $allowedExt)) {
                $_SESSION['error'] = "Profile picture must be a JPG, PNG, or GIF image.";
                header('location: ../views/auth/profile.php');
                exit;
            }

            if ($file['size'] > $maxSize) {
                $_SESSION['error'] = "Profile picture must be under 2 MB.";
                header('location: ../views/auth/profile.php');
                exit;
            }

            $uploadDir = __DIR__ . "/../public/uploads/profiles/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $filename = "profile_" . $id . "_" . time() . "." . $ext;
            $dest     = $uploadDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $_SESSION['error'] = "Failed to save profile picture.";
                header('location: ../views/auth/profile.php');
                exit;
            }

            updateProfilePicture($id, $filename);
            $_SESSION['pic'] = $filename;
        }

        updateUserProfile($id, $name, $email);
        $_SESSION['name']    = $name;
        $_SESSION['email']   = $email;
        $_SESSION['success'] = "Profile updated successfully.";
        header('location: ../views/auth/profile.php');
        exit;
    }



    if ($action === 'change_password') {
        if (!isset($_SESSION['user_id'])) {
            header('location: ../views/auth/login.php');
            exit;
        }

        if (!verifyCsrfToken()) {
            header('location: ../views/auth/profile.php');
            exit;
        }

        $id      = $_SESSION['user_id'];
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_new'] ?? '';

        $user = getUserById($id);

        if (!password_verify($current, $user['password_hash'])) {
            $_SESSION['error'] = "Current password is incorrect.";
            header('location: ../views/auth/profile.php');
            exit;
        }

        if (strlen($new) < 8) {
            $_SESSION['error'] = "New password must be at least 8 characters.";
            header('location: ../views/auth/profile.php');
            exit;
        }

        if ($new !== $confirm) {
            $_SESSION['error'] = "New passwords do not match.";
            header('location: ../views/auth/profile.php');
            exit;
        }

        updatePassword($id, $new);
        $_SESSION['success'] = "Password changed successfully.";
        header('location: ../views/auth/profile.php');
        exit;
    }



    header('location: ../views/auth/login.php');
    exit;
?>
