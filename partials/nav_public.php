<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header class="site-header">
  <nav class="nav">
    <a class="brand" href="index.php">ChitChat</a>
    <div class="nav-links">
      <a href="register.php">Create account</a>
      <a href="login.php">Log in</a>
    </div>
  </nav>
</header>