<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sms_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: user_login.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$stmt = $mysqli->prepare('SELECT id, username, password_hash FROM site_users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_username'] = $user['username'];

    // Log this login for admin visibility
    $logStmt = $mysqli->prepare('INSERT INTO user_logins (user_id) VALUES (?)');
    $logStmt->bind_param('i', $user['id']);
    $logStmt->execute();
    $_SESSION['user_login_log_id'] = $mysqli->insert_id;

    // SMS notification for user login
    sendSmsNotification('User login: ' . $user['username']);

    header('Location: index.php');
    exit;
}

header('Location: user_login.php?error=' . urlencode('Invalid username or password.'));
exit;

