<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
$q = trim($_GET['q'] ?? '');
if ($q === '') { echo json_encode([]); exit; }
$stmt = $pdo->prepare('SELECT id, full_name FROM users WHERE full_name LIKE CONCAT("%", ?, "%") OR email LIKE CONCAT("%", ?, "%") ORDER BY full_name LIMIT 10');
$stmt->execute([$q, $q]);
echo json_encode($stmt->fetchAll());
