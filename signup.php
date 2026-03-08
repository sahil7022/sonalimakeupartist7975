<?php
session_start();

// If already logged in as site user, go to homepage
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up · Sonali Makeup Artist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-page">
        <form action="user_register.php" method="post" class="login-card" autocomplete="on">
            <h1 class="login-title">Create an account</h1>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="login-field">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Choose a username"
                    required
                />
            </div>

            <div class="login-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    required
                    minlength="6"
                />
            </div>

            <div class="login-field">
                <label for="password_confirm">Confirm password</label>
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    placeholder="Repeat your password"
                    required
                    minlength="6"
                />
            </div>

            <div class="login-actions">
                <button type="submit" class="login-button">Sign up</button>
                <a href="user_login.php" class="login-link">Back to login</a>
            </div>

            <p class="auth-hint">Already have an account? <strong><a href="user_login.php">Log in</a></strong></p>
        </form>
    </div>
</body>
</html>

