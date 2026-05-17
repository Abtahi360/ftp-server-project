<?php

    session_start();
    require_once(__DIR__ . '/../models/contentModel.php');

    $action = isset($_GET['action']) ? $_GET['action'] : '';

    
    if ($action === 'download') {
        $id      = intval($_GET['id'] ?? 0);
        $content = getContentById($id);

        if (!$content) {
            header('location: ../views/member/home.php');
            exit;
        }

        // incrementDownload($id);

        // $filePath = __DIR__ . "/../public/uploads/contents/" . $content['file_path'];
        $filePath = __DIR__ . "/../public/uploads/contents/" . basename($content['file_path']);
        if (!file_exists($filePath)) {
    die("Path tried: " . $filePath . " | DB value: " . $content['file_path']);
}
        
$skipCount = isset($_GET['skip_count']) && $_SESSION['role'] ?? '' !== '';

if (!$skipCount) {
    incrementDownload($id);
}
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($content['file_path']) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    ob_clean();
    flush();
    readfile($filePath);
    exit;

    }

    header('location: ../views/member/home.php');
    exit;
?>
