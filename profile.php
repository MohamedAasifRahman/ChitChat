<?php
require_once __DIR__ . '/includes/auth.php';
$user = current_user($pdo);
$errors = [];
$ok = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) $errors[] = 'Invalid request.';
  $display = trim($_POST['full_name'] ?? $user['full_name']);
  $newPic = null;
  if (!$errors) {
    try {
      $newPic = handle_image_upload('profile_pic', __DIR__ . '/uploads/profile', 10 * 1024 * 1024);
    } catch (RuntimeException $e) {
      if ($e->getMessage() !== 'Upload error.') $errors[] = $e->getMessage();
    }
  }
  if (!$errors) {
    if ($newPic) {
      $stmt = $pdo->prepare('UPDATE users SET full_name=?, profile_pic=? WHERE id=?');
      $stmt->execute([$display, $newPic, $user['id']]);
      $user['profile_pic'] = $newPic;
    } else {
      $stmt = $pdo->prepare('UPDATE users SET full_name=? WHERE id=?');
      $stmt->execute([$display, $user['id']]);
    }
    $user['full_name'] = $display;
    $ok = 'Profile updated.';
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Profile — ChitChat</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php include __DIR__ . '/partials/nav_authed.php'; ?>
  <div class="container">
    <div class="card">
      <div class="usercard">
        <img class="avatar" src="<?php echo $user['profile_pic'] ? 'uploads/profile/' . e($user['profile_pic']) : 'css/avatar.png'; ?>" alt="">
        <div>
          <h1><?php echo e($user['full_name']); ?></h1>
          <div class="small"><?php echo e($user['email']); ?></div>
        </div>
      </div>

      <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $m) echo '<p>' . e($m) . '</p>'; ?></div>
      <?php elseif ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>

      <form action="profile.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <label>Display name</label>
        <input type="text" name="full_name" value="<?php echo e($user['full_name']); ?>" required>
        <label>Change profile picture (max 10MB)</label>
        <input type="file" name="profile_pic" accept="image/*">
        <button class="btn" type="submit">Save changes</button>
      </form>
    </div>
  </div>
</body>

</html>