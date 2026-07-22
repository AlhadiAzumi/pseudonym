<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

$user = currentUser();
if (!$user) {
    jsonResponse(['success' => false, 'error' => 'Kamu harus login untuk membalas pesan.'], 401);
}

$input = jsonInput();
$messageId = (int)($input['message_id'] ?? 0);
$content = trim($input['content'] ?? '');

if ($messageId <= 0 || $content === '') {
    jsonResponse(['success' => false, 'error' => 'Data tidak lengkap.'], 400);
}

if (mb_strlen($content) > MAX_REPLY_LENGTH) {
    jsonResponse(['success' => false, 'error' => 'Balasan terlalu panjang (maks ' . MAX_REPLY_LENGTH . ' karakter).'], 400);
}

$db = getDB();

// Pastikan pesan itu memang ditujukan ke user yang sedang login
$stmt = $db->prepare('SELECT * FROM messages WHERE id = ? AND receiver_id = ?');
$stmt->execute([$messageId, $user['id']]);
$message = $stmt->fetch();

if (!$message) {
    jsonResponse(['success' => false, 'error' => 'Pesan tidak ditemukan atau bukan milikmu.'], 403);
}

// Cek sudah pernah dibalas belum
$stmt = $db->prepare('SELECT id FROM replies WHERE message_id = ?');
$stmt->execute([$messageId]);
if ($stmt->fetch()) {
    jsonResponse(['success' => false, 'error' => 'Pesan ini sudah dibalas sebelumnya.'], 409);
}

$stmt = $db->prepare('INSERT INTO replies (message_id, content, created_at) VALUES (?, ?, NOW())');
$stmt->execute([$messageId, $content]);

// Ambil data balasan yang baru dibuat
$replyId = $db->lastInsertId();
$stmt = $db->prepare('SELECT content, created_at FROM replies WHERE id = ?');
$stmt->execute([$replyId]);
$reply = $stmt->fetch();

jsonResponse([
    'success' => true,
    'reply' => [
        'content' => $reply['content'],
        'created_at' => timeAgo($reply['created_at']),
    ],
]);