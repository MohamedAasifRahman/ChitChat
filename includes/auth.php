<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
if (!is_logged_in()) redirect('login.php');
