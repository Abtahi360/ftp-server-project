<?php
require_once('../config/db.php');

function getTopCategories() {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
}

    function getSubCategories($parentId) {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC");
        mysqli_stmt_bind_param($sql, "i", $parentId);
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }

      function countCategories() {
        $con  = getConnection();
        $sql = mysqli_prepare($con, "SELECT COUNT(*) as total FROM categories");
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $row    = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $row['total'];
    } 
 

 function getAllCategories() {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT * FROM categories ORDER BY parent_id ASC, name ASC");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }

    function getCategoryById($id) {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT * FROM categories WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $row;
    }
        

?>