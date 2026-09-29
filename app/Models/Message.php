<?php

declare(strict_types=1);

namespace App\Models;

class Message
{
    public static function getInbox(int $userId): array
    {
        $stmt = getDB()->prepare(
            'SELECT
                m.id,
                m.content,
                m.created_at,
                m.is_hidden,
                r.content AS reply_content,
                r.created_at AS reply_created_at
            FROM messages m
            LEFT JOIN replies r ON r.message_id = m.id
            WHERE m.receiver_id = ?
            ORDER BY m.created_at DESC'
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public static function getPublicMessages(int $userId, bool $includeHidden = false): array
    {
        $sql = 'SELECT
                    m.id,
                    m.content,
                    m.created_at,
                    m.is_hidden,
                    r.content AS reply_content,
                    r.created_at AS reply_created_at
                FROM messages m
                LEFT JOIN replies r ON r.message_id = m.id
                WHERE m.receiver_id = ?';

        if (!$includeHidden) {
            $sql .= ' AND m.is_hidden = 0';
        }

        $sql .= ' ORDER BY m.created_at DESC';

        $stmt = getDB()->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = getDB()->prepare('SELECT * FROM messages WHERE id = ?');
        $stmt->execute([$id]);
        $msg = $stmt->fetch();

        return $msg ?: null;
    }

    public static function create(int $receiverId, string $content, string $senderHash): int
    {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO messages (receiver_id, content, sender_hash, created_at, is_hidden) VALUES (?, ?, ?, NOW(), 0)');
        $stmt->execute([$receiverId, $content, $senderHash]);

        return (int) $db->lastInsertId();
    }

    public static function toggleVisibility(int $id, bool $hidden, int $receiverId): bool
    {
        $stmt = getDB()->prepare('UPDATE messages SET is_hidden = ? WHERE id = ? AND receiver_id = ?');
        return $stmt->execute([$hidden ? 1 : 0, $id, $receiverId]);
    }

    public static function countTodayBySender(int $receiverId, string $senderHash): int
    {
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) as total FROM messages
             WHERE receiver_id = ? AND sender_hash = ? AND created_at >= NOW() - INTERVAL 1 DAY'
        );
        $stmt->execute([$receiverId, $senderHash]);
        $res = $stmt->fetch();

        return (int) ($res['total'] ?? 0);
    }

    public static function getStats(int $userId): array
    {
        $messages = self::getInbox($userId);
        $total = count($messages);
        $replied = count(array_filter($messages, fn($m) => $m['reply_content'] !== null));
        $waiting = $total - $replied;

        return [
            'total'    => $total,
            'replied'  => $replied,
            'waiting'  => $waiting,
            'messages' => $messages,
        ];
    }
}