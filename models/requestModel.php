<?php

    require_once(__DIR__ . '/../config/db.php');

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


    function getAllRequests() {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT * FROM content_requests ORDER BY created_at DESC");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }

    function getPendingRequests() {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT * FROM content_requests WHERE status = 'pending' ORDER BY created_at DESC");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }

    function updateRequestStatus($id, $status) {
        $allowed = ['pending', 'fulfilled', 'rejected'];
        if (!in_array($status, $allowed)) return false;

        $con  = getConnection();
        $stmt = mysqli_prepare($con, "UPDATE content_requests SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

    function countPendingRequests() {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT COUNT(*) as total FROM content_requests WHERE status = 'pending'");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $row['total'];
    }
?>
 