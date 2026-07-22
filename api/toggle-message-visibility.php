<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

$user = currentUser();
if (!$user) {
    jsonResponse(['success' => false, 'error' => 'Kamu harus login untuk mengubah visibilitas pesan.'], 401);
}

$input = jsonInput();
$messageId = (int)($input['message_id'] ?? 0);
$hidden = (bool)($input['hidden'] ?? false);

if ($messageId <= 0) {
    jsonResponse(['success' => false, 'error' => 'Data tidak lengkap.'], 400);
}

$db = getDB();
$stmt = $db->prepare('SELECT id, receiver_id FROM messages WHERE id = ?');
$stmt->execute([$messageId]);
$message = $stmt->fetch();

if (!$message) {
    jsonResponse(['success' => false, 'error' => 'Pesan tidak ditemukan.'], 404);
}

if ((int)$message['receiver_id'] !== (int)$user['id']) {
    jsonResponse(['success' => false, 'error' => 'Kamu tidak memiliki akses untuk mengubah pesan ini.'], 403);
}

$stmt = $db->prepare('UPDATE messages SET is_hidden = ? WHERE id = ?');
$stmt->execute([$hidden ? 1 : 0, $messageId]);

jsonResponse([
    'success' => true,
    'hidden' => $hidden,
]);