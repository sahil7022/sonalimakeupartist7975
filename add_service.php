<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_dashboard.php');
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$image_url = trim($_POST['image_url'] ?? '');
$sort_order = (int)($_POST['sort_order'] ?? 0);

if ($title === '') {
    header('Location: admin_dashboard.php');
    exit;
}

// Optional image upload
if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmp  = $_FILES['image_file']['tmp_name'];
    $fileName = $_FILES['image_file']['name'];
    $fileInfo = pathinfo($fileName);
    $ext      = strtolower($fileInfo['extension'] ?? '');

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed, true)) {
        $uploadDir = __DIR__ . '/uploads/services';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileInfo['filename']);
        $newName  = 'service_new_' . time() . '_' . $safeBase . '.' . $ext;
        $destPath = $uploadDir . '/' . $newName;

        if (move_uploaded_file($fileTmp, $destPath)) {
            $image_url = 'uploads/services/' . $newName;
        }
    }
}

$stmt = $mysqli->prepare('INSERT INTO services (title, description, image_url, sort_order, is_active) VALUES (?, ?, ?, ?, 1)');
$stmt->bind_param('sssi', $title, $description, $image_url, $sort_order);
$stmt->execute();

header('Location: admin_dashboard.php');
exit;

