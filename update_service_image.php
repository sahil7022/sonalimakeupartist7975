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

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0 || empty($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: admin_dashboard.php');
    exit;
}

$fileTmp  = $_FILES['image_file']['tmp_name'];
$fileName = $_FILES['image_file']['name'];
$fileInfo = pathinfo($fileName);
$ext      = strtolower($fileInfo['extension'] ?? '');

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($ext, $allowed, true)) {
    header('Location: admin_dashboard.php');
    exit;
}

$uploadDir = __DIR__ . '/uploads/services';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileInfo['filename']);
$newName  = 'service_' . $id . '_' . time() . '_' . $safeBase . '.' . $ext;
$destPath = $uploadDir . '/' . $newName;

if (!move_uploaded_file($fileTmp, $destPath)) {
    header('Location: admin_dashboard.php');
    exit;
}

// Path used by the frontend
$publicPath = 'uploads/services/' . $newName;

$stmt = $mysqli->prepare('UPDATE services SET image_url = ? WHERE id = ?');
$stmt->bind_param('si', $publicPath, $id);
$stmt->execute();

header('Location: admin_dashboard.php');
exit;

