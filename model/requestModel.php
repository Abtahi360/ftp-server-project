<?php
require_once('../config/db.php');
 function addRequest($ip, $contentTitle, $categoryRequested, $message) {
        $con  = getConnection();
        $sql  = "INSERT INTO content_requests (requester_ip, content_title, category_requested, message)
                 VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $ip, $contentTitle, $categoryRequested, $message);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

 
?>