<?php
function getModerators($conn) {
    $sql = "SELECT id, name, email, created_at FROM users WHERE role = 'moderator'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addModerator($conn, $name, $email, $pass) {
   
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $role = 'moderator';
    
    $sql = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$name, $email, $hash, $role]);
}
function deleteUser($conn, $uid) {
    $sql = "DELETE FROM users WHERE id = ? AND role = 'moderator'";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$uid]);
}
?>
