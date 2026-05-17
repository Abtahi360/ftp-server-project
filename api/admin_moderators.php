<?php

    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
        exit;
    }

    require_once(__DIR__ . '/../models/userModel.php');

    $moderators = getAllModerators();

    
    foreach ($moderators as &$m) {
        unset($m['password_hash']);
    }

    echo json_encode(['success'    => true, 'moderators' => $moderators, 'count'      => count($moderators)]);
?>
