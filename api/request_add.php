<?php
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'POST method required.']);
        exit;
    }
    require_once('/../models/requestModel.php');

    $contentTitle      = trim($_POST['content_title'] ?? '');
    $categoryRequested = trim($_POST['category_requested'] ?? '');
    $message           = trim($_POST['message'] ?? '');
    $ip                = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if ($contentTitle === '') {
        echo json_encode(['success' => false, 'error' => 'Content title is required.']);
        exit;
    }

    if (strlen($contentTitle) > 255) {
        echo json_encode(['success' => false, 'error' => 'Title too long (max 255 chars).']);
        exit;
    }

    $ok = addRequest($ip, $contentTitle, $categoryRequested, $message);

    echo json_encode([
        'success' => $ok,
        'message' => $ok ? 'Your request has been submitted. Thank you!' : 'Submission failed. Please try again.'
    ]);
?>
