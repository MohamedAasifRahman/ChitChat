<?php
require_once __DIR__ . '/includes/auth.php';
if (!csrf_check($_POST['csrf'] ?? '')) redirect('dashboard.php?err=csrf');
$body = trim($_POST['body'] ?? '');
$image_path = null;
try { $image_path = handle_image_upload('image', __DIR__ . '/uploads/posts', 10*1024*1024); } catch (RuntimeException $e) {}
if ($body === '' && !$image_path) redirect('dashboard.php?err=empty');
$stmt = $pdo->prepare('INSERT INTO posts (user_id, body, image_path, created_at) VALUES (?,?,?,NOW())');
$stmt->execute([$_SESSION['user_id'], $body, $image_path]);
redirect('dashboard.php');
