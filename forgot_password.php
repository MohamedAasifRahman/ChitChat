<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$info = null;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) $errors[] = 'Invalid CSRF token.';
    $email = strtolower(trim($_POST['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))');
            $stmt->execute([$user['id'], $token]);
            $info = 'Password reset link (valid 30 min): ' . e("http://localhost/chitchat/reset_password.php?token=$token");
        } else {
            $info = 'If that email exists, a reset link will be sent.';
        }
    }
}
$token = csrf_token();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Forgot password — chitchat</title>
</head>

<body>
    <div class="container">
        <h2>Reset your password</h2>
        <?php if ($errors): ?><div class="alert error"><?php echo e(implode('<br>', $errors)); ?></div><?php endif; ?>
        <?php if ($info): ?><div class="alert"><?php echo $info; ?></div><?php endif; ?>
        <form method="post" class="card form">
            <input type="hidden" name="csrf" value="<?php echo e($token); ?>">
            <label>Registered email<input type="email" name="email" required></label>
            <button class="btn" type="submit">Send reset link</button>
        </form>
    </div>
</body>

</html>