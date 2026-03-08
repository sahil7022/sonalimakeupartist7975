<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['services'])) {
    header('Location: admin_dashboard.php');
    exit;
}

foreach ($_POST['services'] as $id => $data) {
    $id = (int)$id;
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $image_url = trim($data['image_url'] ?? '');
    $sort_order = (int)($data['sort_order'] ?? 0);
    $is_active = isset($data['is_active']) ? 1 : 0;

    if ($title === '') {
        continue;
    }

    // If a new file was uploaded for this service, handle it and override image_url
    $fileField = 'image_file_' . $id;
    if (!empty($_FILES[$fileField]) && $_FILES[$fileField]['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES[$fileField]['tmp_name'];
        $fileName = $_FILES[$fileField]['name'];
        $fileInfo = pathinfo($fileName);
        $ext      = strtolower($fileInfo['extension'] ?? '');

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed, true)) {
            $uploadDir = __DIR__ . '/uploads/services';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileInfo['filename']);
            $newName  = 'service_' . $id . '_' . time() . '_' . $safeBase . '.' . $ext;
            $destPath = $uploadDir . '/' . $newName;

            if (move_uploaded_file($fileTmp, $destPath)) {
                $image_url = 'uploads/services/' . $newName;
            }
        }
    }

    $stmt = $mysqli->prepare('UPDATE services SET title = ?, description = ?, image_url = ?, sort_order = ?, is_active = ? WHERE id = ?');
    $stmt->bind_param('sssiii', $title, $description, $image_url, $sort_order, $is_active, $id);
    $stmt->execute();
}

header('Location: admin_dashboard.php');
exit;

