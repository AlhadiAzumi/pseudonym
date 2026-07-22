<?php
require_once __DIR__ . '/../includes/functions.php';

$username = trim($_GET['u'] ?? '');

if (!isValidUsername($username)) {
    jsonResponse(['exists' => false]);
}

$stmt = getDB()->prepare('SELECT username, display_name, avatar_url FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    jsonResponse(['exists' => false]);
}

jsonResponse([
    'exists'       => true,
    'username'     => $user['username'],
    'display_name' => $user['display_name'] ?: $user['username'],
    'avatar_url'   => $user['avatar_url'],
]);