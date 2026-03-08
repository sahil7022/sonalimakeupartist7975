<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sms_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$preferred_service = trim($_POST['preferred_service'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '') {
    header('Location: index.php#book');
    exit;
}

$stmt = $mysqli->prepare('INSERT INTO users (name, email, phone, preferred_service, message) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $name, $email, $phone, $preferred_service, $message);
$stmt->execute();

// SMS notification for new enquiry / enrolment
$summary = $preferred_service !== '' ? $preferred_service : 'General enquiry';
sendSmsNotification('New enquiry: ' . $name . ' - ' . $summary);

header('Location: index.php#book');
exit;

