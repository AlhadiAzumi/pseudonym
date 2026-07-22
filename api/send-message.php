<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

$input = jsonInput();
$username = trim($input['username'] ?? '');
$content = trim($input['content'] ?? '');

if ($username === '' || $content === '') {
    jsonResponse(['success' => false, 'error' => 'Username dan isi pesan wajib diisi.'], 400);
}

if (mb_strlen($content) > MAX_MESSAGE_LENGTH) {
    jsonResponse(['success' => false, 'error' => 'Pesan terlalu panjang (maks ' . MAX_MESSAGE_LENGTH . ' karakter).'], 400);
}

$db = getDB();
$stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$receiver = $stmt->fetch();

if (!$receiver) {
    jsonResponse(['success' => false, 'error' => 'Akun tujuan tidak ditemukan.'], 404);
}

// Anti-spam ringan
$fingerprint = senderFingerprint();
$stmt = $db->prepare(
    'SELECT COUNT(*) as total FROM messages
     WHERE receiver_id = ? AND sender_hash = ? AND created_at >= NOW() - INTERVAL 1 DAY'
);
$stmt->execute([$receiver['id'], $fingerprint]);
if ((int)$stmt->fetch()['total'] >= 20) {
    jsonResponse(['success' => false, 'error' => 'Kamu sudah mengirim cukup banyak pesan ke akun ini hari ini. Coba lagi besok.'], 429);
}

$stmt = $db->prepare('INSERT INTO messages (receiver_id, content, sender_hash, created_at, is_hidden) VALUES (?, ?, ?, NOW(), 0)');
$stmt->execute([$receiver['id'], $content, $fingerprint]);
$messageId = $db->lastInsertId();

jsonResponse([
    'success' => true,
    'message' => [
        'id' => (int)$messageId,
        'content' => $content,
        'created_at' => 'baru saja',
    ],
]);