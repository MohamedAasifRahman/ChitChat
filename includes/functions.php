<?php
function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
function redirect($path)
{
    header('Location: ' . $path);
    exit;
}
function is_logged_in()
{
    return !empty($_SESSION['user_id']);
}

function current_user(PDO $pdo)
{
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare('SELECT id, full_name, email, profile_pic FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_check($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function handle_image_upload($field, $destDir, $maxSize = 2097152)
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;

    if (!is_dir($destDir)) {
        if (!mkdir($destDir, 0777, true) && !is_dir($destDir)) {
            throw new RuntimeException('Upload folder is missing and could not be created.');
        }
    }

    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload error.');
    if ($f['size'] > $maxSize) throw new RuntimeException('File too large (max ' . round($maxSize / 1048576, 1) . 'MB).');

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($f['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Invalid image type.');

    $ext = $allowed[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($f['tmp_name'], $dest)) throw new RuntimeException('Failed to move uploaded file.');
    return $filename;
}
