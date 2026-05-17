<?php

    require_once(__DIR__ . '/../config/db.php');

    function getAllContents() {
        $con= getConnection();
        $sql= "SELECT c.*, u.name AS uploader_name, cat.name AS category_name
                 FROM contents c
                 LEFT JOIN users u      ON c.uploader_id = u.id
                 LEFT JOIN categories cat ON c.category_id = cat.id
                 ORDER BY c.uploaded_at DESC";
        $stmt= mysqli_prepare($con, $sql);
        mysqli_stmt_execute($stmt);
        $result= mysqli_stmt_get_result($stmt);
        $rows= mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }



    function getContentsByCategory($categoryId) {
        $con= getConnection();
        $sql= "SELECT c.*, u.name AS uploader_name, cat.name AS category_name
                 FROM contents c
                 LEFT JOIN users u        ON c.uploader_id = u.id
                 LEFT JOIN categories cat ON c.category_id  = cat.id
                 WHERE c.category_id = ?
                 ORDER BY c.uploaded_at DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);
        $result= mysqli_stmt_get_result($stmt);
        $rows= mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }


    function getRecentContents($limit = 6) {
        $con= getConnection();
        $sql= "SELECT c.*, cat.name AS category_name
                 FROM contents c
                 LEFT JOIN categories cat ON c.category_id = cat.id
                 ORDER BY c.uploaded_at DESC LIMIT ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows= mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }

function getMostDownloaded($limit = 6) {
        $con= getConnection();
        $sql= "SELECT c.*, cat.name AS category_name
                 FROM contents c
                 LEFT JOIN categories cat ON c.category_id = cat.id
                 ORDER BY c.download_count DESC LIMIT ?";
        $stmt= mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result= mysqli_stmt_get_result($stmt);
        $rows= mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }



    function getContentById($id) {
        $con= getConnection();
        $sql= "SELECT c.*, u.name AS uploader_name, cat.name AS category_name
                 FROM contents c
                 LEFT JOIN users u        ON c.uploader_id = u.id
                 LEFT JOIN categories cat ON c.category_id  = cat.id
                 WHERE c.id = ? LIMIT 1";
        $stmt= mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row= mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $row;
    }
    function searchContents($keyword) {
        $con= getConnection();
        $like = "%" . $keyword . "%";
        $sql= "SELECT c.*, cat.name AS category_name
                 FROM contents c
                 LEFT JOIN categories cat ON c.category_id = cat.id
                 WHERE c.title LIKE ? OR c.description LIKE ?
                 ORDER BY c.uploaded_at DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows= mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($con);
        return $rows;
    }


    function addContent($title, $description, $filePath, $categoryId, $uploaderId) {
        $con  = getConnection();
        $sql  = "INSERT INTO contents (title, description, file_path, category_id, uploader_id)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssii", $title, $description, $filePath, $categoryId, $uploaderId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);
        return $ok;
    }

    function deleteContent($id) {
        $content = getContentById($id);
        if (!$content) return false;

        $con  = getConnection();
        $stmt = mysqli_prepare($con, "DELETE FROM contents WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_close($con);

        return $ok ? $content['file_path'] : false;
    }
    function incrementDownload($id) {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "UPDATE contents SET download_count = download_count + 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_close($con);
    }
    function countContents() {
        $con  = getConnection();
        $stmt = mysqli_prepare($con, "SELECT COUNT(*) as total FROM contents");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $row['total'];
    }
?>
