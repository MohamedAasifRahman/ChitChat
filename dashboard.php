<?php
require_once __DIR__ . '/includes/auth.php';
$user = current_user($pdo);
$errors = [];
$ok = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'new_post') {
  if (!csrf_check($_POST['csrf'] ?? '')) $errors[] = 'Invalid request.';
  $body = trim($_POST['body'] ?? '');
  if ($body === '' && (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE)) {
    $errors[] = 'Write something or attach an image.';
  }
  $image_path = null;
  if (!$errors) {
    try {
      $image_path = handle_image_upload('image', __DIR__ . '/uploads/posts', 10 * 1024 * 1024);
    } catch (RuntimeException $e) {
      if ($e->getMessage() !== 'Upload error.') $errors[] = $e->getMessage();
    }
  }
  if (!$errors) {
    $stmt = $pdo->prepare('INSERT INTO posts (user_id, body, image_path, created_at) VALUES (?,?,?,NOW())');
    $stmt->execute([$user['id'], $body, $image_path]);
    $ok = 'Posted!';
  }
}

$stmt = $pdo->query('SELECT p.id, p.body, p.image_path, p.created_at, u.full_name, u.profile_pic, u.id AS uid
                     FROM posts p JOIN users u ON p.user_id = u.id
                     ORDER BY p.created_at DESC LIMIT 50');
$posts = $stmt->fetchAll();
$ulist = $pdo->query('SELECT id, full_name, email, profile_pic FROM users ORDER BY created_at DESC LIMIT 12')->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Dashboard — ChitChat</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php include __DIR__ . '/partials/nav_authed.php'; ?>
  <div class="container">
    <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $m) echo '<p>' . e($m) . '</p>'; ?></div>
    <?php elseif ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>

    <div class="grid">
      <div>
        <div class="card">
          <form action="dashboard.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
            <input type="hidden" name="action" value="new_post">
            <label>What's happening?</label>
            <textarea name="body" placeholder="Share something..."></textarea>
            <label>Attach image (optional, max 10MB)</label>
            <input type="file" name="image" accept="image/*">
            <button class="btn" type="submit">Post</button>
          </form>
        </div>

        <div class="card">
          <h3>Timeline</h3>
          <?php foreach ($posts as $p): ?>
            <div class="post">
              <div class="usercard">
                <img class="avatar" src="<?php echo $p['profile_pic'] ? 'uploads/profile/' . e($p['profile_pic']) : 'css/avatar.png'; ?>" alt="">
                <div>
                  <div><a class="link" href="user.php?u=<?php echo (int)$p['uid']; ?>"><?php echo e($p['full_name']); ?></a></div>
                  <div class="meta"><?php echo e($p['created_at']); ?></div>
                </div>
              </div>
              <?php if ($p['body']): ?><p><?php echo nl2br(e($p['body'])); ?></p><?php endif; ?>
              <?php if ($p['image_path']): ?><img src="uploads/posts/<?php echo e($p['image_path']); ?>" alt=""><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <aside>
        <div class="card">
          <h3>People</h3>
          <?php foreach ($ulist as $u): ?>
            <div class="usercard">
              <img class="avatar" src="<?php echo $u['profile_pic'] ? 'uploads/profile/' . e($u['profile_pic']) : 'css/avatar.png'; ?>" alt="">
              <div>
                <div><a class="link" href="user.php?u=<?php echo (int)$u['id']; ?>"><?php echo e($u['full_name']); ?></a></div>
                <div class="small"><?php echo e($u['email']); ?></div>
                <a class="small link" href="messages.php?to=<?php echo (int)$u['id']; ?>">Message</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </aside>
    </div>
  </div>
</body>

</html>