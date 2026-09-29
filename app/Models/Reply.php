<?php

declare(strict_types=1);

namespace App\Models;

class Reply
{
    public static function findByMessageId(int $messageId): ?array
    {
        $stmt = getDB()->prepare('SELECT * FROM replies WHERE message_id = ?');
        $stmt->execute([$messageId]);
        $reply = $stmt->fetch();

        return $reply ?: null;
    }

    public static function create(int $messageId, string $content): int
    {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO replies (message_id, content, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$messageId, $content]);

        return (int) $db->lastInsertId();
    }
}
