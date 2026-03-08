<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sms_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

if ($username === '' || $password === '' || $password_confirm === '') {
    header('Location: signup.php?error=' . urlencode('All fields are required.'));
    exit;
}

if (strlen($password) < 6) {
    header('Location: signup.php?error=' . urlencode('Password must be at least 6 characters.'));
    exit;
}

if ($password !== $password_confirm) {
    header('Location: signup.php?error=' . urlencode('Passwords do not match.'));
    exit;
}

// Check if username is already taken
$checkStmt = $mysqli->prepare('SELECT id FROM site_users WHERE username = ? LIMIT 1');
$checkStmt->bind_param('s', $username);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->fetch_assoc()) {
    header('Location: signup.php?error=' . urlencode('Username is already taken. Please choose another.'));
    exit;
}

// Create user
$password_hash = password_hash($password, PASSWORD_BCRYPT);
$insertStmt = $mysqli->prepare('INSERT INTO site_users (username, password_hash) VALUES (?, ?)');
$insertStmt->bind_param('ss', $username, $password_hash);

if (!$insertStmt->execute()) {
    header('Location: signup.php?error=' . urlencode('Could not create account. Please try again.'));
    exit;
}

$user_id = $mysqli->insert_id;

// Auto-login the new user and record login
$_SESSION['user_id'] = $user_id;
$_SESSION['user_username'] = $username;

$logStmt = $mysqli->prepare('INSERT INTO user_logins (user_id) VALUES (?)');
$logStmt->bind_param('i', $user_id);
$logStmt->execute();
$_SESSION['user_login_log_id'] = $mysqli->insert_id;

// SMS notification for new signup / enrolment
sendSmsNotification('New user signup: ' . $username);

header('Location: index.php');
exit;

