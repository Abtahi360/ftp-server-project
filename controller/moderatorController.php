<?php

    session_start();
    require_once('/../models/contentModel.php');
    require_once('/../models/requestModel.php');
    require_once('/../models/categoryModel.php');

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
        header('location: ../views/auth/login.php');
        exit;
    }

    $action= isset($_GET['action']) ? $_GET['action'] : '';

    function verifyCsrfMod() {
        $submitted= $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || $submitted !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Invalid form submission. Please try again.";
            return false;
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return true;
    }

    
    if ($action === 'upload_content') {
        if (!isset($_POST['submit'])) {
            header('location: ../views/moderator/upload_content.php');
            exit;
        }

        if (!verifyCsrfMod()) {
            header('location: ../views/moderator/upload_content.php');
            exit;
        }

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId  = intval($_POST['category_id'] ?? 0);

        $errors = [];
        if ($title === '')    $errors[] = "Title is required.";
        if ($categoryId <= 0) $errors[] = "Please select a category.";

        if (!isset($_FILES['content_file']) || $_FILES['content_file']['error'] !== 0) {
            $errors[] = "A file is required.";
        } else {
            $file  = $_FILES['content_file'];
            $maxSize = 100 * 1024 * 1024;
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            
            $finfo  = finfo_open(FILEINFO_MIME_TYPE);
            $detectedMime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            
            $cat  = getCategoryById($categoryId);
            $catName = strtolower($cat['name'] ?? '');
            
            $allowedExt = [];
            $allowedMime = [];
            $catGroup   = '';

            if (strpos($catName, 'movie') !== false || strpos($catName, 'action') !== false || strpos($catName, 'drama') !== false) {
                $catGroup    = 'Movies';
                $allowedExt  = ['mp4', 'avi', 'mkv'];
                $allowedMime = ['video/mp4', 'video/x-msvideo', 'video/x-matroska'];
            } else if (strpos($catName, 'music') !== false || strpos($catName, 'audio') !== false || strpos($catName, 'pop') !== false || strpos($catName, 'rock') !== false) {
                $catGroup    = 'Audio';
                $allowedExt  = ['mp3', 'wav'];
                $allowedMime = ['audio/mpeg', 'audio/wav', 'audio/x-wav'];
            } else if (strpos($catName, 'ebook') !== false || strpos($catName, 'document') !== false || strpos($catName, 'pdf') !== false) {
                $catGroup    = 'Documents / eBooks';
                $allowedExt  = ['pdf', 'doc', 'docx'];
                $allowedMime = [
                    'application/pdf', 
                    'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
            } else if (strpos($catName, 'image') !== false || strpos($catName, 'photo') !== false || strpos($catName, 'jpg') !== false || strpos($catName, 'png') !== false) {
                $catGroup = 'Images';
                $allowedExt  = ['jpg', 'jpeg', 'png', 'gif'];
                $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];
            } else {
                
                $allowedExt = ['mp4','avi','mkv','mp3','wav','zip','pdf','doc','docx','jpg','jpeg','png','gif'];
                $allowedMime = [
                    'video/mp4','video/x-msvideo','video/x-matroska',
                    'audio/mpeg','audio/wav','audio/x-wav',
                    'application/zip','application/x-zip-compressed',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg','image/png','image/gif',
                    'application/octet-stream'
                ];
            }

            if (!in_array($ext, $allowedExt)) $errors[] = $catGroup ? "Invalid file extension for $catGroup category." : "File extension not allowed.";
            if (!in_array($detectedMime, $allowedMime)) $errors[] = $catGroup ? "Invalid file type for $catGroup category." : "File type not permitted.";
            if ($file['size'] > $maxSize) $errors[] = "File must be under 100 MB.";
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('location: ../views/moderator/upload_content.php');
            exit;
        }

        $ext  = strtolower(pathinfo($_FILES['content_file']['name'], PATHINFO_EXTENSION));
        $filename = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        $dest  = __DIR__ . "/../public/uploads/contents/" . $filename;

        if (!move_uploaded_file($_FILES['content_file']['tmp_name'], $dest)) {
            $_SESSION['error'] = "File upload failed.";
            header('location: ../views/moderator/upload_content.php');
            exit;
        }

        $ok = addContent($title, $description, $filename, $categoryId, $_SESSION['user_id']);
        if ($ok) {
            $_SESSION['success'] = "Content uploaded successfully.";
            header('location: ../views/moderator/contents.php');
        } else {
            unlink($dest);
            $_SESSION['error'] = "Database insert failed.";
            header('location: ../views/moderator/upload_content.php');
        }
        exit;
    }

    
    if ($action === 'delete_content') {
        $id  = intval($_GET['id'] ?? 0);
        $content = getContentById($id);

        if ($content && ($_SESSION['role'] === 'admin' || $content['uploader_id'] == $_SESSION['user_id'])) {
            $filePath = deleteContent($id);
            if ($filePath) {
                $fullPath = __DIR__ . "/../public/uploads/contents/" . $filePath;
                if (file_exists($fullPath)) unlink($fullPath);
                $_SESSION['success'] = "Content deleted.";
            } else {
                $_SESSION['error'] = "Delete failed.";
            }
        } else {
            $_SESSION['error'] = "You can only delete your own content.";
        }
        header('location: ../views/moderator/contents.php');
        exit;
    }

    if ($action === 'update_request') {
        if (!verifyCsrfMod()) {
            header('location: ../views/moderator/requests.php');
            exit;
        }

        $id  = intval($_POST['request_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if ($id > 0 && in_array($status, ['pending', 'fulfilled', 'rejected'])) {
            $ok = updateRequestStatus($id, $status);
            $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Status updated." : "Update failed.";
        } else {
            $_SESSION['error'] = "Invalid request data.";
        }
        header('location: ../views/moderator/requests.php');
        exit;
    }

   
    header('location: ../views/moderator/dashboard.php');
    exit;
?>
 