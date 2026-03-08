<?php
// Database connection settings
$db_host = 'localhost';
$db_user = 'root';      // XAMPP default
$db_pass = '';          // XAMPP default (empty)
$db_name = 'sonali_makeup';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');
