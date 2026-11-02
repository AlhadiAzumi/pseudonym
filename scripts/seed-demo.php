<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/env.php';

loadEnv(dirname(__DIR__) . '/.env');
require dirname(__DIR__) . '/app/Helpers/helpers.php';

$options = getopt('', ['username:', 'confirm']);
$username = isset($options['username']) && is_string($options['username'])
    ? trim($options['username'])
    : '';
$appEnvironment = strtolower((string) env('APP_ENV', 'production'));

if (!in_array($appEnvironment, ['local', 'development', 'testing'], true)) {
    fwrite(STDERR, "Seeder hanya boleh dijalankan pada APP_ENV=local, development, atau testing.\n");
    exit(1);
}

if (env('ALLOW_DUMMY_SEEDING') !== true && env('ALLOW_DUMMY_SEEDING') !== 'true') {
    fwrite(STDERR, "Aktifkan ALLOW_DUMMY_SEEDING=true di .env untuk mengizinkan seeding.\n");
    exit(1);
}

if (!isset($options['confirm'])) {
    fwrite(STDERR, "Operasi ini menambah data ke database dari .env. Jalankan ulang dengan --confirm jika yakin.\n");
    exit(1);
}

if (!isValidUsername($username)) {
    fwrite(STDERR, "Gunakan --username=<username-valid> (3-20 huruf, angka, underscore).\n");
    exit(1);
}

$db = getDB();
$db->beginTransaction();

try {
    $userQuery = $db->prepare('SELECT id FROM users WHERE username = ?');
    $userQuery->execute([$username]);
    $receiverId = $userQuery->fetchColumn();

    if ($receiverId === false) {
        throw new RuntimeException("Username \"{$username}\" tidak ditemukan.");
    }

    $receiverId = (int) $receiverId;
    $messages = [
        [
            'key' => 'appreciation',
            'content' => 'Aku cuma mau bilang, kehadiranmu bikin hari-hari di sekitarmu terasa lebih baik. Tetap jadi dirimu sendiri, ya!',
            'hidden' => false,
            'reply' => 'Makasih banyak, pesan ini bikin hariku ikut lebih baik.',
        ],
        [
            'key' => 'encouragement',
            'content' => 'Kalau akhir-akhir ini terasa berat, jangan lupa kamu sudah melewati banyak hal. Pelan-pelan juga tetap maju.',
            'hidden' => true,
            'reply' => null,
        ],
        [
            'key' => 'memory',
            'content' => 'Masih ingat waktu kita ngobrol sampai lupa waktu? Semoga kapan-kapan bisa mengulang momen sederhana seperti itu.',
            'hidden' => true,
            'reply' => null,
        ],
        [
            'key' => 'feedback',
            'content' => 'Karyamu keren! Aku suka caramu memperhatikan detail kecil yang sering dilewatkan orang lain.',
            'hidden' => false,
            'reply' => null,
        ],
        [
            'key' => 'greeting',
            'content' => 'Semoga minggu ini ada kabar baik dan waktu istirahat yang cukup buat kamu. Jangan lupa jaga diri!',
            'hidden' => true,
            'reply' => null,
        ],
    ];

    $seedHashes = array_map(
        static fn(array $message): string => hash('sha256', 'pseudonym-demo-seeder:v1:' . $message['key']),
        $messages
    );
    $placeholders = implode(', ', array_fill(0, count($seedHashes), '?'));

    $deleteSeedMessages = $db->prepare(
        "DELETE FROM messages WHERE receiver_id = ? AND sender_hash IN ({$placeholders})"
    );
    $deleteSeedMessages->execute([$receiverId, ...$seedHashes]);

    $insertMessage = $db->prepare(
        'INSERT INTO messages (receiver_id, content, sender_hash, is_hidden)
         VALUES (?, ?, ?, ?) RETURNING id'
    );
    $insertReply = $db->prepare('INSERT INTO replies (message_id, content) VALUES (?, ?)');

    foreach ($messages as $message) {
        $senderHash = hash('sha256', 'pseudonym-demo-seeder:v1:' . $message['key']);
        $insertMessage->execute([
            $receiverId,
            $message['content'],
            $senderHash,
            $message['hidden'] ? 'true' : 'false',
        ]);
        $messageId = (int) $insertMessage->fetchColumn();

        if ($message['reply'] !== null) {
            $insertReply->execute([$messageId, $message['reply']]);
        }
    }

    $db->commit();
    fwrite(STDOUT, "Berhasil menambahkan 5 pesan contoh ke Kotak Masuk @{$username}.\n");
    fwrite(STDOUT, "Data seeder sebelumnya untuk username ini diperbarui; pesan lainnya tidak diubah.\n");
} catch (Throwable $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    fwrite(STDERR, 'Seeder gagal: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
