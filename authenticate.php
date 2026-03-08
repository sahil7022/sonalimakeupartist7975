<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sms_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// 1) Simple hard-coded fallback: allow sahil / 2005 even if DB is misconfigured
if ($username === 'sahil' && $password === '2005') {
    // Ensure there is at least a row in admins for sahil, but don't depend on it for login
    $defaultPasswordHash = password_hash('2005', PASSWORD_BCRYPT);
    $ensureStmt = $mysqli->prepare('INSERT IGNORE INTO admins (username, password_hash) VALUES (?, ?)');
    $ensureStmt->bind_param('ss', $username, $defaultPasswordHash);
    $ensureStmt->execute();

    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_username'] = 'sahil';

    // SMS notification for admin login
    sendSmsNotification('Admin login: sahil');

    header('Location: admin_dashboard.php');
    exit;
}

// 2) Normal DB-based admin login (for any future admins)

// Look up requested admin
$stmt = $mysqli->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin && password_verify($password, $admin['password_hash'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];

    // SMS notification for admin login
    sendSmsNotification('Admin login: ' . $admin['username']);
    header('Location: admin_dashboard.php');
    exit;
}

header('Location: login.php?error=' . urlencode('Invalid username or password.'));
exit;

