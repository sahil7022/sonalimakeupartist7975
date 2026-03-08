<?php
session_start();

// If already logged in as site user, go to homepage
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Sonali Makeup Artist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-page">
        <form action="user_auth.php" method="post" class="login-card" autocomplete="on">
            <h1 class="login-title">Welcome back</h1>

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
                    placeholder="Your username"
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
                <a href="signup.php" class="login-link">Sign up</a>
            </div>

            <p class="auth-hint">
                Demo user: <strong>demo</strong> / <strong>demo123</strong><br>
                Admin? <strong><a href="login.php">Go to admin login</a></strong>
            </p>
        </form>
    </div>
</body>
</html>

