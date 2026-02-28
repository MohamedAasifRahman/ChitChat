<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header class="site-header">
  <nav class="nav">
    <a class="brand" href="dashboard.php">ChitChat</a>
    <div class="nav-links">
      <a href="messages.php">Messages</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Log out</a>
    </div>
  </nav>
</header>