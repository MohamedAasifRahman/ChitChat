<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$token = $_GET['token'] ?? ''; $errors = []; $success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) $errors[] = 'Invalid CSRF token.';
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? ''; $confirm = $_POST['confirm'] ?? '';
    if (strlen($password) < 6) $errors[] = 'Password too short.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()');
        $stmt->execute([$token]); $row = $stmt->fetch();
        if (!$row) { $errors[] = 'Invalid or expired token.'; }
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->beginTransaction();
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $row['user_id']]);
            $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$row['user_id']]);
            $pdo->commit(); $success = 'Password updated. You may now <a href="login.php">log in</a>.';
        }
    }
}
$csrf = csrf_token();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="css/style.css"><title>Set new password — SocialApp</title></head>
<body><div class="container"><h2>Set a new password</h2>
<?php if ($errors): ?><div class="alert error"><?php echo e(implode('<br>', $errors)); ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert"><?php echo $success; ?></div><?php endif; ?>
<form method="post" class="card form">
<input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
<input type="hidden" name="token" value="<?php echo e($token); ?>">
<label>New password<input type="password" name="password" required minlength="6"></label>
<label>Confirm password<input type="password" name="confirm" required minlength="6"></label>
<button class="btn" type="submit">Update</button>
</form></div></body></html>
