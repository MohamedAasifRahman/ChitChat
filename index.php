<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Welcome — ChitChat</title>


  <link rel="stylesheet" href="css/style.css">
</head>


<body>
  <?php include __DIR__ . '/partials/nav_public.php'; ?>
  <div class="container">
    <div class="card">
      <h1>ChitChat</h1>
      <br>
      <a class="btn" href="register.php">Create account</a>
      <a class="btn btn-secondary" href="login.php">Log in</a>
    </div>
  </div>
</body>

</html>