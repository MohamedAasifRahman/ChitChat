<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$full_name = $_POST['full_name'] ?? '';
$email     = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) $errors[] = 'Invalid request. Please try again.';

    if (trim($full_name) === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($_POST['password']) || strlen($_POST['password']) < 6) $errors[] = 'Password must be at least 6 characters.';
    if (($_POST['password'] ?? '') !== ($_POST['confirm_password'] ?? '')) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = 'Email is already registered.';
    }

    $profile_pic = null;
    if (!$errors) {
        try {
            $profile_pic = handle_image_upload('profile_pic', __DIR__ . '/uploads/profile', 10 * 1024 * 1024);
        } catch (RuntimeException $e) {
            if ($e->getMessage() !== 'Upload error.') $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, profile_pic, created_at) VALUES (?,?,?,?,NOW())');
        $stmt->execute([$full_name, $email, $hash, $profile_pic]);
        redirect('login.php?registered=1');
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Register — ChitChat</title>


    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include __DIR__ . '/partials/nav_public.php'; ?>
    <div class="container">
        <h1>Create account — ChitChat</h1>
        <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $m) echo '<p>' . e($m) . '</p>'; ?></div><?php endif; ?>
        <form action="register.php" method="post" enctype="multipart/form-data" class="card">
            <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
            <label>Full name</label><input type="text" name="full_name" value="<?php echo e($full_name); ?>" required>
            <label>Email</label><input type="email" name="email" value="<?php echo e($email); ?>" required>
            <label>Password</label><input type="password" name="password" required>
            <label>Confirm password</label><input type="password" name="confirm_password" required>
            <label for="profile_pic">Profile picture (optional, max 10MB)</label>
            <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
            <button type="submit" class="btn">Sign up</button>
        </form>
        <p>Already have an account? <a class="link" href="login.php">Log in</a></p>
    </div>
</body>

</html>