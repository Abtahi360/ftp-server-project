<?php
function getContentsForAdmin($conn) {

    $sql = "SELECT contents.*, users.name as uploader_name 
            FROM contents 
            JOIN users ON contents.uploader_id = users.id 
            ORDER BY uploaded_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function saveContentRecord($conn, $title, $desc, $path, $cat_id, $up_id) {
    $sql = "INSERT INTO contents (title, description, file_path, category_id, uploader_id) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$title, $desc, $path, $cat_id, $up_id]);
}

function removeContentRecord($conn, $cid) {

    $stmt_path = $conn->prepare("SELECT file_path FROM contents WHERE id = ?");
    $stmt_path->execute([$cid]);
    $path = $stmt_path->fetchColumn();

    $stmt_del = $conn->prepare("DELETE FROM contents WHERE id = ?");
    if($stmt_del->execute([$cid])) {
        return $path; 
    }
    return false;
}
?>
