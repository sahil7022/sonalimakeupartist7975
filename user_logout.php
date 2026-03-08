<?php
session_start();
require_once __DIR__ . '/db.php';

// Mark logout time if we have a login log row
if (!empty($_SESSION['user_login_log_id'])) {
    $logId = (int)$_SESSION['user_login_log_id'];
    $stmt = $mysqli->prepare('UPDATE user_logins SET logout_time = NOW() WHERE id = ?');
    $stmt->bind_param('i', $logId);
    $stmt->execute();
}

session_unset();
session_destroy();

header('Location: user_login.php');
exit;

