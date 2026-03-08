<?php
session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · Sonali Makeup Artist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-page">
        <form action="authenticate.php" method="post" class="login-card" autocomplete="on">
            <h1 class="login-title">Admin Login</h1>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="login-field">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Admin username"
                    required
                />
            </div>

            <div class="login-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Password"
                    required
                />
            </div>

            <div class="login-actions">
                <button type="submit" class="login-button">Login</button>
                <a href="index.php" class="login-link">Back to site</a>
            </div>

            <p class="auth-hint">Default admin: <strong>sahil</strong> / <strong>2005</strong></p>
        </form>
    </div>
</body>
</html>

