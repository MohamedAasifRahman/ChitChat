<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) $errors[] = 'Invalid CSRF token.';
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) $errors[] = 'Invalid credentials.';
    else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    }
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$token = csrf_token();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Log in — ChitChat</title>

</head>

<body>
    <div class="container">
        <h2>Welcome back</h2>
        <?php if ($flash): ?><div class="alert"><?php echo e($flash); ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="alert error"><?php echo e(implode('<br>', $errors)); ?></div><?php endif; ?>
        <form method="post" class="card form">
            <input type="hidden" name="csrf" value="<?php echo e($token); ?>">
            <label>Email<input type="email" name="email" required></label>
            <label>Password<input type="password" name="password" required></label>
            <button class="btn" type="submit">Log in</button>
            <p><a href="forgot_password.php">Forgot password?</a></p>
        </form>
        <p>New here? <a href="register.php">Create an account</a></p>
    </div>
</body>

</html>