<?php
require_once __DIR__ . '/includes/auth.php';
$user = current_user($pdo);
$errors = [];
$ok = null;

$to = isset($_GET['to']) ? (int)$_GET['to'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) $errors[] = 'Invalid request.';
  $to = (int)($_POST['to'] ?? 0);
  $body = trim($_POST['body'] ?? '');
  if ($to <= 0 || $to === $user['id']) $errors[] = 'Choose a valid recipient.';
  if ($body === '') $errors[] = 'Write a message.';
  if (!$errors) {
    $stmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, body, created_at) VALUES (?,?,?,NOW())');
    $stmt->execute([$user['id'], $to, $body]);
    $ok = 'Sent.';
  }
}
$users = $pdo->query('SELECT id, full_name FROM users ORDER BY full_name')->fetchAll();
$conv = [];
if ($to) {
  $stmt = $pdo->prepare('SELECT m.*, s.full_name AS sname, r.full_name AS rname
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    JOIN users r ON m.receiver_id = r.id
    WHERE (m.sender_id=? AND m.receiver_id=?) OR (m.sender_id=? AND m.receiver_id=?)
    ORDER BY m.created_at ASC');
  $stmt->execute([$user['id'], $to, $to, $user['id']]);
  $conv = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Messages — ChitChat</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php include __DIR__ . '/partials/nav_authed.php'; ?>
  <div class="container">
    <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $m) echo '<p>' . e($m) . '</p>'; ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>

    <div class="card">
      <form action="messages.php" method="post">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <label>Send to</label>
        <select name="to" required>
          <option value="">-- Select user --</option>
          <?php foreach ($users as $u): if ($u['id'] == $user['id']) continue; ?>
            <option value="<?php echo (int)$u['id']; ?>" <?php echo $to == $u['id'] ? 'selected' : ''; ?>>
              <?php echo e($u['full_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label>Message</label>
        <textarea name="body" required></textarea>
        <button class="btn" type="submit">Send</button>
      </form>
    </div>

    <?php if ($to): ?>
      <div class="card">
        <h3>Conversation</h3>
        <?php foreach ($conv as $m): ?>
          <div class="post">
            <div class="meta"><strong><?php echo e($m['sname']); ?></strong> → <strong><?php echo e($m['rname']); ?></strong> • <?php echo e($m['created_at']); ?></div>
            <p><?php echo nl2br(e($m['body'])); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>