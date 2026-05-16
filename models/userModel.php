<?php
    require_once('/../config/db.php');
    function getUserByEmail($email) {
        $con = getConnection();
        $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $user;
    }

    function getUserById($id) {
        $con = getConnection();
        $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $user;
    }

    function getAllModerators() {
        $con = getConnection();
        $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE role = 'moderator' ORDER BY created_at DESC");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }


    function createUser($name, $email, $password, $role) {
        $con  = getConnection();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($con, "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hash, $role);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

    function deleteModerator($id) {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "DELETE FROM users WHERE id = ? AND role = 'moderator'");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

 
    function updateUserProfile($id, $name, $email) {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "UPDATE users SET name = ?, email = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

       function updateProfilePicture($id, $picPath) {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "UPDATE users SET profile_picture = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $picPath, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

    
    function updatePassword($id, $newPassword) {
        $con  = getConnection();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($con, "UPDATE users SET password_hash = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hash, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }
    function emailExists($email) {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_close($con);
        return $exists;
    }

    
    function countModerators() {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT COUNT(*) as total FROM users WHERE role = 'moderator'");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $row['total'];
    }
    function saveRememberToken($userId, $tokenPlain) {
        $con   = getConnection();
        $hashed = hash('sha256', $tokenPlain);
        $stmt  = mysqli_prepare($con, "UPDATE users SET remember_token_hash = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashed, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

   
    function getUserByRememberToken($tokenPlain) {
        $con    = getConnection();
        $hashed = hash('sha256', $tokenPlain);
        $stmt   = mysqli_prepare($con, "SELECT * FROM users WHERE remember_token_hash = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $hashed);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $user;
    }
    function clearRememberToken($userId) {
        $con  = getConnection();
        $null = null;
        $stmt = mysqli_prepare($con, "UPDATE users SET remember_token_hash = NULL WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_close($con);
    }
?>
 