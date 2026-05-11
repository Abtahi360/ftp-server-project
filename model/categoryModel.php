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

        

?>