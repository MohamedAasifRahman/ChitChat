<?php
require_once __DIR__ . '/includes/auth.php';
$uid = (int)($_GET['u'] ?? 0);
$stmt = $pdo->prepare('SELECT id, full_name, email, profile_pic FROM users WHERE id=?');
$stmt->execute([$uid]);
$u = $stmt->fetch();
if (!$u) redirect('dashboard.php');
$posts = $pdo->prepare('SELECT id, body, image_path, created_at FROM posts WHERE user_id=? ORDER BY created_at DESC');
$posts->execute([$uid]);
$posts = $posts->fetchAll();
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><title><?php echo e($u['full_name']); ?> — SocialApp</title>
<link rel="stylesheet" href="css/style.css">
</head><body>
<?php include __DIR__ . '/partials/nav_authed.php'; ?>
<div class="container">
  <div class="card">
    <div class="usercard">
      <img class="avatar" src="<?php echo $u['profile_pic'] ? 'uploads/profile/'.e($u['profile_pic']) : 'css/avatar.png'; ?>" alt="">
      <div>
        <h2><?php echo e($u['full_name']); ?></h2>
        <div class="small"><?php echo e($u['email']); ?></div>
      </div>
    </div>
  </div>
  <div class="card">
    <h3><?php echo e($u['full_name']); ?>'s posts</h3>
    <?php foreach ($posts as $p): ?>
      <div class="post">
        <div class="meta"><?php echo e($p['created_at']); ?></div>
        <?php if ($p['body']): ?><p><?php echo nl2br(e($p['body'])); ?></p><?php endif; ?>
        <?php if ($p['image_path']): ?><img src="uploads/posts/<?php echo e($p['image_path']); ?>" alt=""><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div></body></html>
