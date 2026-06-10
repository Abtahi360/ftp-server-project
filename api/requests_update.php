<?php
    require_once(__DIR__ . '/../models/requestModel.php');


    session_start();
    header('Content-Type: application/json');

    
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'POST method required.']);
        exit;
    }


    $data =json_decode(file_get_contents('php://input'), true);
    $id  =intval($data['request_id'] ?? 0);
    $status =trim($data['status'] ?? '');

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid request ID.']);
        exit;
    }

    if (!in_array($status, ['pending', 'fulfilled', 'rejected'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid status value.']);
        exit;
    }

    $ok =updateRequestStatus($id, $status);

    echo json_encode([
        'success' => $ok,
        'message'=> $ok ? "Status updated to '$status'." : 'Update failed.'
    ]);
?>
