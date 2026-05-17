<?php
    header('Content-Type: application/json');
    require_once(__DIR__ . '/../models/contentModel.php');

    $keyword = trim($_GET['q'] ?? '');

    if ($keyword === '') {
        echo json_encode(['success' => false, 'error' => 'Search keyword is required.']);
        exit;
    }

    if (strlen($keyword) < 2) {
        echo json_encode(['success' => false, 'error' => 'Keyword must be at least 2 characters.']);
        exit;
    }
    $results = searchContents($keyword);
    echo json_encode([
        'success' => true,
        'keyword' => htmlspecialchars($keyword),
        'count'   => count($results),
        'results' => $results
    ]);
?>
