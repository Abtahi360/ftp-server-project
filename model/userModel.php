<?php
    require_once('../config/db.php');

    function getUserByEmail($email) {
        $con = getConnection();
        $sql = mysqli_prepare($con, "SELECT * FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($sql, "s", $email);
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $user   = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $user;
    }
    function getUserById($id) {
        $con = getConnection();
        $sql = mysqli_prepare($con, "SELECT * FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($sql, "i", $id);
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $user   = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $user;
    }


    function getAllModerators() {
        $con = getConnection();
        $sql = mysqli_prepare($con, "SELECT * FROM users WHERE role = 'moderator' ORDER BY created_at DESC");
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }



    function createUser($name, $email, $password, $role) {
        $con  = getConnection();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = mysqli_prepare($con, "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($sql, "ssss", $name, $email, $hash, $role);
        $ok = mysqli_stmt_execute($sql);
        mysqli_close($con);
        return $ok;
    }


    function deleteModerator($id) {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "DELETE FROM users WHERE id = ? AND role = 'moderator'");
        mysqli_stmt_bind_param($sql, "i", $id);
        $ok = mysqli_stmt_execute($sql);
        mysqli_close($con);
        return $ok;
    }



    function updateUserProfile($id, $name, $email) {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "UPDATE users SET name = ?, email = ? WHERE id = ?");
        mysqli_stmt_bind_param($sql, "ssi", $name, $email, $id);
        $ok = mysqli_stmt_execute($sql);
        mysqli_close($con);
        return $ok;
    }



    function updateProfilePicture($id, $picPath) {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "UPDATE users SET profile_picture = ? WHERE id = ?");
        mysqli_stmt_bind_param($sql, "si", $picPath, $id);
        $ok = mysqli_stmt_execute($sql);
        mysqli_close($con);
        return $ok;
    }
    function updatePassword($id, $newPassword) {
        $con  = getConnection();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = mysqli_prepare($con, "UPDATE users SET password_hash = ? WHERE id = ?");
        mysqli_stmt_bind_param($sql, "si", $hash, $id);
        $ok = mysqli_stmt_execute($sql);
        mysqli_close($con);
        return $ok;
    }
    function emailExists($email) {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($sql, "s", $email);
        mysqli_stmt_execute($sql);
        mysqli_stmt_store_result($sql);
        $exists = mysqli_stmt_num_rows($sql) > 0;
        mysqli_close($con);
        return $exists;
    }
    function countModerators() {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "SELECT COUNT(*) as total FROM users WHERE role = 'moderator'");
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $row    = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $row['total'];
    }



    
?>