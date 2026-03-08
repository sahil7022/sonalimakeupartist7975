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
$headline = trim($_POST['headline'] ?? '');
$description = trim($_POST['description'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');

if ($id > 0) {
    $stmt = $mysqli->prepare('UPDATE contact_settings SET headline = ?, description = ?, phone = ?, email = ?, address = ?, instagram = ? WHERE id = ?');
    $stmt->bind_param('ssssssi', $headline, $description, $phone, $email, $address, $instagram, $id);
    $stmt->execute();
} else {
    $stmt = $mysqli->prepare('INSERT INTO contact_settings (headline, description, phone, email, address, instagram) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssss', $headline, $description, $phone, $email, $address, $instagram);
    $stmt->execute();
}

header('Location: admin_dashboard.php');
exit;

