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

if ($id > 0) {
    $stmt = $mysqli->prepare('DELETE FROM services WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

header('Location: admin_dashboard.php');
exit;

